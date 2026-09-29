<?php

namespace App\Http\Controllers;

use App\Models\BerkasKredit;
use App\Models\Kantor;
use Illuminate\Http\Request;
use App\Services\LaporanBerkasService;


class AreaManagerController extends Controller
{
    private function kantorIdsInArea()
    {
        return Kantor::where('area', auth()->user()->area)->pluck('id');
    }

    public function dashboard()
    {
        $kantorIds = $this->kantorIdsInArea();

        $totalAktif = BerkasKredit::whereIn('kantor_id', $kantorIds)
            ->whereNotIn('status_terkini', ['cair', 'batal', 'tolak'])
            ->count();

        $totalCair = BerkasKredit::whereIn('kantor_id', $kantorIds)
            ->where('status_terkini', 'cair')
            ->count();

        $totalTolak = BerkasKredit::whereIn('kantor_id', $kantorIds)
            ->where('status_terkini', 'tolak')
            ->count();

        $totalBatal = BerkasKredit::whereIn('kantor_id', $kantorIds)
            ->where('status_terkini', 'batal')
            ->count();

        return view('area-manager.dashboard', compact('totalAktif', 'totalCair', 'totalTolak', 'totalBatal'));
    }

    public function berkasIndex(Request $request)
    {
        $kantorIds = $this->kantorIdsInArea();

        $berkas = BerkasKredit::with('kantor')
            ->whereIn('kantor_id', $kantorIds)
            ->whereNotIn('status_terkini', ['cair', 'batal', 'tolak'])
            ->when($request->filled('cari'), fn($q) => $q->where('nama_nasabah', 'like', '%' . $request->cari . '%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('area-manager.berkas.index', compact('berkas'));
    }

    public function berkasShow($id)
    {
        $kantorIds = $this->kantorIdsInArea();

        $berkas = BerkasKredit::with(['histories.user', 'kantor', 'user', 'slo'])
            ->whereIn('kantor_id', $kantorIds)
            ->findOrFail($id);

        return view('area-manager.berkas.show', compact('berkas'));
    }

    private function layananLaporan(): LaporanBerkasService
    {
        $kantorIds = $this->kantorIdsInArea();

        return new LaporanBerkasService(fn($q) => $q->whereIn('kantor_id', $kantorIds));
    }

    public function laporanIndex(Request $request)
    {
        $area = ucfirst(auth()->user()->area);

        return $this->layananLaporan()->tampilkan($request, [
            'judul'       => "Laporan Berkas — Wilayah $area",
            'routeIndex'  => 'am.laporan.index',
            'routeExport' => 'am.laporan.export',
            'kantors'     => Kantor::where('area', auth()->user()->area)->orderBy('nama_kantor')->get(),
        ]);
    }

    public function laporanExport(Request $request)
    {
        return $this->layananLaporan()->export($request, 'Wilayah ' . ucfirst(auth()->user()->area));
    }
}