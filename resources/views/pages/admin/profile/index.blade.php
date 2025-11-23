@extends('layouts.admin.app')
@section('title', 'Profil Saya')

@section('content')
    {{-- CONTAINER UTAMA (Hapus class content-template) --}}
    <div class="container-fluid p-0">
        
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Profil User</h2>
                    <p class="text-muted mb-0">Kelola informasi profil dan data pribadi Anda</p>
                </div>
            </div>
        </div>

        {{-- Menampilkan Pesan Sukses (Jika ada) --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3 position-relative d-inline-block">
                            {{-- Avatar Placeholder --}}
                            <img src="https://ui-avatars.com/api/?name=User+Pegawai&background=1a73e8&color=fff&size=128" 
                                 alt="User" 
                                 class="rounded-circle img-thumbnail shadow-sm" 
                                 width="128" height="128">
                            
                            {{-- Tombol Ubah Foto (Opsional/Hiasan dulu) --}}
                            <button class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle" 
                                    style="width: 32px; height: 32px; padding: 0;"
                                    title="Ubah Foto">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>

                        <h5 class="fw-bold mb-1">User Pegawai</h5>
                        <p class="text-muted mb-3">NIP. 123456789012345678</p>
                        
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-upload me-2"></i>Upload Foto Baru
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="fw-bold text-muted mb-3">Informasi Akun</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Status</span>
                            <span class="badge bg-success">Aktif</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Terdaftar Sejak</span>
                            <span class="small fw-bold">Januari 2024</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Role</span>
                            <span class="small fw-bold">ASN / Pegawai</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold text-primary">
                            <i class="fas fa-user-edit me-2"></i>Data Pribadi
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        {{-- Form Update --}}
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama" class="form-label fw-bold small">Nama Lengkap</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                            <input type="text" class="form-control border-start-0" id="nama" name="nama" value="User Pegawai">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nip" class="form-label fw-bold small">NIP (Tidak dapat diubah)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-id-card text-muted"></i></span>
                                            <input type="text" class="form-control border-start-0 bg-light" id="nip" name="nip" value="123456789012345678" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-bold small">Alamat Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                            <input type="email" class="form-control border-start-0" id="email" name="email" value="user.pegawai@example.com">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="telepon" class="form-label fw-bold small">No. Telepon / WA</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                            <input type="text" class="form-control border-start-0" id="telepon" name="telepon" value="08123456789">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jabatan" class="form-label fw-bold small">Jabatan Saat Ini</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-briefcase text-muted"></i></span>
                                            <input type="text" class="form-control border-start-0" id="jabatan" name="jabatan" value="Staf Administrasi">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="unit_kerja" class="form-label fw-bold small">Unit Kerja</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-building text-muted"></i></span>
                                            <input type="text" class="form-control border-start-0" id="unit_kerja" name="unit_kerja" value="Dinas Komunikasi dan Informatika">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-control:focus {
            box-shadow: none;
            border-color: #86b7fe;
        }
        .input-group-text {
            background-color: #fff;
        }
    </style>
@endsection