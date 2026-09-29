<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">{{ $judul }}</h3>
        <h6 class="op-7 mb-2">Berkas yang Anda input dengan status ini</h6>
    </x-slot>

    <div class="card card-round">
        <div class="card-body">

            <form method="GET" class="row g-2 mb-3">
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
                            <th>Tanggal Selesai</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berkas as $index => $item)
                            <tr>
                                <td>{{ $berkas->firstItem() + $index }}</td>
                                <td>{{ $item->nama_nasabah }}</td>
                                <td class="mono">{{ $item->nomor_berkas }}</td>
                                <td>{{ $item->lama_hari }} hari</td>
                                <td>{{ $item->tanggal_selesai?->translatedFormat('d M Y') ?? '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('cs.berkas.show', $item->id) }}" class="btn btn-sm btn-label-info btn-round">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Tidak ada data yang tersedia pada tabel ini.
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