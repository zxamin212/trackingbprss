<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Input Berkas Baru</h3>
        <h6 class="op-7 mb-2">Catat pengajuan kredit yang baru masuk</h6>
    </x-slot>

    <div class="row">
        <div class="col-md-8">
            <div class="card card-round">
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('cs.berkas.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nama Nasabah</label>
                            <input type="text" name="nama_nasabah" class="form-control"
                                   value="{{ old('nama_nasabah') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Kredit</label>
                            <select name="jenis_kredit" class="form-select" required>
                                <option value="">-- Pilih jenis kredit --</option>
                                <option value="Kredit Modal Usaha" {{ old('jenis_kredit') == 'Kredit Modal Usaha' ? 'selected' : '' }}>Kredit Modal Usaha</option>
                                <option value="Kredit Investasi" {{ old('jenis_kredit') == 'Kredit Investasi' ? 'selected' : '' }}>Kredit Investasi</option>
                                <option value="Kredit Konsumtif" {{ old('jenis_kredit') == 'Kredit Konsumtif' ? 'selected' : '' }}>Kredit Konsumtif</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sumber Berkas</label>
                            <select name="sumber" class="form-select">
                                <option value="langsung">Nasabah datang langsung</option>
                                <option value="marketing">Diserahkan Marketing (door to door)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control"
                                   value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan (opsional)</label>
                            <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-round">
                            <i class="fas fa-save me-1"></i> Simpan Berkas
                        </button>
                        <a href="{{ route('cs.berkas.index') }}" class="btn btn-label-secondary btn-round">Batal</a>
                    </form>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>