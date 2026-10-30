@props(['selected' => null])

@php
    $daftar = \App\Models\JenisKredit::aktif()->urut()->pluck('nama');
    $dipilih = old('jenis_kredit', $selected);
    $nonaktif = $dipilih && !$daftar->contains($dipilih);
@endphp

<select name="jenis_kredit" class="form-select" required>
    <option value="">-- Pilih jenis kredit --</option>
    @if($nonaktif)
        <option value="{{ $dipilih }}" selected>{{ $dipilih }} (nonaktif)</option>
    @endif
    @foreach($daftar as $nama)
        <option value="{{ $nama }}" {{ $dipilih === $nama ? 'selected' : '' }}>{{ $nama }}</option>
    @endforeach
</select>