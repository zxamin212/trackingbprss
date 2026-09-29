<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Laporan Berkas</h3>
        <h6 class="op-7 mb-2">Rekap berkas berdasarkan status akhir</h6>
    </x-slot>

    <div class="card card-round">
        <div class="card-body">

            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="cair" {{ request('status') === 'cair' ? 'selected' : '' }}>Cair</option>
                        <option value="batal" {{ request('status') === 'batal' ? 'selected' : '' }}>Batal</option>
                        <option value="tolak" {{ request('status') === 'tolak' ? 'selected' : '' }}>Tolak</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="cari" class="form-control" placeholder="Cari nama nasabah..."
                           value="{{ request('cari') }}">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary btn-round">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>No Register</th>
                            <th>Kantor</th>
                            <th>Lama Hari</th>
                            <th>Status</th>
                            <th>Tanggal Selesai</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berkas as $index => $item)
                            @php
                                $statusColor = match($item->status_terkini) {
                                    'cair' => 'success',
                                    'batal' => 'dark',
                                    'tolak' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $berkas->firstItem() + $index }}</td>
                                <td>{{ $item->nama_nasabah }}</td>
                                <td class="mono">{{ $item->nomor_berkas }}</td>
                                <td>{{ $item->kantor->nama_kantor ?? '-' }}</td>
                                <td>{{ $item->lama_hari }} hari</td>
                                <td><span class="badge badge-{{ $statusColor }}">{{ ucfirst($item->status_terkini) }}</span></td>
                                <td>{{ $item->tanggal_selesai?->translatedFormat('d M Y') ?? '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('direksi.berkas.show', $item->id) }}" class="btn btn-sm btn-label-info btn-round">Lihat</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $berkas->links() }}</div>
        </div>
    </div>

</x-app-layout>