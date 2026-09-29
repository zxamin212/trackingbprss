<?php

namespace App\Http\Controllers;

use App\Models\BerkasKredit;
use App\Models\Kantor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\LaporanBerkasService;


class CsController extends Controller
{
    public function dashboard()
    {
        $kantorId = auth()->user()->kantor_id;

        $berkasBerjalan = BerkasKredit::where('kantor_id', $kantorId)
            ->whereNotIn('status_terkini', ['cair', 'batal', 'tolak'])
            ->count();

        $totalBerkas = BerkasKredit::where('kantor_id', $kantorId)->count();

        $totalCair = BerkasKredit::where('kantor_id', $kantorId)
            ->where('status_terkini', 'cair')
            ->count();

        $totalTolak = BerkasKredit::where('kantor_id', $kantorId)
            ->where('status_terkini', 'tolak')
            ->count();

        $totalBatal = BerkasKredit::where('kantor_id', $kantorId)
            ->where('status_terkini', 'batal')
            ->count();

        $berkasTerbaru = BerkasKredit::where('kantor_id', $kantorId)
            ->latest()
            ->take(5)
            ->get();

        return view('cs.dashboard', compact(
            'berkasBerjalan', 'totalBerkas', 'totalCair', 'totalTolak', 'totalBatal', 'berkasTerbaru'
        ));
    }

