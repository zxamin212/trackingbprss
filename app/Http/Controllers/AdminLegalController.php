<?php

namespace App\Http\Controllers;

use App\Models\BerkasKredit;
use Illuminate\Http\Request;

class AdminLegalController extends Controller
{
    public function dashboard()
    {
        $siapAkad = BerkasKredit::where('status_terkini', 'realisasi')->count();

        $cairBulanIni = BerkasKredit::where('status_terkini', 'cair')
            ->whereMonth('tanggal_selesai', now()->month)
            ->whereYear('tanggal_selesai', now()->year)
            ->count();

        $totalCair = BerkasKredit::where('status_terkini', 'cair')->count();

        return view('legal.dashboard', compact('siapAkad', 'cairBulanIni', 'totalCair'));
    }

    public function berkasIndex(Request $request)
    {
        $status = $request->get('status', 'realisasi'); // default: siap akad

        $berkas = BerkasKredit::with('kantor')
            ->when($status !== 'semua', fn($q) => $q->where('status_terkini', $status))
            ->when($status === 'semua', fn($q) => $q->whereIn('status_terkini', ['realisasi', 'cair']))
            ->when($request->filled('cari'), fn($q) => $q->where('nama_nasabah', 'like', '%' . $request->cari . '%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('legal.berkas.index', compact('berkas', 'status'));
    }

    public function berkasShow($id)
    {
        $berkas = BerkasKredit::with(['histories.user', 'kantor', 'user', 'slo'])
            ->whereIn('status_terkini', ['realisasi', 'cair'])
            ->findOrFail($id);

        return view('legal.berkas.show', compact('berkas'));
    }
}