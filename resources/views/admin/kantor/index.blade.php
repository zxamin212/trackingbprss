<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Kelola Kantor</h3>
        <h6 class="op-7 mb-2">Tambah, edit, atau hapus data kantor</h6>
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
            <button type="button" class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#tambahKantor">
                <i class="fas fa-plus-circle me-1"></i> Tambah Kantor
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
                            <th>Nama Kantor</th>
                            <th>Jenis</th>
                            <th>Wilayah</th>
                            <th>Jumlah User</th>
                            <th>Jumlah Berkas</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kantors as $index => $k)
                            <tr>
                                <td>{{ $kantors->firstItem() + $index }}</td>
                                <td>{{ $k->nama_kantor }}</td>
                                <td>{{ ucfirst($k->jenis) }}</td>
                                <td>{{ $k->area ? ucfirst($k->area) : '-' }}</td>
                                <td>{{ $k->jumlah_user }}</td>
                                <td>{{ $k->jumlah_berkas }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-label-primary btn-round"
                                            data-bs-toggle="modal" data-bs-target="#editKantor{{ $k->id }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.kantor.destroy', $k->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin hapus kantor {{ $k->nama_kantor }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-label-danger btn-round">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="editKantor{{ $k->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.kantor.update', $k->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Kantor</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Kantor</label>
                                                    <input type="text" name="nama_kantor" class="form-control" value="{{ $k->nama_kantor }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Jenis</label>
                                                    <select name="jenis" class="form-select" required>
                                                        <option value="pusat" {{ $k->jenis == 'pusat' ? 'selected' : '' }}>Pusat</option>
                                                        <option value="cabang" {{ $k->jenis == 'cabang' ? 'selected' : '' }}>Cabang</option>
                                                        <option value="kas" {{ $k->jenis == 'kas' ? 'selected' : '' }}>Kas</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Wilayah</label>
                                                    <select name="area" class="form-select">
                                                        <option value="">-- Tidak ada --</option>
                                                        <option value="barat" {{ $k->area == 'barat' ? 'selected' : '' }}>Barat</option>
                                                        <option value="selatan" {{ $k->area == 'selatan' ? 'selected' : '' }}>Selatan</option>
                                                        <option value="timur" {{ $k->area == 'timur' ? 'selected' : '' }}>Timur</option>
                                                    </select>
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
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada kantor.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $kantors->links() }}</div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="tambahKantor" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.kantor.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Kantor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kantor</label>
                            <input type="text" name="nama_kantor" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis</label>
                            <select name="jenis" class="form-select" required>
                                <option value="pusat">Pusat</option>
                                <option value="cabang">Cabang</option>
                                <option value="kas">Kas</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Wilayah</label>
                            <select name="area" class="form-select">
                                <option value="">-- Tidak ada --</option>
                                <option value="barat">Barat</option>
                                <option value="selatan">Selatan</option>
                                <option value="timur">Timur</option>
                            </select>
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