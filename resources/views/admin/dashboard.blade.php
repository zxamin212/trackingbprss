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
                                <h4 class="card-title">24</h4>
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
                                <h4 class="card-title">3</h4>
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
                                <h4 class="card-title">7</h4>
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
                                <h4 class="card-title">9.2</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
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
                                    <th>Tahap</th>
                                    <th class="text-end">Durasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>BK-2026-0031</td>
                                    <td>Rina Marlina</td>
                                    <td><span class="badge badge-info">Verifikasi</span></td>
                                    <td class="text-end">2 hari</td>
                                </tr>
                                <tr>
                                    <td>BK-2026-0028</td>
                                    <td>Agus Setiawan</td>
                                    <td><span class="badge badge-secondary">Survey & Analisa</span></td>
                                    <td class="text-end">16 hari</td>
                                </tr>
                                <tr>
                                    <td>BK-2026-0025</td>
                                    <td>Siti Halimah</td>
                                    <td><span class="badge badge-warning">Belum Lengkap</span></td>
                                    <td class="text-end">21 hari</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>