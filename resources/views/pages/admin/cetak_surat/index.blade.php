@extends('layouts.admin.app')
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
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-primary-soft text-primary mb-3 mx-auto">
                            <i class="fas fa-envelope-open-text fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold">SPTJM</h5>
                        <p class="text-muted small mb-4">Cetak surat pernyataan tanggung jawab mutlak</p>
                        <button class="btn btn-outline-primary w-100" onclick="cetakSurat('Surat Pernyataan Tanggung Jawab Mutlak')">
                            <i class="fas fa-print me-2"></i>Cetak Surat
                        </button>
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