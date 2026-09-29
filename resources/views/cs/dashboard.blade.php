<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Dashboard CS</h3>
        <h6 class="op-7 mb-2">Ringkasan berkas kredit yang Anda input</h6>
    </x-slot>

    <div class="row">
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-folder-open"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Berkas Berjalan</p>
                                <h4 class="card-title">{{ $berkasBerjalan }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Sudah Cair</p>
                                <h4 class="card-title">{{ $totalCair }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-danger bubble-shadow-small">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Ditolak</p>
                                <h4 class="card-title">{{ $totalTolak }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                <i class="fas fa-ban"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Dibatalkan</p>
                                <h4 class="card-title">{{ $totalBatal }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('cs.berkas.create') }}" class="btn btn-primary btn-round">
                <i class="fas fa-plus-circle me-1"></i> Input Berkas Baru
            </a>
            <a href="{{ route('cs.berkas.index') }}" class="btn btn-label-secondary btn-round">
                <i class="fas fa-list me-1"></i> Lihat Semua Berkas ({{ $totalBerkas }})
            </a>
        </div>
    </div>

    <div class="card card-round">
        <div class="card-header">
            <div class="card-title">Berkas Terbaru</div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>No. Berkas</th>
                            <th>Nasabah</th>
                            <th>Status</th>
                            <th class="text-end">Durasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berkasTerbaru as $item)
                            @php
                                $statusColor = match($item->status_terkini) {
                                    'diajukan' => 'secondary', 'screening_data' => 'info', 'slik' => 'info',
                                    'survey' => 'primary', 'komite' => 'primary', 'realisasi' => 'warning',
                                    'cair' => 'success', 'batal' => 'dark', 'pending' => 'warning', 'tolak' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td class="mono">{{ $item->nomor_berkas }}</td>
                                <td>{{ $item->nama_nasabah }}</td>
                                <td><span class="badge badge-{{ $statusColor }}">{{ ucfirst(str_replace('_',' ',$item->status_terkini)) }}</span></td>
                                <td class="text-end">{{ $item->lama_hari }} hari</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada berkas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-app-layout>