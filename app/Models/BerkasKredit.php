<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BerkasKredit extends Model
{
    use HasFactory;

    protected $table = 'berkas_kredit';

    protected $fillable = [
        'nomor_berkas', 'nama_nasabah', 'jenis_kredit',
        'status_terkini', 'tanggal_masuk', 'tanggal_selesai',
        'kantor_id', 'user_id',

        // field tambahan
        'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
        'alamat_ktp', 'alamat_domisili', 'no_hp', 'pekerjaan_usaha',
        'plafon', 'file_dokumen', 'slo_id', 'sumber_berkas', 'keterangan',
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
        return $this->belongsTo(User::class, 'slo_id'); // SLO yang menangani
    }

    public function histories()
    {
        return $this->hasMany(BerkasKreditHistory::class)->orderBy('created_at');
    }

    public static function generateNomorBerkas(): string
    {
        $tanggal = now()->format('Ymd');

        $terakhir = static::where('nomor_berkas', 'like', $tanggal . '-%')
            ->lockForUpdate()
            ->orderByDesc('nomor_berkas')
            ->value('nomor_berkas');

        $urutan = $terakhir ? ((int) substr($terakhir, -4)) + 1 : 1;

        return $tanggal . '-' . str_pad($urutan, 4, '0', STR_PAD_LEFT);
    }

    public function getLamaHariAttribute(): int
    {
        $selesai = $this->tanggal_selesai ?? now();
        return (int) floor($this->tanggal_masuk->diffInDays($selesai, false));
    }
}