<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Edit Berkas (Admin)</h3>
        <h6 class="op-7 mb-2">{{ $berkas->nomor_berkas }} — Admin dapat mengubah semua field termasuk status</h6>
    </x-slot>

    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-1"></i>
        Mengubah status di sini akan <strong>melewati alur normal SLO</strong> dan tercatat di riwayat sebagai override.
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">

    <div class="row">
        <div class="col-md-8">
            <div class="card card-round">
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.berkas.update', $berkas->id) }}" enctype="multipart/form-data" id="formBerkas">
                        @csrf
                        @method('PUT')

                        <h6 class="fw-bold mb-3">Data Nasabah</h6>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_nasabah" class="form-control" value="{{ old('nama_nasabah', $berkas->nama_nasabah) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $berkas->tempat_lahir) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="text" name="tanggal_lahir" id="tanggal_lahir" class="form-control datepicker"
                                       value="{{ old('tanggal_lahir', $berkas->tanggal_lahir?->format('d-m-Y')) }}" required autocomplete="off">
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
                            <label class="form-label">Alamat KTP</label>
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
                                <label class="form-label">No. HP</label>
                                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $berkas->no_hp) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pekerjaan / Usaha</label>
                                <input type="text" name="pekerjaan_usaha" class="form-control" value="{{ old('pekerjaan_usaha', $berkas->pekerjaan_usaha) }}" required>
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
                                <p><a href="{{ Storage::url($berkas->file_dokumen) }}" target="_blank" class="btn btn-sm btn-label-info btn-round">Lihat Dokumen Saat Ini</a></p>
                            @endif
                            <input type="file" name="file_dokumen" class="form-control" accept="application/pdf">
                            <small class="text-muted">Kosongkan kalau tidak ingin mengganti.</small>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">Data Proses</h6>

                        <div class="mb-3">
                            <label class="form-label">Kantor</label>
                            <select name="kantor_id" id="kantor_id" class="form-select" required>
                                @foreach ($kantors as $kantor)
                                    <option value="{{ $kantor->id }}" {{ old('kantor_id', $berkas->kantor_id) == $kantor->id ? 'selected' : '' }}>{{ $kantor->nama_kantor }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">SLO</label>
                            <input type="text" id="slo_display" class="form-control" readonly disabled>
                            <input type="hidden" name="slo_id" id="slo_id" value="{{ old('slo_id', $berkas->slo_id) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sumber Berkas</label>
                            <select name="sumber" class="form-select">
                                <option value="langsung" {{ old('sumber', $berkas->sumber) == 'langsung' ? 'selected' : '' }}>Nasabah datang langsung</option>
                                <option value="marketing" {{ old('sumber', $berkas->sumber) == 'marketing' ? 'selected' : '' }}>Marketing</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="text" name="tanggal_masuk" id="tanggal_masuk" class="form-control datepicker"
                                   value="{{ old('tanggal_masuk', $berkas->tanggal_masuk->format('d-m-Y')) }}" required autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai (opsional)</label>
                            <input type="text" name="tanggal_selesai" id="tanggal_selesai" class="form-control datepicker"
                                   value="{{ old('tanggal_selesai', $berkas->tanggal_selesai?->format('d-m-Y')) }}" autocomplete="off">
                        </div>

                        <div class="mb-3 p-3" style="background:#fff3cd; border-radius:8px;">
                            <label class="form-label fw-bold">Status Terkini (Override Admin)</label>
                            <select name="status_terkini" class="form-select" required>
                                @foreach ($statusList as $status)
                                    <option value="{{ $status }}" {{ old('status_terkini', $berkas->status_terkini) == $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_',' ',$status)) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Kalau status diubah dari nilai saat ini, akan tercatat di riwayat sebagai perubahan paksa oleh Admin.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan Override (opsional)</label>
                            <textarea name="keterangan_override" class="form-control" rows="2" placeholder="Alasan mengubah status secara manual..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan Umum</label>
                            <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $berkas->keterangan) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-round">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.berkas.show', $berkas->id) }}" class="btn btn-label-secondary btn-round">Batal</a>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script>
        flatpickr(".datepicker", { dateFormat: "d-m-Y", allowInput: true });

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

        const plafonDisplay = document.getElementById('plafon_display');
        const plafonHidden = document.getElementById('plafon');
        if (plafonHidden.value) plafonDisplay.value = Number(plafonHidden.value).toLocaleString('id-ID');
        plafonDisplay.addEventListener('input', function (e) {
            let raw = e.target.value.replace(/[^0-9]/g, '');
            plafonHidden.value = raw;
            e.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
        });

        // SLO otomatis mengikuti Kantor yang dipilih (1 kantor = 1 SLO)
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

            if (!slo) {
                sloDisplay.value = 'Tidak ada SLO untuk kantor ini';
                sloHidden.value = '';
                return;
            }

            sloDisplay.value = slo.nama;
            sloHidden.value = slo.id;
        }

        kantorSelect.addEventListener('change', setSloByKantor);

        // Jalankan sekali saat halaman dimuat, supaya SLO terisi sesuai data berkas yang ada
        setSloByKantor();

        document.getElementById('formBerkas').addEventListener('submit', function () {
            convertDateFormat('tanggal_lahir');
            convertDateFormat('tanggal_masuk');
            convertDateFormat('tanggal_selesai');
        });
        function convertDateFormat(id) {
            const field = document.getElementById(id);
            if (!field.value) return;
            const parts = field.value.split('-');
            if (parts.length === 3) field.value = `${parts[2]}-${parts[1]}-${parts[0]}`;
        }
    </script>

</x-app-layout>