<?php

namespace App\Http\Controllers;

use App\Models\BerkasKredit;
use App\Services\LaporanBerkasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SloController extends Controller
{
    private const STATUS_FINAL = ['cair', 'tolak', 'batal'];

    // status tujuan => status asal yang diizinkan
    private const ALUR = [
        'screening_data' => ['diajukan'],
        'slik'           => ['screening_data'],
        'survey'         => ['slik'],
        'komite'         => ['survey'],
        'realisasi'      => ['komite'],
        'cair'           => ['realisasi'],
    ];

    public function dashboard()
    {
        $sloId = auth()->id();

        $totalAktif = BerkasKredit::where('slo_id', $sloId)
            ->whereNotIn('status_terkini', self::STATUS_FINAL)
            ->count();

        $totalCair  = BerkasKredit::where('slo_id', $sloId)->where('status_terkini', 'cair')->count();
        $totalTolak = BerkasKredit::where('slo_id', $sloId)->where('status_terkini', 'tolak')->count();
        $totalBatal = BerkasKredit::where('slo_id', $sloId)->where('status_terkini', 'batal')->count();

        return view('slo.dashboard', compact('totalAktif', 'totalCair', 'totalTolak', 'totalBatal'));
    }

    public function berkasIndex(Request $request)
    {
        $berkas = BerkasKredit::where('slo_id', auth()->id())
            ->whereNotIn('status_terkini', self::STATUS_FINAL)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $query->where('nama_nasabah', 'like', '%' . $request->cari . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('slo.berkas.index', compact('berkas'));
    }

    public function laporanCair(Request $request)
    {
        return $this->laporanByStatus($request, 'cair', 'Aplikasi Cair');
    }

    public function laporanBatal(Request $request)
    {
        return $this->laporanByStatus($request, 'batal', 'Aplikasi Batal');
    }

    public function laporanTolak(Request $request)
    {
        return $this->laporanByStatus($request, 'tolak', 'Aplikasi Tolak');
    }

    private function laporanByStatus(Request $request, string $status, string $judul)
    {
        $berkas = BerkasKredit::where('slo_id', auth()->id())
            ->where('status_terkini', $status)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $query->where('nama_nasabah', 'like', '%' . $request->cari . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('slo.laporan.index', compact('berkas', 'judul'));
    }

    public function berkasShow($id)
    {
        $berkas = BerkasKredit::with('histories.user')
            ->where('slo_id', auth()->id())
            ->findOrFail($id);

        return view('slo.berkas.show', compact('berkas'));
    }

    /**
     * Ubah status berkas dengan pengaman:
     * - baris dikunci selama transaksi (klik dobel / dua request bersamaan tidak lolos dua kali)
     * - status final tidak bisa diubah lagi
     * - tahap utama hanya boleh maju satu langkah dari tahap sebelumnya
     * - pending, tolak, batal boleh dari status non-final mana pun
     */
    private function updateStatus($id, string $status, string $keteranganDefault, ?string $tanggalSelesai = null)
    {
        return DB::transaction(function () use ($id, $status, $keteranganDefault, $tanggalSelesai) {
            $berkas = BerkasKredit::where('slo_id', auth()->id())
                ->lockForUpdate()
                ->findOrFail($id);

            $asal = $berkas->status_terkini;

            if (in_array($asal, self::STATUS_FINAL, true)) {
                throw ValidationException::withMessages([
                    'status' => "Berkas sudah berstatus {$asal} dan tidak bisa diubah lagi.",
                ]);
            }

            if ($asal === $status) {
                throw ValidationException::withMessages([
                    'status' => "Berkas sudah berstatus {$status}.",
                ]);
            }

            if (isset(self::ALUR[$status]) && !in_array($asal, self::ALUR[$status], true)) {
                throw ValidationException::withMessages([
                    'status' => "Berkas berstatus {$asal}, tidak bisa langsung dipindah ke {$status}.",
                ]);
            }

            $berkas->histories()->create([
                'status'     => $status,
                'keterangan' => request('keterangan', $keteranganDefault),
                'user_id'    => auth()->id(),
            ]);

            $data = ['status_terkini' => $status];
            if ($tanggalSelesai) {
                $data['tanggal_selesai'] = now();
            }
            $berkas->update($data);

            return $berkas;
        });
    }

    public function updateScreening(Request $request, $id)
    {
        $this->updateStatus($id, 'screening_data', 'Berkas masuk tahap screening data');
        return back()->with('success', 'Berkas masuk tahap Screening Data.');
    }

    public function updateSlik(Request $request, $id)
    {
        $this->updateStatus($id, 'slik', 'Berkas masuk tahap pengecekan SLIK');
        return back()->with('success', 'Berkas masuk tahap SLIK.');
    }

    public function updateSurvey(Request $request, $id)
    {
        $this->updateStatus($id, 'survey', 'Berkas masuk tahap survey');
        return back()->with('success', 'Berkas masuk tahap Survey.');
    }

    public function updateKomite(Request $request, $id)
    {
        $this->updateStatus($id, 'komite', 'Berkas dilanjutkan ke tahap komite');
        return back()->with('success', 'Berkas dilanjutkan ke tahap Komite.');
    }

    public function updateRealisasi(Request $request, $id)
    {
        $this->updateStatus($id, 'realisasi', 'Berkas disetujui komite, masuk tahap realisasi');
        return back()->with('success', 'Berkas masuk tahap Realisasi.');
    }

    public function updateCair(Request $request, $id)
    {
        $this->updateStatus($id, 'cair', 'Dana berhasil dicairkan', tanggalSelesai: now());
        return back()->with('success', 'Berkas ditandai Cair.');
    }

    public function pending(Request $request, $id)
    {
        $this->updateStatus($id, 'pending', 'Berkas ditahan sementara (pending)');
        return back()->with('success', 'Berkas ditandai Pending.');
    }

    public function tolak(Request $request, $id)
    {
        $this->updateStatus($id, 'tolak', 'Berkas ditolak', tanggalSelesai: now());
        return back()->with('success', 'Berkas ditolak.');
    }

    public function batalkan(Request $request, $id)
    {
        $this->updateStatus($id, 'batal', 'Berkas dibatalkan oleh SLO', tanggalSelesai: now());
        return back()->with('success', 'Berkas dibatalkan.');
    }

    public function resume(Request $request, $id)
    {
        $statusSebelumnya = DB::transaction(function () use ($request, $id) {
            $berkas = BerkasKredit::where('slo_id', auth()->id())
                ->lockForUpdate()
                ->findOrFail($id);

            if ($berkas->status_terkini !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Berkas ini tidak sedang pending.',
                ]);
            }

            $sebelumnya = $berkas->histories()
                ->where('status', '!=', 'pending')
                ->reorder('id', 'desc')
                ->value('status') ?? 'diajukan';

            $berkas->histories()->create([
                'status'     => $sebelumnya,
                'keterangan' => $request->input('keterangan', 'Berkas dilanjutkan kembali dari pending'),
                'user_id'    => auth()->id(),
            ]);

            $berkas->update(['status_terkini' => $sebelumnya]);

            return $sebelumnya;
        });

        return back()->with('success', 'Berkas dilanjutkan ke tahap ' . str_replace('_', ' ', $statusSebelumnya) . '.');
    }

    private function layananLaporan(): LaporanBerkasService
    {
        $sloId = auth()->id();

        return new LaporanBerkasService(fn($q) => $q->where('slo_id', $sloId));
    }

    public function laporanIndex(Request $request)
    {
        return $this->layananLaporan()->tampilkan($request, [
            'judul'       => 'Laporan Berkas yang Anda Tangani',
            'routeIndex'  => 'slo.laporan.index',
            'routeExport' => 'slo.laporan.export',
            'kantors'     => null,
        ]);
    }

    public function laporanExport(Request $request)
    {
        return $this->layananLaporan()->export($request, 'Berkas yang ditangani ' . auth()->user()->name);
    }
}