<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Edit Berkas</h3>
        <h6 class="op-7 mb-2">{{ $berkas->nomor_berkas }} — masih bisa diedit selama status Diajukan</h6>
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

                    <form method="POST" action="{{ route('cs.berkas.update', $berkas->id) }}" enctype="multipart/form-data" id="formBerkas">
                        @csrf
                        @method('PUT')

                        <h6 class="fw-bold mb-3 mt-2">Data Nasabah</h6>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_nasabah" class="form-control"
                                   value="{{ old('nama_nasabah', $berkas->nama_nasabah) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control"
                                       value="{{ old('tempat_lahir', $berkas->tempat_lahir) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="text" name="tanggal_lahir" id="tanggal_lahir" class="form-control datepicker"
                                       value="{{ old('tanggal_lahir', $berkas->tanggal_lahir ? \Carbon\Carbon::parse($berkas->tanggal_lahir)->format('d-m-Y') : '') }}"
                                       placeholder="dd-mm-yyyy" required autocomplete="off">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L" {{ old('jenis_kelamin', $berkas->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $berkas->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat sesuai KTP</label>
                            <textarea name="alamat_ktp" id="alamat_ktp" class="form-control" rows="2" required>{{ old('alamat_ktp', $berkas->alamat_ktp) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input type="checkbox" id="samaKtp" class="form-check-input" onchange="toggleDomisili()">
                                <label for="samaKtp" class="form-check-label">Alamat domisili sama dengan KTP</label>
                            </div>
                            <label class="form-label">Alamat Domisili</label>
                            <textarea name="alamat_domisili" id="alamat_domisili" class="form-control" rows="2">{{ old('alamat_domisili', $berkas->alamat_domisili) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. HP / Telepon</label>
                                <input type="text" name="no_hp" id="no_hp" class="form-control"
                                       value="{{ old('no_hp', $berkas->no_hp) }}" inputmode="numeric" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pekerjaan / Usaha</label>
                                <input type="text" name="pekerjaan_usaha" class="form-control"
                                       value="{{ old('pekerjaan_usaha', $berkas->pekerjaan_usaha) }}" required>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">Data Pengajuan</h6>

                        <div class="mb-3">
                            <label class="form-label">Jenis Kredit</label>
                            <select name="jenis_kredit" class="form-select" required>
                                @foreach (['Kredit Modal Usaha', 'Kredit Investasi', 'Kredit Konsumtif'] as $jenis)
                                    <option value="{{ $jenis }}" {{ old('jenis_kredit', $berkas->jenis_kredit) == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Plafon (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="plafon_display" class="form-control" inputmode="numeric" required>
                            </div>
                            <input type="hidden" name="plafon" id="plafon" value="{{ old('plafon', $berkas->plafon) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dokumen Pendukung (PDF)</label>
                            @if($berkas->file_dokumen)
                                <p class="mb-2">
                                    <a href="{{ Storage::url($berkas->file_dokumen) }}" target="_blank" class="btn btn-sm btn-label-info btn-round">
                                        <i class="fas fa-file-pdf me-1"></i> Lihat Dokumen Saat Ini
                                    </a>
                                </p>
                            @endif
                            <input type="file" name="file_dokumen" class="form-control" accept="application/pdf">
                            <small class="text-muted">Kosongkan kalau tidak ingin mengganti dokumen.</small>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">Data Proses</h6>

                        <!-- Kantor (Readonly & Hidden ID) -->
                        <div class="mb-3">
                            <label class="form-label">Kantor</label>
                            <input type="text" class="form-control" value="{{ $berkas->kantor->nama_kantor ?? '-' }}" readonly disabled>
                            <input type="hidden" name="kantor_id" value="{{ $berkas->kantor_id }}">
                        </div>

                        <!-- SLO (Readonly & Hidden ID) -->
                        <div class="mb-3">
                            <label class="form-label">SLO</label>
                            <input type="text" class="form-control" value="{{ $berkas->slo->name ?? '-' }}" readonly disabled>
                            <input type="hidden" name="slo_id" value="{{ $berkas->slo_id }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sumber Berkas</label>
                            <select name="sumber" class="form-select">
                                <option value="langsung" {{ old('sumber', $berkas->sumber) == 'langsung' ? 'selected' : '' }}>Nasabah datang langsung</option>
                                <option value="marketing" {{ old('sumber', $berkas->sumber) == 'marketing' ? 'selected' : '' }}>Diserahkan Marketing (door to door)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="text" name="tanggal_masuk" id="tanggal_masuk" class="form-control datepicker"
                                   value="{{ old('tanggal_masuk', $berkas->tanggal_masuk ? \Carbon\Carbon::parse($berkas->tanggal_masuk)->format('d-m-Y') : '') }}"
                                   placeholder="dd-mm-yyyy" required autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan (opsional)</label>
                            <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $berkas->keterangan) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-round">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('cs.berkas.show', $berkas->id) }}" class="btn btn-label-secondary btn-round">Batal</a>
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

        // Cek otomatis saat halaman load (jika alamat KTP dan Domisili sebelumnya sama)
        document.addEventListener("DOMContentLoaded", function() {
            const ktp = document.getElementById('alamat_ktp').value;
            const domisili = document.getElementById('alamat_domisili').value;
            if (ktp && domisili && ktp === domisili) {
                document.getElementById('samaKtp').checked = true;
                document.getElementById('alamat_domisili').readOnly = true;
            }
        });

        flatpickr(".datepicker", { dateFormat: "d-m-Y", allowInput: true });

        document.getElementById('no_hp').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });

        const plafonDisplay = document.getElementById('plafon_display');
        const plafonHidden = document.getElementById('plafon');
        if (plafonHidden.value) {
            plafonDisplay.value = Number(plafonHidden.value).toLocaleString('id-ID');
        }
        plafonDisplay.addEventListener('input', function (e) {
            let raw = e.target.value.replace(/[^0-9]/g, '');
            plafonHidden.value = raw;
            e.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
        });

        document.getElementById('formBerkas').addEventListener('submit', function () {
            convertDateFormat('tanggal_lahir');
            convertDateFormat('tanggal_masuk');
        });

        function convertDateFormat(id) {
            const field = document.getElementById(id);
            const parts = field.value.split('-');
            if (parts.length === 3) field.value = `${parts[2]}-${parts[1]}-${parts[0]}`;
        }
    </script>
</x-app-layout>