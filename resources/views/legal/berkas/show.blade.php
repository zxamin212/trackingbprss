<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Detail Berkas</h3>
        <h6 class="op-7 mb-2">{{ $berkas->nomor_berkas }}</h6>
    </x-slot>

    @php
        $statusColor = match($berkas->status_terkini) {
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

    <div class="row">
        <div class="col-md-5">
            <div class="card card-round">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="mb-1"><span class="text-muted">No. Berkas</span></p>
                            <h5 class="mono mb-0">{{ $berkas->nomor_berkas }}</h5>
                        </div>
                        <span class="badge badge-{{ $statusColor }}">
                            {{ ucfirst(str_replace('_', ' ', $berkas->status_terkini)) }}
                        </span>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-2">Data Nasabah</h6>
                    <p class="mb-1"><span class="text-muted">Nama</span></p>
                    <p class="fw-bold">{{ $berkas->nama_nasabah }}</p>

                    <p class="mb-1"><span class="text-muted">Tempat, Tanggal Lahir</span></p>
                    <p class="fw-bold">{{ $berkas->tempat_lahir }}, {{ $berkas->tanggal_lahir?->translatedFormat('d M Y') }}</p>

                    <p class="mb-1"><span class="text-muted">Jenis Kelamin</span></p>
                    <p class="fw-bold">{{ $berkas->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>

                    <p class="mb-1"><span class="text-muted">Alamat KTP</span></p>
                    <p class="fw-bold">{{ $berkas->alamat_ktp }}</p>

                    <p class="mb-1"><span class="text-muted">Alamat Domisili</span></p>
                    <p class="fw-bold">{{ $berkas->alamat_domisili ?: '-' }}</p>

                    <p class="mb-1"><span class="text-muted">No. HP</span></p>
                    <p class="fw-bold">{{ $berkas->no_hp }}</p>

                    <p class="mb-1"><span class="text-muted">Pekerjaan / Usaha</span></p>
                    <p class="fw-bold">{{ $berkas->pekerjaan_usaha }}</p>

                    <hr>

                    <h6 class="fw-bold mb-2">Data Pengajuan</h6>
                    <p class="mb-1"><span class="text-muted">Jenis Kredit</span></p>
                    <p class="fw-bold">{{ $berkas->jenis_kredit }}</p>

                    <p class="mb-1"><span class="text-muted">Plafon</span></p>
                    <p class="fw-bold">Rp {{ number_format($berkas->plafon, 0, ',', '.') }}</p>

                    <p class="mb-1"><span class="text-muted">Dokumen Pendukung</span></p>
                    @if($berkas->file_dokumen)
                        <a href="{{ Storage::url($berkas->file_dokumen) }}" target="_blank" class="btn btn-sm btn-label-info btn-round">
                            <i class="fas fa-file-pdf me-1"></i> Lihat Dokumen
                        </a>
                    @else
                        <p class="text-muted">Tidak ada file</p>
                    @endif

                    <hr>

                    <h6 class="fw-bold mb-2">Data Proses</h6>
                    <p class="mb-1"><span class="text-muted">Kantor</span></p>
                    <p class="fw-bold">{{ $berkas->kantor->nama_kantor ?? '-' }}</p>

                    <p class="mb-1"><span class="text-muted">CS Input</span></p>
                    <p class="fw-bold">{{ $berkas->user->name ?? '-' }}</p>

                    <p class="mb-1"><span class="text-muted">SLO Penanggung Jawab</span></p>
                    <p class="fw-bold">{{ $berkas->slo->name ?? '-' }}</p>

                    <p class="mb-1"><span class="text-muted">Tanggal Masuk</span></p>
                    <p class="fw-bold">{{ $berkas->tanggal_masuk->translatedFormat('d M Y') }}</p>

                    @if($berkas->tanggal_selesai)
                        <p class="mb-1"><span class="text-muted">Tanggal Selesai</span></p>
                        <p class="fw-bold">{{ $berkas->tanggal_selesai->translatedFormat('d M Y') }}
                            ({{ $berkas->lama_hari }} hari)
                        </p>
                    @else
                        <p class="mb-1"><span class="text-muted">Sudah Berjalan</span></p>
                        <p class="fw-bold text-warning">{{ $berkas->lama_hari }} hari</p>
                    @endif

                    @if($berkas->keterangan)
                        <p class="mb-1"><span class="text-muted">Keterangan</span></p>
                        <p class="fw-bold">{{ $berkas->keterangan }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-title">Riwayat Proses</div>
                </div>
                <div class="card-body">
                    @forelse($berkas->histories as $history)
                        @php
                            $dotColor = match($history->status) {
                                'tolak', 'batal' => '#e63757',
                                'pending' => '#f0b429',
                                'cair' => '#1f9d63',
                                default => '#3b7ddd',
                            };
                        @endphp
                        <div class="d-flex mb-3">
                            <div class="me-3 text-center" style="width: 16px;">
                                <div style="width: 12px; height: 12px; border-radius: 50%; background: {{ $dotColor }}; margin-top: 4px;"></div>
                                @if(!$loop->last)
                                    <div style="width: 1px; height: 100%; background: #e6e8ef; margin: 4px auto;"></div>
                                @endif
                            </div>
                            <div>
                                <p class="fw-bold mb-0">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</p>
                                <p class="text-muted mb-1" style="font-size: 12px;">
                                    {{ $history->created_at->translatedFormat('d M Y - H:i') }} WIB,
                                    oleh {{ $history->user->name ?? '-' }}
                                </p>
                                @if($history->keterangan)
                                    <p class="mb-0" style="font-size: 13px;">{{ $history->keterangan }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">Belum ada riwayat.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('legal.berkas.index') }}" class="btn btn-label-secondary btn-round">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>

</x-app-layout>