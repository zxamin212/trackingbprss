<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Detail Berkas</h3>
        <h6 class="op-7 mb-2">{{ $berkas->nomor_berkas }}</h6>
    </x-slot>

    <div class="row">
        <div class="col-md-5">
            <div class="card card-round">
                <div class="card-body">
                    <p class="mb-1"><span class="text-muted">No. Berkas</span></p>
                    <h5 class="mono">{{ $berkas->nomor_berkas }}</h5>

                    <hr>

                    <p class="mb-1"><span class="text-muted">Nasabah</span></p>
                    <p class="fw-bold">{{ $berkas->nama_nasabah }}</p>

                    <p class="mb-1"><span class="text-muted">Jenis Kredit</span></p>
                    <p class="fw-bold">{{ $berkas->jenis_kredit }}</p>

                    <p class="mb-1"><span class="text-muted">Tanggal Masuk</span></p>
                    <p class="fw-bold">{{ $berkas->tanggal_masuk->translatedFormat('d M Y') }}</p>

                    @if($berkas->tanggal_selesai)
                        <p class="mb-1"><span class="text-muted">Tanggal Selesai</span></p>
                        <p class="fw-bold">{{ $berkas->tanggal_selesai->translatedFormat('d M Y') }}
                            ({{ $berkas->tanggal_masuk->diffInDays($berkas->tanggal_selesai) }} hari)
                        </p>
                    @else
                        <p class="mb-1"><span class="text-muted">Sudah Berjalan</span></p>
                        <p class="fw-bold text-warning">{{ $berkas->tanggal_masuk->diffInDays(now()) }} hari</p>
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
                    @foreach($berkas->histories as $history)
                        <div class="d-flex mb-3">
                            <div class="me-3 text-center" style="width: 16px;">
                                <div style="width: 12px; height: 12px; border-radius: 50%; background: {{ $loop->last ? '#f0b429' : '#1f9d63' }}; margin-top: 4px;"></div>
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
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('cs.berkas.index') }}" class="btn btn-label-secondary btn-round">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>

</x-app-layout>