<?php

namespace App\Http\Controllers;

use App\Models\BerkasKredit;
use Illuminate\Http\Request;
use App\Services\DurasiTahapService;
use App\Models\Kantor;
use App\Services\LaporanBerkasService;

class ManagerBisnisController extends Controller
{
    public function dashboard()
    {
        $totalAktif = BerkasKredit::whereNotIn('status_terkini', ['cair', 'batal', 'tolak'])->count();
        $totalCair  = BerkasKredit::where('status_terkini', 'cair')->count();
        $totalTolak = BerkasKredit::where('status_terkini', 'tolak')->count();
        $totalBatal = BerkasKredit::where('status_terkini', 'batal')->count();

        $durasiPerTahap = DurasiTahapService::rataRata();

        return view('manager-bisnis.dashboard', compact(
            'totalAktif', 'totalCair', 'totalTolak', 'totalBatal', 'durasiPerTahap'
        ));
    }

    public function berkasIndex(Request $request)
    {
        $berkas = BerkasKredit::with('kantor')
            ->whereNotIn('status_terkini', ['cair', 'batal', 'tolak'])
            ->when($request->filled('cari'), fn($q) => $q->where('nama_nasabah', 'like', '%' . $request->cari . '%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('manager-bisnis.berkas.index', compact('berkas'));
    }

    public function berkasShow($id)
    {
        $berkas = BerkasKredit::with(['histories.user', 'kantor', 'user', 'slo'])->findOrFail($id);

        return view('manager-bisnis.berkas.show', compact('berkas'));
    }

    public function laporanIndex(Request $request)
    {
        return (new LaporanBerkasService())->tampilkan($request, [
            'judul'       => 'Laporan Berkas — Semua Kantor',
            'routeIndex'  => 'mb.laporan.index',
            'routeExport' => 'mb.laporan.export',
            'kantors'     => Kantor::orderBy('nama_kantor')->get(),
        ]);
    }

    public function laporanExport(Request $request)
    {
        return (new LaporanBerkasService())->export($request, 'Semua Kantor');
}
}