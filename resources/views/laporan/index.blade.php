<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">{{ $judul }}</h3>
        <h6 class="op-7 mb-2">Filter data berkas, lalu export ke Excel atau PDF</h6>
    </x-slot>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @php
        $opsiStatus = [
            'berjalan' => 'Semua berkas berjalan',
            'diajukan' => 'Diajukan',
            'screening_data' => 'Screening Data',
            'slik' => 'SLIK',
            'survey' => 'Survey',
            'komite' => 'Komite',
            'realisasi' => 'Realisasi',
            'pending' => 'Pending',
            'cair' => 'Cair',
            'tolak' => 'Tolak',
            'batal' => 'Batal',
        ];
    @endphp

    <div class="card card-round">
        <div class="card-body">
            <form method="GET" action="{{ route($routeIndex) }}" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Tanggal Masuk Dari</label>
                    <input type="text" name="dari" class="form-control datepicker"
                           value="{{ request('dari') }}" placeholder="dd-mm-yyyy" autocomplete="off">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sampai</label>
                    <input type="text" name="sampai" class="form-control datepicker"
                           value="{{ request('sampai') }}" placeholder="dd-mm-yyyy" autocomplete="off">
                </div>

                @if($kantors)
                    <div class="col-md-3">
                        <label class="form-label">Kantor</label>
                        <select name="kantor_id" class="form-select">
                            <option value="">-- Semua Kantor --</option>
                            @foreach($kantors as $k)
                                <option value="{{ $k->id }}" {{ request('kantor_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kantor }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        @foreach($opsiStatus as $nilai => $label)
                            <option value="{{ $nilai }}" {{ request('status') === $nilai ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-round">
                        <i class="fas fa-search me-1"></i> Tampilkan
                    </button>
                    <a href="{{ route($routeIndex) }}" class="btn btn-label-secondary btn-round">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-round">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <p class="text-muted mb-1">Hasil filter</p>
                    <h4 class="fw-bold mb-1">{{ $ringkasan['total'] }} berkas</h4>
                    <p class="mb-2">Total plafon: <strong>Rp {{ number_format($ringkasan['plafon'], 0, ',', '.') }}</strong></p>
                    @foreach($ringkasan['perStatus'] as $status => $jumlah)
                        <span class="badge badge-secondary me-1">{{ ucfirst(str_replace('_', ' ', $status)) }}: {{ $jumlah }}</span>
                    @endforeach
                </div>

                <form method="POST" action="{{ route($routeExport) }}" class="d-flex gap-2">
                    @csrf
                    <input type="hidden" name="dari" value="{{ request('dari') }}">
                    <input type="hidden" name="sampai" value="{{ request('sampai') }}">
                    <input type="hidden" name="kantor_id" value="{{ request('kantor_id') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <button type="submit" name="format" value="excel" class="btn btn-success btn-round">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </button>
                    <button type="submit" name="format" value="pdf" class="btn btn-danger btn-round">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="card card-round">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>No. Berkas</th>
                            <th>Nasabah</th>
                            <th>Kantor</th>
                            <th>Jenis Kredit</th>
                            <th class="text-end">Plafon</th>
                            <th>Tgl Masuk</th>
                            <th>Tgl Selesai</th>
                            <th class="text-end">Lama</th>
                            <th>Status</th>
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
                                <td class="mono">{{ $item->nomor_berkas }}</td>
                                <td>{{ $item->nama_nasabah }}</td>
                                <td>{{ $item->kantor->nama_kantor ?? '-' }}</td>
                                <td>{{ $item->jenis_kredit }}</td>
                                <td class="text-end">{{ number_format($item->plafon, 0, ',', '.') }}</td>
                                <td>{{ $item->tanggal_masuk->format('d-m-Y') }}</td>
                                <td>{{ $item->tanggal_selesai?->format('d-m-Y') ?? '-' }}</td>
                                <td class="text-end">{{ $item->lama_hari }} hari</td>
                                <td><span class="badge badge-{{ $statusColor }}">{{ ucfirst(str_replace('_', ' ', $item->status_terkini)) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted py-4">Tidak ada data pada filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $berkas->links() }}</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script>
        // Tampil dd-mm-yyyy, tapi nilai yang dikirim ke server tetap yyyy-mm-dd
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d-m-Y",
        });
    </script>

</x-app-layout>