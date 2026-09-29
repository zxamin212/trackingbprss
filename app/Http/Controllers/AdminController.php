<?php

namespace App\Http\Controllers;

use App\Models\BerkasKredit;
use App\Models\Kantor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\DurasiTahapService;
use Carbon\Carbon;
use Dompdf\Dompdf;
use App\Services\LaporanBerkasService;

use Maatwebsite\Excel\Facades\Excel;


class AdminController extends Controller
{
    public function dashboard()
    {
        $totalBerkas = BerkasKredit::count();
        $totalAktif  = BerkasKredit::whereNotIn('status_terkini', ['cair', 'batal', 'tolak'])->count();
        $totalCair   = BerkasKredit::where('status_terkini', 'cair')->count();
        $totalTolak  = BerkasKredit::where('status_terkini', 'tolak')->count();
        $totalBatal  = BerkasKredit::where('status_terkini', 'batal')->count();

        $lebihDari14Hari = BerkasKredit::whereNotIn('status_terkini', ['cair', 'batal', 'tolak'])
            ->where('tanggal_masuk', '<=', now()->subDays(14))
            ->count();

        $cairBulanIni = BerkasKredit::where('status_terkini', 'cair')
            ->whereMonth('tanggal_selesai', now()->month)
            ->whereYear('tanggal_selesai', now()->year)
            ->count();

        $rataRataHari = BerkasKredit::where('status_terkini', 'cair')
            ->whereNotNull('tanggal_selesai')
            ->selectRaw('AVG(DATEDIFF(tanggal_selesai, tanggal_masuk)) as rata')
            ->value('rata');

        $berkasTerbaru = BerkasKredit::with('kantor')->latest()->take(5)->get();

        $durasiPerTahap = DurasiTahapService::rataRata();

        return view('admin.dashboard', compact(
            'totalBerkas', 'totalAktif', 'totalCair', 'totalTolak', 'totalBatal',
            'lebihDari14Hari', 'cairBulanIni', 'rataRataHari', 'berkasTerbaru', 'durasiPerTahap'
        ));
    }
    public function berkasIndex(Request $request)
    {
        $berkas = BerkasKredit::with('kantor')
            ->when($request->filled('cari'), fn($q) => $q->where('nama_nasabah', 'like', '%' . $request->cari . '%'))
            ->when($request->filled('status'), fn($q) => $q->where('status_terkini', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.berkas.index', compact('berkas'));
    }

    public function berkasShow($id)
    {
        $berkas = BerkasKredit::with(['histories.user', 'kantor', 'user', 'slo'])->findOrFail($id);

        return view('admin.berkas.show', compact('berkas'));
    }

    public function berkasEdit($id)
    {
        $berkas = BerkasKredit::findOrFail($id);
        $kantors = Kantor::orderBy('nama_kantor')->get();
        $slos = User::where('role', 'slo')->orderBy('name')->get();

        $statusList = [
            'diajukan', 'screening_data', 'slik', 'survey', 'komite',
            'realisasi', 'cair', 'pending', 'tolak', 'batal',
        ];

        return view('admin.berkas.edit', compact('berkas', 'kantors', 'slos', 'statusList'));
    }

    public function berkasUpdate(Request $request, $id)
    {
        $berkas = BerkasKredit::findOrFail($id);

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
            'tanggal_selesai' => 'nullable|date',
            'status_terkini'  => 'required|string',
            'keterangan'      => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request, $berkas) {
            $statusLama = $berkas->status_terkini;
            $statusBaru = $validated['status_terkini'];

            if ($request->hasFile('file_dokumen')) {
                if ($berkas->file_dokumen && Storage::disk('public')->exists($berkas->file_dokumen)) {
                    Storage::disk('public')->delete($berkas->file_dokumen);
                }
                $validated['file_dokumen'] = $request->file('file_dokumen')->store('berkas/dokumen', 'public');
            } else {
                unset($validated['file_dokumen']);
            }

            $berkas->update($validated);

            // Kalau status diubah oleh admin, catat sebagai override di histori
            if ($statusLama !== $statusBaru) {
                $berkas->histories()->create([
                    'status'     => $statusBaru,
                    'keterangan' => '[Diubah paksa oleh Admin] dari status "' . $statusLama . '" ke "' . $statusBaru . '"'
                        . ($request->filled('keterangan_override') ? ': ' . $request->keterangan_override : ''),
                    'user_id'    => auth()->id(),
                ]);
            }
        });

        return redirect()->route('admin.berkas.show', $berkas->id)
            ->with('success', 'Berkas berhasil diperbarui oleh Admin.');
    }

