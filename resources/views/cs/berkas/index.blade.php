<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Verifikasi Berkas</h3>
        <h6 class="op-7 mb-2">Daftar berkas kredit di kantor Anda</h6>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mb-3">
        <div class="col-12 text-end">
            <a href="{{ route('cs.berkas.create') }}" class="btn btn-primary btn-round">
                <i class="fas fa-plus-circle me-1"></i> Input Berkas Baru
            </a>
        </div>
    </div>

    <div class="card card-round">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>No. Berkas</th>
                            <th>Nasabah</th>
                            <th>Jenis Kredit</th>
                            <th>Status</th>
                            <th>Tanggal Masuk</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berkas as $item)
                            <tr>
                                <td class="mono">{{ $item->nomor_berkas }}</td>
                                <td>{{ $item->nama_nasabah }}</td>
                                <td>{{ $item->jenis_kredit }}</td>
                                <td>
                                    @php
                                        $statusColor = match($item->status_terkini) {
                                            'diajukan' => 'secondary',
                                            'verifikasi' => 'info',
                                            'survey' => 'primary',
                                            'komite' => 'primary',
                                            'ditolak_survey', 'ditolak_komite' => 'danger',
                                            'belum_lengkap' => 'warning',
                                            'akad' => 'warning',
                                            'pencairan' => 'success',
                                            'dibatalkan' => 'dark',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $statusColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->status_terkini)) }}
                                    </span>
                                </td>
                                <td>{{ $item->tanggal_masuk->translatedFormat('d M Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('cs.berkas.show', $item->id) }}" class="btn btn-sm btn-label-info btn-round">
                                        Lihat
                                    </a>

                                    @if($item->status_terkini === 'diajukan')
                                        <form action="{{ route('cs.berkas.verifikasi', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-label-success btn-round"
                                                    onclick="return confirm('Tandai berkas ini selesai diverifikasi?')">
                                                Verifikasi
                                            </button>
                                        </form>
                                    @endif

                                    @if(!in_array($item->status_terkini, ['pencairan', 'dibatalkan', 'ditolak_survey', 'ditolak_komite']))
                                        <form action="{{ route('cs.berkas.batalkan', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-label-danger btn-round"
                                                    onclick="return confirm('Yakin batalkan berkas ini?')">
                                                Batalkan
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada berkas. Klik "Input Berkas Baru" untuk mulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $berkas->links() }}
    </div>

</x-app-layout>