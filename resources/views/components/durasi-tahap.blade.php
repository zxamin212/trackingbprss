@props(['data'])

@php
    $labelTahap = [
        'diajukan' => 'Diajukan',
        'screening_data' => 'Screening Data',
        'slik' => 'SLIK',
        'survey' => 'Survey',
        'komite' => 'Komite',
        'realisasi' => 'Realisasi',
    ];
    $nilaiValid = array_filter($data, fn($v) => $v !== null);
    $maxDurasi = count($nilaiValid) ? max(max($nilaiValid), 0.1) : 1;
    $tahapTerlama = count($nilaiValid) > 1 ? array_search(max($nilaiValid), $nilaiValid) : null;
@endphp

<div class="card card-round">
    <div class="card-header">
        <div class="card-title">Rata-rata Durasi per Tahap</div>
        <p class="text-muted mb-0" style="font-size: 13px;">Tahap mana yang paling lama diproses (dalam hari)</p>
    </div>
    <div class="card-body">
        @foreach($data as $status => $hari)
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-bold">
                        {{ $labelTahap[$status] }}
                        @if($status === $tahapTerlama)
                            <span class="badge badge-danger ms-1">Terlama</span>
                        @endif
                    </span>
                    <span class="text-muted">{{ $hari !== null ? $hari . ' hari' : 'Belum ada data' }}</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar {{ $status === $tahapTerlama ? 'bg-danger' : 'bg-primary' }}" role="progressbar"
                         style="width: {{ $hari !== null ? min(100, ($hari / $maxDurasi) * 100) : 0 }}%">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>