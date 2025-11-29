@extends('layouts.user.app')
@section('title', 'Profil Satuan Kerja')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="flex-grow-1">
                <h2 class="h3 fw-bold text-dark mb-2">Profil Satuan Kerja</h2>
                <p class="text-muted mb-0">Informasi Satuan Kerja Anda</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-building me-2 text-primary"></i>Data Satuan Kerja
                    </h5>
                    @if($satuanKerja)
                        <span class="badge bg-primary">Terverifikasi</span>
                    @else
                        <span class="badge bg-danger">Data Belum Diset</span>
                    @endif
                </div>
                <div class="card-body">
                    {{-- Cek apakah user punya satuan kerja --}}
                    @if($satuanKerja)
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">Nama Satuan Kerja</label>
                                    <div class="info-value bg-light border-0 py-3 px-3 rounded">
                                        <i class="fas fa-building text-muted me-2"></i>
                                        {{ $satuanKerja->nama_satuan_kerja }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">Kode Satuan Kerja</label>
                                    <div class="info-value bg-light border-0 py-3 px-3 rounded">
                                        <i class="fas fa-hashtag text-muted me-2"></i>
                                        {{ $satuanKerja->kode_satuan_kerja }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">Alamat Lengkap</label>
                                    <div class="info-value bg-light border-0 py-3 px-3 rounded">
                                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                        <div>
                                            {{ $satuanKerja->alamat_lengkap }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">Telepon</label>
                                    <div class="info-value bg-light border-0 py-3 px-3 rounded">
                                        <i class="fas fa-phone text-muted me-2"></i>
                                        {{ $satuanKerja->telepon ?? '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">Email</label>
                                    <div class="info-value bg-light border-0 py-3 px-3 rounded">
                                        <i class="fas fa-envelope text-muted me-2"></i>
                                        {{ $satuanKerja->email ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">Website</label>
                                    <div class="info-value bg-light border-0 py-3 px-3 rounded">
                                        <i class="fas fa-globe text-muted me-2"></i>
                                        @if($satuanKerja->website)
                                            <a href="{{ str_starts_with($satuanKerja->website, 'http') ? $satuanKerja->website : 'https://' . $satuanKerja->website }}" target="_blank" class="text-decoration-none text-dark">
                                                {{ $satuanKerja->website }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="form-label text-muted small mb-2 fw-semibold">Kepala Satker</label>
                                    <div class="info-value bg-light border-0 py-3 px-3 rounded">
                                        <i class="fas fa-user-tie text-muted me-2"></i>
                                        <div>
                                            {{ $satuanKerja->kepala_satker ?? '-' }}
                                            @if($satuanKerja->nip_kepala_satker)
                                                <br>
                                                <small class="text-muted">NIP. {{ $satuanKerja->nip_kepala_satker }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Tampilan jika User belum memiliki Satuan Kerja --}}
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3 opacity-50"></i>
                            <h5 class="text-muted">Akun Anda belum terhubung dengan Satuan Kerja manapun.</h5>
                            <p class="text-muted small">Silakan hubungi Administrator untuk memperbarui data unit kerja Anda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <style>
        .info-group {
            margin-bottom: 1rem;
        }

        .info-value {
            background-color: #f8f9fa !important;
            border: 1px solid #e9ecef !important;
            min-height: 48px;
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .info-value:hover {
            background-color: #e9ecef !important;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .info-value {
                min-height: 44px;
                font-size: 0.9rem;
            }

            .page-header .d-flex {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
    
    {{-- Script JS Bawaan --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Profil Satker page loaded');
        });
    </script>
@endsection