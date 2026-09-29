<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Daftar Monitoring</h3>
        <h6 class="op-7 mb-2">Data Nasabah — Berkas yang perlu Anda tangani</h6>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-round">
        <div class="card-body">

            <form method="GET" action="{{ route('slo.berkas.index') }}" class="row g-2 mb-3">
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
                            <th>Lama Hari</th>
                            <th>Nama LO</th>
                            <th>Status Progress</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berkas as $index => $item)
                            @php
                                $statusColor = match($item->status_terkini) {
                                    'diajukan' => 'secondary',
                                    'screening_data' => 'info',
                                    'slik' => 'info',
                                    'survey' => 'primary',
                                    'komite' => 'primary',
                                    'realisasi' => 'warning',
                                    'cair' => 'success',
                                    'batal' => 'dark',
                                    'pending' => 'warning',
                                    'tolak' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $berkas->firstItem() + $index }}</td>
                                <td>{{ $item->nama_nasabah }}</td>
                                <td class="mono">{{ $item->nomor_berkas }}</td>
                                <td>{{ $item->lama_hari }} hari</td>
                                <td>{{ $item->slo->name ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $statusColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->status_terkini)) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('slo.berkas.show', $item->id) }}" class="btn btn-sm btn-label-info btn-round">
                                        Kelola
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada berkas yang perlu ditangani.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $berkas->links() }}
            </div>

        </div>
    </div>

</x-app-layout>