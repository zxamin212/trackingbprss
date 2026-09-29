<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Kelola Berkas</h3>
        <h6 class="op-7 mb-2">Admin dapat mengedit, mengubah status, dan menghapus berkas</h6>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-round">
        <div class="card-body">

            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        @foreach(['diajukan','screening_data','slik','survey','komite','realisasi','cair','pending','tolak','batal'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="cari" class="form-control" placeholder="Cari nama nasabah..." value="{{ request('cari') }}">
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
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berkas as $index => $item)
                            @php
                                $statusColor = match($item->status_terkini) {
                                    'diajukan' => 'secondary', 'screening_data' => 'info', 'slik' => 'info',
                                    'survey' => 'primary', 'komite' => 'primary', 'realisasi' => 'warning',
                                    'cair' => 'success', 'batal' => 'dark', 'pending' => 'warning', 'tolak' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $berkas->firstItem() + $index }}</td>
                                <td>{{ $item->nama_nasabah }}</td>
                                <td class="mono">{{ $item->nomor_berkas }}</td>
                                <td>{{ $item->kantor->nama_kantor ?? '-' }}</td>
                                <td><span class="badge badge-{{ $statusColor }}">{{ ucfirst(str_replace('_',' ',$item->status_terkini)) }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.berkas.show', $item->id) }}" class="btn btn-sm btn-label-info btn-round">Lihat</a>
                                    <a href="{{ route('admin.berkas.edit', $item->id) }}" class="btn btn-sm btn-label-primary btn-round">Edit</a>
                                    <form action="{{ route('admin.berkas.destroy', $item->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin hapus PERMANEN berkas {{ $item->nomor_berkas }}? Tindakan ini tidak bisa dibatalkan.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-label-danger btn-round">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $berkas->links() }}</div>
        </div>
    </div>
</x-app-layout>