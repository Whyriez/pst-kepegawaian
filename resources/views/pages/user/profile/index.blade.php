@extends('layouts.user.app')
@section('title', 'Profil Saya')

@section('content')
    <div class="container-fluid p-0">

        {{-- Header Page --}}
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Profil User</h2>
                    <p class="text-muted mb-0">Kelola informasi profil dan data pribadi Anda</p>
                </div>
            </div>
        </div>

        {{-- Alert Sukses / Error --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Tampilkan semua error validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <h6 class="fw-bold mb-2">Terjadi kesalahan:</h6>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Form Mulai Disini --}}
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Kiri: Foto Profil & Info Singkat --}}
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3 position-relative d-inline-block">
                                {{-- Logic Menampilkan Foto --}}
                                @php
                                    // Cek apakah user punya avatar upload-an sendiri
                                    if ($user->avatar) {
                                        $avatarUrl = asset('storage/' . $user->avatar);
                                    } else {
                                        // Fallback ke UI Avatars
                                        $name = urlencode($user->name);
                                        $avatarUrl = "https://ui-avatars.com/api/?name={$name}&background=1a73e8&color=fff&size=128";
                                    }
                                @endphp

                                <img src="{{ $avatarUrl }}" alt="User Avatar"
                                    class="rounded-circle img-thumbnail shadow-sm object-fit-cover" width="128"
                                    height="128" id="avatar-preview">

                                {{-- Tombol Kamera Kecil (Trigger Upload) --}}
                                <button type="button"
                                    class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle"
                                    style="width: 32px; height: 32px; padding: 0;"
                                    onclick="document.getElementById('avatar-input').click()" title="Ubah Foto">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>

                            <h5 class="fw-bold mb-1">{{ $user->pegawai->nama_lengkap ?? $user->name }}</h5>
                            <p class="text-muted mb-3">NIP. {{ $user->pegawai->nip ?? '-' }}</p>

                            {{-- Input File Hidden --}}
                            <input type="file" name="avatar" id="avatar-input" class="d-none" accept="image/*"
                                onchange="previewImage(event)">
                        </div>
                    </div>

                    {{-- Card Info Readonly --}}
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-body">
                            <h6 class="fw-bold text-muted mb-3">Informasi Akun</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Status</span>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Role</span>
                                <span class="small fw-bold">{{ ucfirst($user->role) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Unit Kerja</span>
                                {{-- Unit Kerja Readonly (Diambil dari relasi user/pegawai) --}}
                                <span class="small fw-bold text-end">
                                    {{ $user->pegawai->satuanKerja->nama_satuan_kerja ?? ($user->satuanKerja->nama_satuan_kerja ?? 'Belum diset') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Form Input Data --}}
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0 fw-bold text-primary">
                                <i class="fas fa-user-edit me-2"></i>Data Pribadi Pegawai
                            </h5>
                        </div>
                        <div class="card-body p-4">

                            {{-- Section 1: Identitas Utama --}}
                            <h6 class="text-muted mb-3 text-uppercase small fw-bold ls-1">Identitas Utama</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama" class="form-label fw-bold small">Nama Lengkap</label>
                                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                            id="nama" name="nama"
                                            value="{{ old('nama', $user->pegawai->nama_lengkap ?? $user->name) }}">
                                        @error('nama')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nip" class="form-label fw-bold small">NIP (Nomor Induk
                                            Pegawai)</label>
                                        {{-- NIP DIBUAT EDITABLE --}}
                                        <input type="number" class="form-control @error('nip') is-invalid @enderror"
                                            id="nip" name="nip"
                                            value="{{ old('nip', $user->pegawai->nip ?? '') }}"
                                            placeholder="Masukkan 18 digit NIP">
                                        @error('nip')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-bold small">Email Login</label>
                                        <input type="email"
                                            class="form-control bg-light @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email', $user->email) }}"
                                            readonly title="Hubungi admin jika ingin ganti email login">
                                        {{-- Email saya buat readonly agar aman, tapi jika ingin diubah hapus 'readonly' --}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="pendidikan_terakhir" class="form-label fw-bold small">Pendidikan
                                            Terakhir</label>
                                        <select class="form-select @error('pendidikan_terakhir') is-invalid @enderror"
                                            name="pendidikan_terakhir">
                                            <option value="">Pilih Pendidikan</option>
                                            @php $pendidikan = ['SMA', 'D3', 'D4', 'S1', 'S2', 'S3']; @endphp
                                            @foreach ($pendidikan as $p)
                                                <option value="{{ $p }}"
                                                    {{ old('pendidikan_terakhir', $user->pegawai->pendidikan_terakhir ?? '') == $p ? 'selected' : '' }}>
                                                    {{ $p }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('pendidikan_terakhir')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small">Nomor Telepon</label>
                                    <input type="number" name="nomor_telepon" class="form-control"
                                           placeholder="Contoh: 081234567890"
                                           value="{{ old('nomor_telepon', $user->nomor_telepon) }}">
                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- Section 2: Data Kelahiran --}}
                            <h6 class="text-muted mb-3 text-uppercase small fw-bold ls-1">Data Kelahiran</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tempat_lahir" class="form-label fw-bold small">Tempat Lahir</label>
                                        <input type="text"
                                            class="form-control @error('tempat_lahir') is-invalid @enderror"
                                            id="tempat_lahir" name="tempat_lahir"
                                            value="{{ old('tempat_lahir', $user->pegawai->tempat_lahir ?? '') }}">
                                        @error('tempat_lahir')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_lahir" class="form-label fw-bold small">Tanggal Lahir</label>
                                        <input type="date"
                                            class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                            id="tanggal_lahir" name="tanggal_lahir" {{-- Kita parse dulu ke Carbon, lalu format ke Y-m-d --}}
                                            value="{{ old('tanggal_lahir', $user->pegawai && $user->pegawai->tanggal_lahir ? \Carbon\Carbon::parse($user->pegawai->tanggal_lahir)->format('Y-m-d') : '') }}">
                                        @error('tanggal_lahir')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- Section 3: Jabatan & Pangkat --}}
                            <h6 class="text-muted mb-3 text-uppercase small fw-bold ls-1">Jabatan & Pangkat</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="jabatan" class="form-label fw-bold small">Jabatan</label>
                                        <input type="text" class="form-control @error('jabatan') is-invalid @enderror"
                                            id="jabatan" name="jabatan"
                                            value="{{ old('jabatan', $user->pegawai->jabatan ?? '') }}">
                                        @error('jabatan')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="pangkat" class="form-label fw-bold small">Pangkat</label>
                                        <input type="text" class="form-control @error('pangkat') is-invalid @enderror"
                                            id="pangkat" name="pangkat" placeholder="Contoh: Penata Muda"
                                            value="{{ old('pangkat', $user->pegawai->pangkat ?? '') }}">
                                        @error('pangkat')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="golongan_ruang" class="form-label fw-bold small">Gol. Ruang</label>
                                        <select class="form-select @error('golongan_ruang') is-invalid @enderror"
                                            name="golongan_ruang">
                                            <option value="">Pilih Golongan</option>
                                            {{-- List Contoh Golongan --}}
                                            @foreach (['II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b'] as $gol)
                                                <option value="{{ $gol }}"
                                                    {{ old('golongan_ruang', $user->pegawai->golongan_ruang ?? '') == $gol ? 'selected' : '' }}>
                                                    {{ $gol }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('golongan_ruang')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Simpan --}}
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        .form-control:focus,
        .form-select:focus {
            box-shadow: none;
            border-color: #86b7fe;
        }

        .object-fit-cover {
            object-fit: cover;
        }
    </style>

    {{-- Script Preview Image saat file dipilih --}}
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('avatar-preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
