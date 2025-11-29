@extends('layouts.admin.app')
@section('title', 'Manajemen Pengguna')

@section('content')
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold text-primary mb-1">Manajemen Pengguna</h2>
            <p class="text-muted small mb-0">Kelola akun admin dan user aplikasi</p>
        </div>
        <button class="btn btn-primary" onclick="showAddModal()">
            <i class="fas fa-plus me-1"></i> Tambah Akun
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- TOOLBAR FILTER & SEARCH --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form action="{{ route('admin.manajemen_akun.index') }}" method="GET" class="row g-2 align-items-center">
                {{-- 1. Search --}}
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 bg-light"
                            placeholder="Cari nama atau email..." value="{{ request('search') }}">
                    </div>
                </div>

                {{-- 2. Filter Role --}}
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i
                                class="fas fa-user-tag text-muted"></i></span>
                        <select name="role" class="form-select border-start-0 bg-light" onchange="this.form.submit()">
                            <option value="">-- Semua Role --</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                        </select>
                    </div>
                </div>

                {{-- 3. Reset Button --}}
                <div class="col-md-auto">
                    <button type="submit" class="d-none"></button>
                    @if (request('search') || request('role'))
                        <a href="{{ route('admin.manajemen_akun.index') }}" class="btn btn-outline-secondary btn-sm"
                            title="Reset Filter">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Pengguna</th>
                            <th class="py-3">Role</th>
                            {{-- Tambahan Kolom NIP (Optional ditampilkan di header, atau gabung di kolom Pengguna) --}}
                            <th class="py-3">NIP</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                                            class="rounded-circle me-3" width="40" height="40" alt="Avatar">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <div class="text-muted small">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{-- ... (Badge Role TETAP SAMA) ... --}}
                                    @if ($user->role == 'admin')
                                        <span
                                            class="badge bg-primary-subtle text-primary border border-primary-subtle px-3">Admin</span>
                                    @else
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle px-3">User</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Tampilkan NIP dari relasi pegawai --}}
                                    <span class="font-monospace text-dark">{{ $user->pegawai->nip ?? '-' }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        {{-- Pass data lengkap termasuk NIP ke fungsi Javascript --}}
                                        <button class="btn btn-sm btn-outline-warning"
                                            onclick='editUser(@json($user), "{{ $user->pegawai->nip ?? '' }}")'
                                            title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>

                                        {{-- ... (Tombol Hapus TETAP SAMA) ... --}}
                                        @if (auth()->id() != $user->id)
                                            <form action="{{ route('admin.manajemen_akun.destroy', $user->id) }}"
                                                method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i
                                                        class="fas fa-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- ... (Empty State TETAP SAMA) ... --}}
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>

    {{-- MODAL TAMBAH/EDIT --}}
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.manajemen_akun.store') }}" method="POST" id="formUser">
                @csrf
                <div id="methodField"></div>

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Pengguna Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        {{-- INPUT NIP BARU --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">NIP (Username Login)</label>
                            <input type="text" name="nip" id="nip" class="form-control"
                                placeholder="Masukkan 18 digit NIP" maxlength="18" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            <small class="text-muted">NIP ini akan digunakan untuk login.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="Nama user..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                placeholder="user@kemenag.go.id" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Role</label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Password</label>
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Minimal 6 karakter">
                                <small class="text-muted d-none" id="passwordHint">Kosongkan jika tidak ingin
                                    mengubah.</small>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let modalInstance = null;

        function getModal() {
            if (!modalInstance) {
                const modalEl = document.getElementById('userModal');
                modalInstance = new bootstrap.Modal(modalEl);
            }
            return modalInstance;
        }

        function showAddModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Pengguna Baru';
            document.getElementById('formUser').action = "{{ route('admin.manajemen_akun.store') }}";
            document.getElementById('formUser').reset();
            document.getElementById('methodField').innerHTML = '';

            // Mode Tambah: Password Wajib
            document.getElementById('password').required = true;
            document.getElementById('passwordHint').classList.add('d-none');

            getModal().show();
        }

        function editUser(data, nipValue) {
            document.getElementById('modalTitle').innerText = 'Edit Data Pengguna';
            document.getElementById('formUser').action = `/admin/manajemen-akun/update/${data.id}`;
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

            // Isi Data Form
            document.getElementById('nip').value = nipValue; // <--- Isi NIP
            document.getElementById('name').value = data.name;
            document.getElementById('email').value = data.email;
            document.getElementById('role').value = data.role;

            // Mode Edit: Password Opsional
            document.getElementById('password').value = '';
            document.getElementById('password').required = false;
            document.getElementById('passwordHint').classList.remove('d-none');

            getModal().show();
        }
    </script>
@endpush
