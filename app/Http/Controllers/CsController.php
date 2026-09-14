<?php

namespace App\Http\Controllers;

use App\Models\BerkasKredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CsController extends Controller
{
    public function dashboard()
    {
        $kantorId = auth()->user()->kantor_id;

        $berkasBerjalan = BerkasKredit::where('kantor_id', $kantorId)
            ->whereNull('tanggal_selesai')
            ->count();

        $perluDiverifikasi = BerkasKredit::where('kantor_id', $kantorId)
            ->where('status_terkini', 'diajukan')
            ->count();

        return view('cs.dashboard', compact('berkasBerjalan', 'perluDiverifikasi'));
    }

    public function berkasIndex()
    {
        $berkas = BerkasKredit::where('kantor_id', auth()->user()->kantor_id)
            ->latest()
            ->paginate(10);

        return view('cs.berkas.index', compact('berkas'));
    }

    public function berkasCreate()
    {
        return view('cs.berkas.create');
    }

    public function berkasStore(Request $request)
    {
        $validated = $request->validate([
            'nama_nasabah' => 'required|string|max:255',
            'jenis_kredit' => 'required|string',
            'sumber' => 'required|in:langsung,marketing',
            'tanggal_masuk' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $nomorBerkas = $this->generateNomorBerkas();

            $berkas = BerkasKredit::create([
                'nomor_berkas' => $nomorBerkas,
                'nama_nasabah' => $validated['nama_nasabah'],
                'jenis_kredit' => $validated['jenis_kredit'],
                'status_terkini' => 'diajukan',
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'kantor_id' => auth()->user()->kantor_id,
                'user_id' => auth()->id(),
            ]);

            $berkas->histories()->create([
                'status' => 'diajukan',
                'keterangan' => $validated['keterangan']
                    ?? ('Berkas masuk via ' . ($validated['sumber'] === 'marketing' ? 'marketing (door to door)' : 'nasabah langsung')),
                'user_id' => auth()->id(),
            ]);
        });

        return redirect()->route('cs.berkas.index')
            ->with('success', 'Berkas baru berhasil disimpan.');
    }

    public function berkasShow($id)
    {
        $berkas = BerkasKredit::with('histories.user')->findOrFail($id);

        return view('cs.berkas.show', compact('berkas'));
    }

    public function verifikasi(Request $request, $id)
    {
        $berkas = BerkasKredit::findOrFail($id);

        $berkas->histories()->create([
            'status' => 'verifikasi',
            'keterangan' => $request->input('keterangan'),
            'user_id' => auth()->id(),
        ]);

        $berkas->update(['status_terkini' => 'verifikasi']);

        return back()->with('success', 'Berkas ditandai selesai diverifikasi.');
    }

    public function batalkan(Request $request, $id)
    {
        $berkas = BerkasKredit::findOrFail($id);

        $berkas->histories()->create([
            'status' => 'dibatalkan',
            'keterangan' => $request->input('keterangan', 'Dibatalkan oleh CS'),
            'user_id' => auth()->id(),
        ]);

        $berkas->update([
            'status_terkini' => 'dibatalkan',
            'tanggal_selesai' => now(),
        ]);

        return back()->with('success', 'Berkas dibatalkan.');
    }

    /**
     * Generate nomor berkas otomatis, format: BK-{tahun}-{urutan}
     */
    private function generateNomorBerkas(): string
    {
        $tahun = date('Y');
        $terakhir = BerkasKredit::where('nomor_berkas', 'like', "BK-{$tahun}-%")
            ->orderByDesc('id')
            ->first();

        $urutan = $terakhir
            ? ((int) substr($terakhir->nomor_berkas, -4)) + 1
            : 1;

        return sprintf('BK-%s-%04d', $tahun, $urutan);
    }
}