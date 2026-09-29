<?php

namespace App\Http\Controllers;

use App\Models\BerkasKredit;
use Illuminate\Http\Request;
use App\Services\LaporanBerkasService;

class SloController extends Controller
{
    public function dashboard()
    {
        $sloId = auth()->id();

        $totalAktif = BerkasKredit::where('slo_id', $sloId)
            ->whereNotIn('status_terkini', ['cair', 'batal', 'tolak'])
            ->count();

        $totalCair = BerkasKredit::where('slo_id', $sloId)->where('status_terkini', 'cair')->count();
        $totalTolak = BerkasKredit::where('slo_id', $sloId)->where('status_terkini', 'tolak')->count();
        $totalBatal = BerkasKredit::where('slo_id', $sloId)->where('status_terkini', 'batal')->count();

        return view('slo.dashboard', compact('totalAktif', 'totalCair', 'totalTolak', 'totalBatal'));
    }
    public function berkasIndex(Request $request)
    {
        $berkas = BerkasKredit::where('slo_id', auth()->id())
            ->whereNotIn('status_terkini', ['cair', 'batal', 'tolak'])
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

    private function updateStatus($id, string $status, string $keteranganDefault, ?string $tanggalSelesai = null)
    {
        $berkas = BerkasKredit::where('slo_id', auth()->id())->findOrFail($id);

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
        $berkas = BerkasKredit::where('slo_id', auth()->id())->findOrFail($id);

        if ($berkas->status_terkini !== 'pending') {
            return back()->with('error', 'Berkas ini tidak sedang pending.');
        }

        $statusSebelumnya = $berkas->histories()
            ->where('status', '!=', 'pending')
            ->reorder('id', 'desc')
            ->value('status') ?? 'diajukan';

        $berkas->histories()->create([
            'status'     => $statusSebelumnya,
            'keterangan' => $request->input('keterangan', 'Berkas dilanjutkan kembali dari pending'),
            'user_id'    => auth()->id(),
        ]);

        $berkas->update(['status_terkini' => $statusSebelumnya]);

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