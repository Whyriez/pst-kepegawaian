@extends('layouts.user.app')
@section('title', 'Cetak Surat')

@section('content')
    {{-- HAPUS CLASS content-template AGAR LANGSUNG MUNCUL --}}
    <div class="container-fluid p-0">

        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Cetak Surat</h2>
                    <p class="text-muted mb-0">Generator surat dan dokumen resmi kepegawaian</p>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- 1. Surat Pengantar --}}
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-primary-soft text-primary mb-3 mx-auto">
                            <i class="fas fa-envelope-open-text fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold">Surat Pengantar</h5>
                        <p class="text-muted small mb-4">Cetak surat pengantar untuk berbagai keperluan administrasi ke dinas lain.</p>
                        <button class="btn btn-outline-primary w-100" onclick="cetakSurat('Surat Pengantar')">
                            <i class="fas fa-print me-2"></i>Cetak Surat
                        </button>
                    </div>
                </div>
            </div>

            {{-- 2. Surat Keterangan --}}
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-success-soft text-success mb-3 mx-auto">
                            <i class="fas fa-file-contract fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold">Surat Keterangan</h5>
                        <p class="text-muted small mb-4">Cetak surat keterangan aktif bekerja, keterangan penghasilan, dll.</p>
                        <button class="btn btn-outline-success w-100" onclick="cetakSurat('Surat Keterangan')">
                            <i class="fas fa-print me-2"></i>Cetak Surat
                        </button>
                    </div>
                </div>
            </div>

            {{-- 3. Surat Cuti --}}
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-warning-soft text-warning mb-3 mx-auto">
                            <i class="fas fa-plane-departure fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold">Formulir Cuti</h5>
                        <p class="text-muted small mb-4">Cetak formulir pengajuan cuti tahunan, sakit, atau alasan penting.</p>
                        <button class="btn btn-outline-warning w-100" onclick="cetakSurat('Formulir Cuti')">
                            <i class="fas fa-print me-2"></i>Cetak Surat
                        </button>
                    </div>
                </div>
            </div>

            {{-- 4. Surat Pernyataan --}}
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-info-soft text-info mb-3 mx-auto">
                            <i class="fas fa-file-signature fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold">Surat Pernyataan</h5>
                        <p class="text-muted small mb-4">Cetak template surat pernyataan melaksanakan tugas, dll.</p>
                        <button class="btn btn-outline-info w-100" onclick="cetakSurat('Surat Pernyataan')">
                            <i class="fas fa-print me-2"></i>Cetak Surat
                        </button>
                    </div>
                </div>
            </div>

            {{-- 5. Riwayat Hidup --}}
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-danger-soft text-danger mb-3 mx-auto">
                            <i class="fas fa-address-card fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold">Daftar Riwayat Hidup</h5>
                        <p class="text-muted small mb-4">Cetak DRH terbaru berdasarkan data profil kepegawaian.</p>
                        <button class="btn btn-outline-danger w-100" onclick="cetakSurat('Daftar Riwayat Hidup')">
                            <i class="fas fa-print me-2"></i>Cetak DRH
                        </button>
                    </div>
                </div>
            </div>

            {{-- 6. Lainnya --}}
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-secondary-soft text-secondary mb-3 mx-auto">
                            <i class="fas fa-folder-open fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold">Dokumen Lainnya</h5>
                        <p class="text-muted small mb-4">Cetak dokumen kepegawaian umum lainnya.</p>
                        <button class="btn btn-outline-secondary w-100" onclick="cetakSurat('Dokumen Lainnya')">
                            <i class="fas fa-print me-2"></i>Lihat Daftar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .icon-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Soft Background Colors */
        .bg-primary-soft { background-color: rgba(13, 110, 253, 0.1); }
        .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
        .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
        .bg-info-soft { background-color: rgba(13, 202, 240, 0.1); }
        .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
        .bg-secondary-soft { background-color: rgba(108, 117, 125, 0.1); }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Cetak Surat Page Initialized');
        });

        function cetakSurat(jenisSurat) {
            // Simulasi proses generate surat
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang menyiapkan ' + jenisSurat,
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                timer: 1500,
                willOpen: () => {
                    Swal.showLoading();
                }
            }).then(() => {
                Swal.fire({
                    title: 'Berhasil!',
                    text: jenisSurat + ' berhasil di-generate dan siap diunduh.',
                    icon: 'success',
                    confirmButtonText: 'Unduh PDF',
                    showCancelButton: true,
                    cancelButtonText: 'Tutup'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Di sini nanti logika download file
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        
                        Toast.fire({
                            icon: 'success',
                            title: 'Mulai mengunduh file...'
                        });
                    }
                });
            });
        }
    </script>
@endsection