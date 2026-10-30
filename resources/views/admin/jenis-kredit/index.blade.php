<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Kelola Jenis Kredit</h3>
        <h6 class="op-7 mb-2">Daftar pilihan jenis kredit di form berkas</h6>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-12 text-end">
            <button type="button" class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#tambahJenis">
                <i class="fas fa-plus-circle me-1"></i> Tambah Jenis Kredit
            </button>
        </div>
    </div>

    <div class="card card-round">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Urutan</th>
                            <th>Status</th>
                            <th>Dipakai Berkas</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenisList as $i => $j)
                            @php $dipakai = $pemakaian[$j->nama] ?? 0; @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $j->nama }}</td>
                                <td>{{ $j->urutan }}</td>
                                <td>
                                    <span class="badge badge-{{ $j->aktif ? 'success' : 'secondary' }}">
                                        {{ $j->aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>{{ $dipakai }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-label-primary btn-round"
                                                data-bs-toggle="modal" data-bs-target="#editJenis{{ $j->id }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.jenis-kredit.toggle', $j->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm btn-label-{{ $j->aktif ? 'warning' : 'success' }} btn-round">
                                                {{ $j->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        @if($dipakai === 0)
                                            <form action="{{ route('admin.jenis-kredit.destroy', $j->id) }}" method="POST"
                                                  onsubmit="return confirm('Yakin hapus {{ $j->nama }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-label-danger btn-round">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada jenis kredit.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Edit: di luar tabel --}}
    @foreach($jenisList as $j)
        <div class="modal fade" id="editJenis{{ $j->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.jenis-kredit.update', $j->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Jenis Kredit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" name="nama" class="form-control" value="{{ $j->nama }}" maxlength="100" required>
                                <small class="text-muted">Kalau nama diubah, berkas lama yang memakai nama ini ikut diperbarui.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Urutan di dropdown</label>
                                <input type="number" name="urutan" class="form-control" value="{{ $j->urutan }}" min="0" max="9999" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-round" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-round">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal Tambah -->
    <div class="modal fade" id="tambahJenis" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.jenis-kredit.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Jenis Kredit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Urutan di dropdown (opsional)</label>
                            <input type="number" name="urutan" class="form-control" min="0" max="9999"
                                   placeholder="Kosongkan untuk ditaruh paling bawah">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-round" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-round">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>