    public function berkasIndex(Request $request)
    {
        $berkas = BerkasKredit::where('kantor_id', auth()->user()->kantor_id)
            ->whereNotIn('status_terkini', ['cair', 'batal', 'tolak'])
            ->when($request->filled('cari'), function ($query) use ($request) {
                $query->where('nama_nasabah', 'like', '%' . $request->cari . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('cs.berkas.index', compact('berkas'));
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
        $berkas = BerkasKredit::where('kantor_id', auth()->user()->kantor_id)
            ->where('status_terkini', $status)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $query->where('nama_nasabah', 'like', '%' . $request->cari . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('cs.laporan.index', compact('berkas', 'judul'));
    }

    public function berkasCreate()
    {
        $kantors = Kantor::orderBy('nama_kantor')->get();
        $slos = User::where('role', 'slo')->orderBy('name')->get();

        return view('cs.berkas.create', compact('kantors', 'slos'));
    }

    public function berkasStore(Request $request)
    {
        $validated = $request->validate([
            'nama_nasabah'    => 'required|string|max:255',
            'tempat_lahir'    => 'required|string|max:255',
            'tanggal_lahir'   => 'required|date',
            'jenis_kelamin'   => 'required|in:L,P',
            'alamat_ktp'      => 'required|string',
            'alamat_domisili' => 'nullable|string',
            'no_hp'           => 'required|string|max:20',
            'pekerjaan_usaha' => 'required|string|max:255',
            'jenis_kredit'    => 'required|string',
            'plafon'          => 'required|numeric|min:0',
            'file_dokumen'    => 'required|file|mimes:pdf|max:5120',
            'kantor_id'       => 'required|exists:kantor,id',
            'slo_id'          => 'required|exists:users,id',
            'sumber'          => 'required|in:langsung,marketing',
            'tanggal_masuk'   => 'required|date',
            'keterangan'      => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $path = $request->file('file_dokumen')->store('berkas/dokumen', 'public');

            $berkas = BerkasKredit::create([
                'nomor_berkas'    => BerkasKredit::generateNomorBerkas(),
                'nama_nasabah'    => $validated['nama_nasabah'],
                'tempat_lahir'    => $validated['tempat_lahir'],
                'tanggal_lahir'   => $validated['tanggal_lahir'],
                'jenis_kelamin'   => $validated['jenis_kelamin'],
                'alamat_ktp'      => $validated['alamat_ktp'],
                'alamat_domisili' => $validated['alamat_domisili'] ?: $validated['alamat_ktp'],
                'no_hp'           => $validated['no_hp'],
                'pekerjaan_usaha' => $validated['pekerjaan_usaha'],
                'jenis_kredit'    => $validated['jenis_kredit'],
                'plafon'          => $validated['plafon'],
                'file_dokumen'    => $path,
                'status_terkini'  => 'diajukan',
                'tanggal_masuk'   => $validated['tanggal_masuk'],
                'kantor_id'       => $validated['kantor_id'],
                'slo_id'          => $validated['slo_id'],
                'sumber'          => $validated['sumber'],
                'keterangan'      => $validated['keterangan'] ?? null,
                'user_id'         => auth()->id(),
            ]);

            $berkas->histories()->create([
                'status'     => 'diajukan',
                'keterangan' => $validated['keterangan']
                    ?? ('Berkas masuk via ' . ($validated['sumber'] === 'marketing' ? 'marketing (door to door)' : 'nasabah langsung')),
                'user_id'    => auth()->id(),
            ]);
        });

        return redirect()->route('cs.berkas.index')
            ->with('success', 'Berkas baru berhasil disimpan.');
    }

    public function berkasShow($id)
    {
        $berkas = BerkasKredit::with('histories.user')
            ->where('kantor_id', auth()->user()->kantor_id)
            ->findOrFail($id);

        return view('cs.berkas.show', compact('berkas'));
    }

    public function edit($id)
    {
        $berkas = BerkasKredit::where('kantor_id', auth()->user()->kantor_id)
            ->where('status_terkini', 'diajukan')
            ->findOrFail($id);

        $kantors = Kantor::orderBy('nama_kantor')->get();
        $slos = User::where('role', 'slo')->orderBy('name')->get();

        return view('cs.berkas.edit', compact('berkas', 'kantors', 'slos'));
    }

    public function update(Request $request, $id)
    {
        $berkas = BerkasKredit::where('kantor_id', auth()->user()->kantor_id)
            ->where('status_terkini', 'diajukan')
            ->findOrFail($id);

        $validated = $request->validate([
            'nama_nasabah'    => 'required|string|max:255',
            'tempat_lahir'    => 'required|string|max:255',
            'tanggal_lahir'   => 'required|date',
            'jenis_kelamin'   => 'required|in:L,P',
            'alamat_ktp'      => 'required|string',
            'alamat_domisili' => 'nullable|string',
            'no_hp'           => 'required|string|max:20',
            'pekerjaan_usaha' => 'required|string|max:255',
            'jenis_kredit'    => 'required|string',
            'plafon'          => 'required|numeric|min:0',
            'file_dokumen'    => 'nullable|file|mimes:pdf|max:5120',
            'kantor_id'       => 'required|exists:kantor,id',
            'slo_id'          => 'required|exists:users,id',
            'sumber'          => 'required|in:langsung,marketing',
            'tanggal_masuk'   => 'required|date',
            'keterangan'      => 'nullable|string',
        ]);

        if ($request->hasFile('file_dokumen')) {
            if ($berkas->file_dokumen && Storage::disk('public')->exists($berkas->file_dokumen)) {
                Storage::disk('public')->delete($berkas->file_dokumen);
            }
            $validated['file_dokumen'] = $request->file('file_dokumen')->store('berkas/dokumen', 'public');
        } else {
            unset($validated['file_dokumen']);
        }

        $berkas->update($validated);

        return redirect()->route('cs.berkas.show', $berkas->id)
            ->with('success', 'Berkas berhasil diperbarui.');
    }

    private function layananLaporan(): LaporanBerkasService
    {
        $kantorId = auth()->user()->kantor_id;

        return new LaporanBerkasService(fn($q) => $q->where('kantor_id', $kantorId));
    }

    public function laporanIndex(Request $request)
    {
        $kantor = auth()->user()->kantor->nama_kantor ?? 'Kantor Anda';

        return $this->layananLaporan()->tampilkan($request, [
            'judul'       => "Laporan Berkas — $kantor",
            'routeIndex'  => 'cs.laporan.index',
            'routeExport' => 'cs.laporan.export',
            'kantors'     => null,
        ]);
    }

    public function laporanExport(Request $request)
    {
        return $this->layananLaporan()->export($request, auth()->user()->kantor->nama_kantor ?? 'Kantor Anda');
    }
}