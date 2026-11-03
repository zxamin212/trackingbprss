<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BerkasKredit extends Model
{
    use HasFactory;

    public const JENIS_KREDIT = [
        'Kredit Modal Usaha',
        'Kredit Investasi',
        'Kredit Konsumtif',
    ];

    public const STATUS = [
        'diajukan', 'screening_data', 'slik', 'survey', 'komite',
        'realisasi', 'cair', 'pending', 'tolak', 'batal',
    ];

    protected $table = 'berkas_kredit';

    protected $fillable = [
        'nomor_berkas', 'nama_nasabah', 'jenis_kredit',
        'status_terkini', 'tanggal_masuk', 'tanggal_selesai',
        'kantor_id', 'user_id',
        'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
        'alamat_ktp', 'alamat_domisili', 'no_hp', 'pekerjaan_usaha',
        'plafon', 'file_dokumen', 'slo_id', 'sumber', 'keterangan',
    ];

    protected $casts = [
        'tanggal_masuk'   => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_lahir'   => 'date',
        'plafon'          => 'decimal:2',
    ];

    public function kantor()
    {
        return $this->belongsTo(Kantor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // CS yang input pertama kali
    }

    public function slo()
    {
        return $this->belongsTo(User::class, 'slo_id');
    }

    public function histories()
    {
        return $this->hasMany(BerkasKreditHistory::class)->orderBy('created_at');
    }

    /**
     * Panggil SEBELUM DB::transaction penyimpanan berkas.
     * Membuat baris penghitung hari ini (dimulai dari nomor tertinggi yang sudah ada)
     * di luar transaksi, supaya request serentak tidak saling mengunci.
     */
    public static function siapkanPenghitungHariIni(): void
    {
        $sekarang = now();

        $tertinggi = (int) substr(
            (string) static::where('nomor_berkas', 'like', $sekarang->format('Ymd') . '-%')->max('nomor_berkas'),
            -4
        );

        \DB::table('nomor_berkas_counter')->insertOrIgnore([
            'tanggal'  => $sekarang->toDateString(),
            'terakhir' => $tertinggi,
        ]);
    }

    /**
     * Panggil DI DALAM DB::transaction yang menyimpan berkas.
     * Baris penghitung dikunci sampai transaksi selesai, jadi CS lain menunggu giliran.
     */
    public static function generateNomorBerkas(): string
    {
        $sekarang = now();
        $tanggal  = $sekarang->toDateString();

        $kunci = fn () => \DB::table('nomor_berkas_counter')
            ->where('tanggal', $tanggal)
            ->lockForUpdate()
            ->value('terakhir');

        $terakhir = $kunci();

        if ($terakhir === null) {
            // Cadangan: baris belum ada (mis. pergantian hari tepat saat menyimpan)
            static::siapkanPenghitungHariIni();
            $terakhir = $kunci();
        }

        $berikut = (int) $terakhir + 1;

        \DB::table('nomor_berkas_counter')
            ->where('tanggal', $tanggal)
            ->update(['terakhir' => $berikut]);

        return $sekarang->format('Ymd') . '-' . str_pad($berikut, 4, '0', STR_PAD_LEFT);
    }
    public function getLamaHariAttribute(): int
    {
        $selesai = $this->tanggal_selesai ?? now();
        return (int) floor($this->tanggal_masuk->diffInDays($selesai, false));
    }

    /**
     * Update data + ganti dokumen dengan aman:
     * - file baru dihapus kalau penyimpanan gagal
     * - file lama baru dihapus SETELAH data berhasil tersimpan
     * $dalamTransaksi: kode tambahan yang ikut satu transaksi (mis. catat riwayat)
     */
    public function updateDenganDokumen(array $data, ?UploadedFile $file = null, ?callable $dalamTransaksi = null): void
    {
        $lama = $this->file_dokumen;
        $baru = null;

        try {
            DB::transaction(function () use (&$baru, $data, $file, $dalamTransaksi) {
                if ($file) {
                    $baru = $file->store('berkas/dokumen', 'public');
                    $data['file_dokumen'] = $baru;
                }

                $this->update($data);

                if ($dalamTransaksi) {
                    $dalamTransaksi($this);
                }
            });
        } catch (\Throwable $e) {
            if ($baru) {
                Storage::disk('public')->delete($baru);
            }
            throw $e;
        }

        if ($baru && $lama) {
            Storage::disk('public')->delete($lama);
        }
    }
}