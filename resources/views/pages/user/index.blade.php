@extends('layouts.user.app')
@section('title', 'Home')

@section('content')
    <div class="content-template active" id="dashboard-content">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Dashboard User</h2>

                </div>
            </div>
        </div>

        <!-- Welcome Card -->
        <div class="card welcome-card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="fw-bold mb-2">Halo, User Pegawai!</h3>
                        <p class="mb-0">Selamat datang di sistem PTSP Kepegawaian. Di sini Anda dapat melihat informasi
                            terbaru dan pengumuman penting.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="fas fa-user-circle fa-5x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Terbaru -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Informasi Terbaru</h5>
                <span class="badge bg-primary rounded-pill">4 Baru</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex align-items-start p-4">
                        <div class="flex-shrink-0">
                            <span class="badge bg-warning rounded-circle p-2 me-3">
                                <i class="fas fa-bullhorn text-white"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-bold mb-0">Pengumuman Kenaikan Pangkat Reguler</h6>
                                <span class="badge bg-light text-danger small">Batas: 30 April 2024</span>
                            </div>
                            <p class="small text-muted mb-2">Pengajuan kenaikan pangkat reguler untuk periode 2024. Pastikan
                                semua dokumen lengkap sebelum batas akhir.</p>
                            <a href="#" class="small text-primary fw-semibold">
                                Baca selengkapnya <i class="fas fa-chevron-right ms-1 small"></i>
                            </a>
                        </div>
                    </div>

                    <div class="list-group-item d-flex align-items-start p-4">
                        <div class="flex-shrink-0">
                            <span class="badge bg-success rounded-circle p-2 me-3">
                                <i class="fas fa-chalkboard-teacher text-white"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-bold mb-0">Pelatihan Jabatan Fungsional</h6>
                                <span class="badge bg-light text-danger small">Batas: 25 April 2024</span>
                            </div>
                            <p class="small text-muted mb-2">Program pelatihan untuk meningkatkan kompetensi pegawai dalam
                                jabatan fungsional. Daftar segera sebelum kuota penuh.</p>
                            <a href="#" class="small text-primary fw-semibold">
                                Baca selengkapnya <i class="fas fa-chevron-right ms-1 small"></i>
                            </a>
                        </div>
                    </div>

                    <div class="list-group-item d-flex align-items-start p-4">
                        <div class="flex-shrink-0">
                            <span class="badge bg-info rounded-circle p-2 me-3">
                                <i class="fas fa-user-clock text-white"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-bold mb-0">Jadwal Pensiun 2024</h6>
                                <span class="badge bg-light text-dark small">Semester II 2024</span>
                            </div>
                            <p class="small text-muted mb-2">Daftar pegawai yang akan memasuki masa pensiun pada semester
                                kedua tahun 2024. Periksa dan persiapkan dokumen.</p>
                            <a href="#" class="small text-primary fw-semibold">
                                Baca selengkapnya <i class="fas fa-chevron-right ms-1 small"></i>
                            </a>
                        </div>
                    </div>

                    <div class="list-group-item d-flex align-items-start p-4">
                        <div class="flex-shrink-0">
                            <span class="badge bg-danger rounded-circle p-2 me-3">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-bold mb-0">Pemberitahuan Penting</h6>
                                <span class="badge bg-light text-dark small">15 Mei 2024</span>
                            </div>
                            <p class="small text-muted mb-2">Sistem akan mengalami maintenance pada tanggal 15 Mei 2024
                                pukul 22.00-02.00 WIB. Pastikan semua pekerjaan telah disimpan.</p>
                            <a href="#" class="small text-primary fw-semibold">
                                Baca selengkapnya <i class="fas fa-chevron-right ms-1 small"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light text-center py-3">
                <a href="#" class="text-primary fw-semibold small">Lihat Semua Pengumuman <i
                        class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

@endsection
