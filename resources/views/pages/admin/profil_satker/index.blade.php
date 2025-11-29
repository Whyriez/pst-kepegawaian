@extends('layouts.admin.app')
@section('title', 'Profil Satuan Kerja')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="flex-grow-1">
                <h2 class="h3 fw-bold text-dark mb-2">Profil Satuan Kerja</h2>
                <p class="text-muted mb-0">Kelola informasi satuan kerja dan data administrasi</p>
            </div>
        </div>
    </div>

    {{-- Alert Sukses --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Alert Error --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('admin.profil_satker.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-building me-2 text-primary"></i>Data Satuan Kerja
                        </h5>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">Nama Satuan Kerja</label>
                                    <input type="text" class="form-control" name="nama_satuan_kerja" 
                                        value="{{ old('nama_satuan_kerja', $satuanKerja->nama_satuan_kerja) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">Kode Satuan Kerja</label>
                                    <input type="text" class="form-control" name="kode_satuan_kerja" 
                                        value="{{ old('kode_satuan_kerja', $satuanKerja->kode_satuan_kerja) }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">Alamat Lengkap</label>
                                    <textarea class="form-control" name="alamat_lengkap" rows="3" required>{{ old('alamat_lengkap', $satuanKerja->alamat_lengkap) }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">Telepon</label>
                                    <input type="text" class="form-control" name="telepon" 
                                        value="{{ old('telepon', $satuanKerja->telepon) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">Email</label>
                                    <input type="email" class="form-control" name="email" 
                                        value="{{ old('email', $satuanKerja->email) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">Website</label>
                                    <input type="text" class="form-control" name="website" 
                                        value="{{ old('website', $satuanKerja->website) }}">
                                </div>
                            </div>

                            <div class="col-12"><hr></div>
                            <h6 class="fw-bold text-primary mb-3">Informasi Pimpinan</h6>

                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">Nama Kepala Satker</label>
                                    <input type="text" class="form-control" name="kepala_satker" 
                                        value="{{ old('kepala_satker', $satuanKerja->kepala_satker) }}" required>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">NIP Kepala Satker</label>
                                    <input type="number" class="form-control" name="nip_kepala_satker" 
                                        value="{{ old('nip_kepala_satker', $satuanKerja->nip_kepala_satker) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <style>
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
@endsection