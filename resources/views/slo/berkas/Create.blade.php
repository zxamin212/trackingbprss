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

                    <form method="POST" action="{{ route('cs.berkas.store') }}" enctype="multipart/form-data">
                        @csrf

                        <h6 class="fw-bold mb-3 mt-2">Data Nasabah</h6>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_nasabah" class="form-control"
                                   value="{{ old('nama_nasabah') }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control"
                                       value="{{ old('tempat_lahir') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control"
                                       value="{{ old('tanggal_lahir') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat sesuai KTP</label>
                            <textarea name="alamat_ktp" id="alamat_ktp" class="form-control" rows="2" required>{{ old('alamat_ktp') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input type="checkbox" id="samaKtp" class="form-check-input" onchange="toggleDomisili()">
                                <label for="samaKtp" class="form-check-label">Alamat domisili sama dengan KTP</label>
                            </div>
                            <label class="form-label">Alamat Domisili</label>
                            <textarea name="alamat_domisili" id="alamat_domisili" class="form-control" rows="2">{{ old('alamat_domisili') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. HP / Telepon</label>
                                <input type="text" name="no_hp" class="form-control"
                                       value="{{ old('no_hp') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pekerjaan / Usaha</label>
                                <input type="text" name="pekerjaan_usaha" class="form-control"
                                       value="{{ old('pekerjaan_usaha') }}" required>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">Data Pengajuan</h6>

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
                            <label class="form-label">Plafon (Rp)</label>
                            <input type="number" name="plafon" class="form-control" min="0" step="1000"
                                   value="{{ old('plafon') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dokumen Pendukung (PDF)</label>
                            <input type="file" name="file_dokumen" class="form-control" accept="application/pdf" required>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">Data Proses</h6>

                        <div class="mb-3">
                            <label class="form-label">Kantor</label>
                            <select name="kantor_id" class="form-select" required>
                                <option value="">-- Pilih Kantor --</option>
                                @foreach ($kantors as $kantor)
                                    <option value="{{ $kantor->id }}" {{ old('kantor_id') == $kantor->id ? 'selected' : '' }}>
                                        {{ $kantor->nama_kantor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">SLO</label>
                            <select name="slo_id" class="form-select" required>
                                <option value="">-- Pilih SLO --</option>
                                @foreach ($slos as $slo)
                                    <option value="{{ $slo->id }}" {{ old('slo_id') == $slo->id ? 'selected' : '' }}>
                                        {{ $slo->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sumber Berkas</label>
                            <select name="sumber" class="form-select">
                                <option value="langsung" {{ old('sumber') == 'langsung' ? 'selected' : '' }}>Nasabah datang langsung</option>
                                <option value="marketing" {{ old('sumber') == 'marketing' ? 'selected' : '' }}>Diserahkan Marketing (door to door)</option>
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

    <script>
        function toggleDomisili() {
            const checkbox = document.getElementById('samaKtp');
            const domisili = document.getElementById('alamat_domisili');
            if (checkbox.checked) {
                domisili.value = document.getElementById('alamat_ktp').value;
                domisili.readOnly = true;
            } else {
                domisili.readOnly = false;
            }
        }
    </script>

</x-app-layout>