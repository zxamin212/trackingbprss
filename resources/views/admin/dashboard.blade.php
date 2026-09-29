<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Dashboard Admin</h3>
        <h6 class="op-7 mb-2">Ringkasan seluruh berkas kredit — semua kantor</h6>
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
                                <h4 class="card-title">{{ $totalAktif }}</h4>
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
                            <div class="icon-big text-center icon-warning bubble-shadow-small">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Lebih dari 14 Hari</p>
                                <h4 class="card-title">{{ $lebihDari14Hari }}</h4>
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
                                <p class="card-category">Cair Bulan Ini</p>
                                <h4 class="card-title">{{ $cairBulanIni }}</h4>
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
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Rata-rata Hari Selesai</p>
                                <h4 class="card-title">{{ $rataRataHari ? number_format($rataRataHari, 1) : '-' }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-5">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-title">Ringkasan Status Final</div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Berkas</span>
                        <span class="fw-bold">{{ $totalBerkas }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-success">Sudah Cair</span>
                        <span class="fw-bold text-success">{{ $totalCair }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-danger">Ditolak</span>
                        <span class="fw-bold text-danger">{{ $totalTolak }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-dark">Dibatalkan</span>
                        <span class="fw-bold text-dark">{{ $totalBatal }}</span>
                    </div>
                </div>
            </div>
        </div>

    <div class="col-md-7">
        <x-durasi-tahap :data="$durasiPerTahap" />
    </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Berkas Terbaru</div>
                    <a href="{{ route('admin.berkas.index') }}" class="btn btn-sm btn-label-primary btn-round">Kelola Semua Berkas</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>No. Berkas</th>
                                    <th>Nasabah</th>
                                    <th>Kantor</th>
                                    <th>Tahap</th>
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
                                        <td>{{ $item->kantor->nama_kantor ?? '-' }}</td>
                                        <td><span class="badge badge-{{ $statusColor }}">{{ ucfirst(str_replace('_',' ',$item->status_terkini)) }}</span></td>
                                        <td class="text-end">{{ $item->lama_hari }} hari</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>