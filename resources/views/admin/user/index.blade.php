<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Kelola User</h3>
        <h6 class="op-7 mb-2">Tambah, edit, atau hapus akun pengguna</h6>
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

    <div class="card card-round">
        <div class="card-body">

            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <form method="GET">
                        <select name="role" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Semua Role --</option>
                            @foreach(['admin','cs','slo','admin_legal','direksi','area_manager','manager_bisnis'] as $r)
                                <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$r)) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="GET" class="d-flex gap-2">
                        <input type="hidden" name="role" value="{{ request('role') }}">
                        <input type="text" name="cari" class="form-control" placeholder="Cari nama..." value="{{ request('cari') }}">
                        <button type="submit" class="btn btn-primary btn-round">Cari</button>
                    </form>
                </div>
                <div class="col-md-auto ms-auto">
                    <button type="button" class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#tambahUser">
                        <i class="fas fa-user-plus me-1"></i> Tambah User
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Kantor</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $u)
                            <tr>
                                <td>{{ $users->firstItem() + $index }}</td>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td><span class="badge badge-secondary">{{ ucfirst(str_replace('_',' ',$u->role)) }}</span></td>
                                <td>{{ $u->kantor->nama_kantor ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-label-primary btn-round"
                                                data-bs-toggle="modal" data-bs-target="#editUser{{ $u->id }}">
                                            Edit
                                        </button>
                                        @if($u->id !== auth()->id())
                                            <form action="{{ route('admin.user.destroy', $u->id) }}" method="POST"
                                                  onsubmit="return confirm('Yakin hapus user {{ $u->name }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-label-danger btn-round">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada user.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $users->links() }}</div>
        </div>
    </div>

    {{-- Modal Edit — DITARUH DI LUAR TABLE, setelah card --}}
    @foreach($users as $u)
        <div class="modal fade" id="editUser{{ $u->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.user.update', $u->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password Baru (opsional)</label>
                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select role-select" required>
                                    @foreach(['admin','cs','slo','admin_legal','direksi','area_manager','manager_bisnis'] as $r)
                                        <option value="{{ $r }}" {{ $u->role === $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$r)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kantor</label>
                                <select name="kantor_id" class="form-select">
                                    <option value="">-- Tidak ada --</option>
                                    @foreach($kantors as $k)
                                        <option value="{{ $k->id }}" {{ $u->kantor_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kantor }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 area-field" style="{{ $u->role !== 'area_manager' ? 'display:none' : '' }}">
                                <label class="form-label">Wilayah (khusus Area Manager)</label>
                                <select name="area" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="barat" {{ $u->area == 'barat' ? 'selected' : '' }}>Barat</option>
                                    <option value="selatan" {{ $u->area == 'selatan' ? 'selected' : '' }}>Selatan</option>
                                    <option value="timur" {{ $u->area == 'timur' ? 'selected' : '' }}>Timur</option>
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
    @endforeach

    <!-- Modal Tambah -->
    <div class="modal fade" id="tambahUser" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.user.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select role-select" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach(['admin','cs','slo','admin_legal','direksi','area_manager','manager_bisnis'] as $r)
                                    <option value="{{ $r }}">{{ ucfirst(str_replace('_',' ',$r)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kantor</label>
                            <select name="kantor_id" class="form-select">
                                <option value="">-- Tidak ada --</option>
                                @foreach($kantors as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kantor }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 area-field" style="display:none">
                            <label class="form-label">Wilayah (khusus Area Manager)</label>
                            <select name="area" class="form-select">
                                <option value="">-- Pilih --</option>
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

    <script>
        document.querySelectorAll('.role-select').forEach(function (select) {
            const areaField = select.closest('form').querySelector('.area-field');
            function toggleArea() {
                areaField.style.display = select.value === 'area_manager' ? 'block' : 'none';
            }
            select.addEventListener('change', toggleArea);
            toggleArea();
        });
    </script>

</x-app-layout>