<?php

namespace App\Services;

use App\Exports\LaporanBerkasExport;
use App\Models\BerkasKredit;
use App\Models\Kantor;
use Carbon\Carbon;
use Closure;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class LaporanBerkasService
{
    /**
     * $scope = pembatas data sesuai role, contoh: fn($q) => $q->where('kantor_id', 5).
     * null = semua data.
     */
    public function __construct(private ?Closure $scope = null)
    {
    }

    public function aturan(Request $request): array
    {
        return [
            'dari'      => 'nullable|date',
            'sampai'    => ['nullable', 'date', Rule::when($request->filled('dari'), 'after_or_equal:dari')],
            'kantor_id' => 'nullable|exists:kantor,id',
            'status'    => 'nullable|in:berjalan,diajukan,screening_data,slik,survey,komite,realisasi,pending,cair,tolak,batal',
        ];
    }

    public function query(Request $request)
    {
        $query = BerkasKredit::query();

        // Pembatas role selalu diterapkan lebih dulu, filter user hanya mempersempit
        if ($this->scope) {
            ($this->scope)($query);
        }

        return $query
            ->when($request->filled('dari'), fn($q) => $q->whereDate('tanggal_masuk', '>=', $request->dari))
            ->when($request->filled('sampai'), fn($q) => $q->whereDate('tanggal_masuk', '<=', $request->sampai))
            ->when($request->filled('kantor_id'), fn($q) => $q->where('kantor_id', $request->kantor_id))
            ->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'berjalan') {
                    $q->whereNotIn('status_terkini', ['cair', 'batal', 'tolak']);
                } else {
                    $q->where('status_terkini', $request->status);
                }
            });
    }

    public function ringkasan($query): array
    {
        $perStatus = (clone $query)
            ->selectRaw('status_terkini, COUNT(*) as jumlah')
            ->groupBy('status_terkini')
            ->pluck('jumlah', 'status_terkini');

        return [
            'total'     => $perStatus->sum(),
            'plafon'    => (clone $query)->sum('plafon'),
            'perStatus' => $perStatus,
        ];
    }

    public function label(Request $request): array
    {
        $dari   = $request->filled('dari') ? Carbon::parse($request->dari)->format('d-m-Y') : null;
        $sampai = $request->filled('sampai') ? Carbon::parse($request->sampai)->format('d-m-Y') : null;

        $periode = match (true) {
            $dari && $sampai => "$dari s/d $sampai",
            (bool) $dari     => "Sejak $dari",
            (bool) $sampai   => "Sampai $sampai",
            default          => 'Semua periode',
        };

        $status = match (true) {
            !$request->filled('status')     => 'Semua status',
            $request->status === 'berjalan' => 'Berkas berjalan',
            default                         => ucfirst(str_replace('_', ' ', $request->status)),
        };

        return [
            'periode' => $periode,
            'kantor'  => $request->filled('kantor_id')
                ? (Kantor::find($request->kantor_id)?->nama_kantor ?? '-')
                : 'Semua kantor',
            'status'  => $status,
        ];
    }

    /**
     * Halaman laporan (filter + ringkasan + tabel).
     * $opsi: judul, routeIndex, routeExport, kantors (null = sembunyikan filter kantor)
     */
    public function tampilkan(Request $request, array $opsi)
    {
        $request->validate($this->aturan($request));

        $query = $this->query($request);

        $berkas = (clone $query)
            ->with(['kantor', 'slo'])
            ->orderBy('tanggal_masuk')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('laporan.index', [
            'berkas'      => $berkas,
            'ringkasan'   => $this->ringkasan($query),
            'kantors'     => $opsi['kantors'] ?? null,
            'judul'       => $opsi['judul'],
            'routeIndex'  => $opsi['routeIndex'],
            'routeExport' => $opsi['routeExport'],
        ]);
    }

    /** Export Excel / PDF dengan filter yang sama. */
    public function export(Request $request, string $cakupan)
    {
        $request->validate(array_merge($this->aturan($request), [
            'format' => 'required|in:excel,pdf',
        ]));

        $query  = $this->query($request);
        $jumlah = (clone $query)->count();

        if ($jumlah === 0) {
            return back()->with('error', 'Tidak ada data pada filter yang dipilih, jadi tidak ada yang bisa diexport.');
        }

        if ($request->format === 'pdf' && $jumlah > 1000) {
            return back()->with('error', "Data terlalu banyak untuk PDF ($jumlah berkas). Persempit filter sampai maksimal 1.000 berkas, atau gunakan Excel.");
        }

        $berkas = (clone $query)
            ->with(['kantor', 'slo'])
            ->orderBy('tanggal_masuk')
            ->orderBy('id')
            ->get();

        $namaFile = 'laporan-berkas-' . now()->format('Ymd-His');

        if ($request->format === 'excel') {
            return Excel::download(new LaporanBerkasExport($berkas), $namaFile . '.xlsx');
        }

        $html = view('laporan.pdf', [
            'berkas'      => $berkas,
            'ringkasan'   => $this->ringkasan($query),
            'filter'      => $this->label($request),
            'cakupan'     => $cakupan,
            'dicetakOleh' => auth()->user()->name,
            'dicetakPada' => now()->format('d-m-Y H:i'),
        ])->render();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $namaFile . '.pdf"',
        ]);
    }
}