<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Berkas Kredit</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8px; color: #222; }
        h2 { margin: 0 0 2px 0; font-size: 14px; }
        .sub { color: #666; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 3px 4px; vertical-align: top; }
        th { background: #e9edf5; text-align: left; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .num { text-align: right; }
        .meta { margin-bottom: 8px; }
        .meta td { border: none; padding: 1px 6px 1px 0; }
    </style>
</head>
<body>
    @php $label = fn($s) => ucfirst(str_replace('_', ' ', $s)); @endphp

    <h2>Laporan Berkas Kredit — BPR Sahabat Sejati</h2>
    <div class="sub">Cakupan: {{ $cakupan }} · Dicetak {{ $dicetakPada }} oleh {{ $dicetakOleh }}</div>

    <table class="meta">
        <tr><td>Periode (tanggal masuk)</td><td>: {{ $filter['periode'] }}</td></tr>
        <tr><td>Kantor</td><td>: {{ $filter['kantor'] }}</td></tr>
        <tr><td>Status</td><td>: {{ $filter['status'] }}</td></tr>
        <tr><td>Total berkas</td><td>: {{ $ringkasan['total'] }}</td></tr>
        <tr><td>Total plafon</td><td>: Rp {{ number_format($ringkasan['plafon'], 0, ',', '.') }}</td></tr>
        <tr>
            <td>Per status</td>
            <td>:
                @foreach($ringkasan['perStatus'] as $status => $jumlah)
                    {{ $label($status) }} {{ $jumlah }}@if(!$loop->last), @endif
                @endforeach
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Berkas</th>
                <th>Nasabah</th>
                <th>Jenis Kredit</th>
                <th class="num">Plafon (Rp)</th>
                <th>Kantor</th>
                <th>SLO</th>
                <th>Tgl Masuk</th>
                <th>Tgl Selesai</th>
                <th class="num">Lama</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($berkas as $i => $b)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $b->nomor_berkas }}</td>
                    <td>{{ $b->nama_nasabah }}</td>
                    <td>{{ $b->jenis_kredit }}</td>
                    <td class="num">{{ number_format($b->plafon, 0, ',', '.') }}</td>
                    <td>{{ $b->kantor->nama_kantor ?? '-' }}</td>
                    <td>{{ $b->slo->name ?? '-' }}</td>
                    <td>{{ $b->tanggal_masuk?->format('d-m-Y') }}</td>
                    <td>{{ $b->tanggal_selesai?->format('d-m-Y') ?? '-' }}</td>
                    <td class="num">{{ $b->lama_hari }} hari</td>
                    <td>{{ $label($b->status_terkini) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>