    public function berkasDestroy($id)
    {
        $berkas = BerkasKredit::findOrFail($id);

        if ($berkas->file_dokumen && Storage::disk('public')->exists($berkas->file_dokumen)) {
            Storage::disk('public')->delete($berkas->file_dokumen);
        }

        $berkas->histories()->delete();
        $berkas->delete();

        return redirect()->route('admin.berkas.index')
            ->with('success', 'Berkas berhasil dihapus permanen.');
    }



    // ==================== KANTOR ====================

    public function kantorIndex()
    {
        $kantors = Kantor::withCount(['users' => function ($q) {}])
            ->orderBy('nama_kantor')
            ->paginate(20);

        // Hitung jumlah user & berkas per kantor manual (karena relasi belum tentu ada)
        $kantors->getCollection()->transform(function ($k) {
            $k->jumlah_user = User::where('kantor_id', $k->id)->count();
            $k->jumlah_berkas = BerkasKredit::where('kantor_id', $k->id)->count();
            return $k;
        });

        return view('admin.kantor.index', compact('kantors'));
    }

    public function kantorStore(Request $request)
    {
        $validated = $request->validate([
            'nama_kantor' => 'required|string|max:255|unique:kantor,nama_kantor',
            'jenis'       => 'required|in:pusat,cabang,kas',
            'area'        => 'nullable|in:barat,selatan,timur',
        ]);

        Kantor::create($validated);

        return back()->with('success', 'Kantor berhasil ditambahkan.');
    }

    public function kantorUpdate(Request $request, $id)
    {
        $kantor = Kantor::findOrFail($id);

        $validated = $request->validate([
            'nama_kantor' => 'required|string|max:255|unique:kantor,nama_kantor,' . $kantor->id,
            'jenis'       => 'required|in:pusat,cabang,kas',
            'area'        => 'nullable|in:barat,selatan,timur',
        ]);

        $kantor->update($validated);

        return back()->with('success', 'Kantor berhasil diperbarui.');
    }

    public function kantorDestroy($id)
    {
        $kantor = Kantor::findOrFail($id);

        if (BerkasKredit::where('kantor_id', $kantor->id)->exists()) {
            return back()->with('error', 'Kantor tidak bisa dihapus karena masih memiliki berkas terkait.');
        }

        if (User::where('kantor_id', $kantor->id)->exists()) {
            return back()->with('error', 'Kantor tidak bisa dihapus karena masih memiliki user terkait.');
        }

        $kantor->delete();

        return back()->with('success', 'Kantor berhasil dihapus.');
    }

    // ==================== USER ====================

    public function userIndex(Request $request)
    {
        $users = User::with('kantor')
            ->when($request->filled('role'), fn($q) => $q->where('role', $request->role))
            ->when($request->filled('cari'), fn($q) => $q->where('name', 'like', '%' . $request->cari . '%'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $kantors = Kantor::orderBy('nama_kantor')->get();

        return view('admin.user.index', compact('users', 'kantors'));
    }

    public function userStore(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'role'      => 'required|in:admin,cs,slo,admin_legal,direksi,area_manager,manager_bisnis',
            'kantor_id' => 'nullable|exists:kantor,id',
            'area'      => 'nullable|in:barat,selatan,timur',
        ]);

        User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => $validated['role'],
            'kantor_id' => $validated['kantor_id'] ?? null,
            'area'      => $validated['area'] ?? null,
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => 'nullable|string|min:6',
            'role'      => 'required|in:admin,cs,slo,admin_legal,direksi,area_manager,manager_bisnis',
            'kantor_id' => 'nullable|exists:kantor,id',
            'area'      => 'nullable|in:barat,selatan,timur',
        ]);

        $data = [
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'role'      => $validated['role'],
            'kantor_id' => $validated['kantor_id'] ?? null,
            'area'      => $validated['area'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function userDestroy($id)
    {
        if ((int) $id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        User::findOrFail($id)->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

        // ==================== LAPORAN ====================

    public function laporanIndex(Request $request)
    {
        return (new LaporanBerkasService())->tampilkan($request, [
            'judul'       => 'Laporan Berkas — Semua Kantor',
            'routeIndex'  => 'admin.laporan.index',
            'routeExport' => 'admin.laporan.export',
            'kantors'     => Kantor::orderBy('nama_kantor')->get(),
        ]);
    }

    public function exportProses(Request $request)
    {
        return (new LaporanBerkasService())->export($request, 'Semua Kantor');
    }

}