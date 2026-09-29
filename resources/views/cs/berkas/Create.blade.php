<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Input Berkas Baru</h3>
        <h6 class="op-7 mb-2">Catat pengajuan kredit yang baru masuk</h6>
    </x-slot>

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">

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

                    <form method="POST" action="{{ route('cs.berkas.store') }}" enctype="multipart/form-data" id="formBerkas">
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
                                <input type="text" name="tanggal_lahir" id="tanggal_lahir" class="form-control datepicker"
                                       value="{{ old('tanggal_lahir') }}" placeholder="dd-mm-yyyy" required autocomplete="off">
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
                                <input type="text" name="no_hp" id="no_hp" class="form-control"
                                       value="{{ old('no_hp') }}" inputmode="numeric" placeholder="08xxxxxxxxxx" required>
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
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="plafon_display" class="form-control"
                                       inputmode="numeric" placeholder="1.000.000" required>
                            </div>
                            <input type="hidden" name="plafon" id="plafon" value="{{ old('plafon') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dokumen Pendukung (PDF)</label>
                            <input type="file" name="file_dokumen" class="form-control" accept="application/pdf" required>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">Data Proses</h6>

                        <div class="mb-3">
                            <label class="form-label">Kantor</label>
                            <select name="kantor_id" id="kantor_id" class="form-select" required>
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
                            <input type="text" id="slo_display" class="form-control" value="-- Pilih Kantor dulu --" readonly disabled>
                            <input type="hidden" name="slo_id" id="slo_id" value="{{ old('slo_id') }}">
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
                            <input type="text" name="tanggal_masuk" id="tanggal_masuk" class="form-control datepicker"
                                   value="{{ old('tanggal_masuk', date('d-m-Y')) }}" placeholder="dd-mm-yyyy" required autocomplete="off">
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

    <!-- Flatpickr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>

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

        // Date picker format dd-mm-yyyy, tapi kirim ke server format yyyy-mm-dd
        flatpickr(".datepicker", {
            dateFormat: "d-m-Y",
            allowInput: true,
        });

        // No HP: hanya angka
        document.getElementById('no_hp').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });

        // Plafon: format ribuan pakai titik saat mengetik, simpan angka murni di hidden input
        const plafonDisplay = document.getElementById('plafon_display');
        const plafonHidden = document.getElementById('plafon');

        // Set nilai awal kalau ada old input
        if (plafonHidden.value) {
            plafonDisplay.value = Number(plafonHidden.value).toLocaleString('id-ID');
        }

        plafonDisplay.addEventListener('input', function (e) {
            let raw = e.target.value.replace(/[^0-9]/g, '');
            plafonHidden.value = raw;
            e.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
        });

        // Sebelum submit, konversi tanggal dd-mm-yyyy jadi yyyy-mm-dd supaya valid di backend
        document.getElementById('formBerkas').addEventListener('submit', function (e) {
            convertDateFormat('tanggal_lahir');
            convertDateFormat('tanggal_masuk');
        });

        function convertDateFormat(id) {
            const field = document.getElementById(id);
            const parts = field.value.split('-'); // dd-mm-yyyy
            if (parts.length === 3) {
                field.value = `${parts[2]}-${parts[1]}-${parts[0]}`; // yyyy-mm-dd
            }
        }

        // SLO otomatis mengikuti Kantor yang dipilih (1 kantor = 1 SLO), ditampilkan sebagai teks
        const kantorSelect = document.getElementById('kantor_id');
        const sloDisplay = document.getElementById('slo_display');
        const sloHidden = document.getElementById('slo_id');

        const daftarSlo = [
            @foreach ($slos as $slo)
                { id: {{ $slo->id }}, kantorId: {{ $slo->kantor_id }}, nama: @json($slo->name) },
            @endforeach
        ];

        function setSloByKantor() {
            const kantorId = parseInt(kantorSelect.value);
            const slo = daftarSlo.find(s => s.kantorId === kantorId);

            if (!kantorId) {
                sloDisplay.value = '-- Pilih Kantor dulu --';
                sloHidden.value = '';
                return;
            }

            if (!slo) {
                sloDisplay.value = 'Tidak ada SLO untuk kantor ini';
                sloHidden.value = '';
                return;
            }

            sloDisplay.value = slo.nama;
            sloHidden.value = slo.id;
        }

        kantorSelect.addEventListener('change', setSloByKantor);

        // Jalankan sekali saat halaman load (misal validasi gagal, kantor sudah terisi dari old())
        if (kantorSelect.value) setSloByKantor();
    </script>

</x-app-layout>