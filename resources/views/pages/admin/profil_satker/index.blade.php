@extends('layouts.admin.app')
@section('title', 'Home')

@section('content')
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="flex-grow-1">
                <h2 class="h3 fw-bold text-dark mb-2">Profil Satuan Kerja</h2>
                <p class="text-muted mb-0">Kelola informasi satuan kerja dan data administrasi</p>
            </div>
            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                <div class="alert alert-info alert-dismissible fade show mb-0" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        <div>
                            <strong>Informasi Profil</strong>
                            <span class="ms-2">Pastikan data selalu diperbarui</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="profileActionsDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-cog me-2"></i>Aksi
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileActionsDropdown">
                        <li><a class="dropdown-item" href="#" onclick="editProfile()"><i
                                    class="fas fa-edit me-2"></i>Edit Profil</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportData()"><i
                                    class="fas fa-file-export me-2"></i>Export Data</a></li>
                        <li><a class="dropdown-item" href="#" onclick="backupData()"><i
                                    class="fas fa-database me-2"></i>Backup Data</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#" onclick="generateReport()"><i
                                    class="fas fa-chart-bar me-2"></i>Generate Laporan</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2">Total Pegawai</h6>
                            <h3 class="fw-bold text-primary mb-2">127</h3>
                            <p class="text-muted small mb-0">Aktif</p>
                        </div>
                        <div class="icon-circle bg-primary flex-shrink-0">
                            <i class="fas fa-users text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2">Akan Pensiun</h6>
                            <h3 class="fw-bold text-warning mb-2">8</h3>
                            <p class="text-muted small mb-0">Tahun ini</p>
                        </div>
                        <div class="icon-circle bg-warning flex-shrink-0">
                            <i class="fas fa-user-clock text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2">Pengajuan Bulan Ini</h6>
                            <h3 class="fw-bold text-success mb-2">45</h3>
                            <p class="text-muted small mb-0">Total pengajuan</p>
                        </div>
                        <div class="icon-circle bg-success flex-shrink-0">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2">Rata-rata Proses</h6>
                            <h3 class="fw-bold text-info mb-2">3.2</h3>
                            <p class="text-muted small mb-0">Hari</p>
                        </div>
                        <div class="icon-circle bg-info flex-shrink-0">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Kolom Kiri - Data Satuan Kerja -->
        <div class="col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-building me-2 text-primary"></i>Data Satuan Kerja
                    </h5>
                    <span class="badge bg-primary">Aktif</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="form-label text-muted small mb-2 fw-semibold">Nama Satuan Kerja</label>
                                <div class="info-value bg-light border-0 py-3 px-3 rounded">
                                    <i class="fas fa-building text-muted me-2"></i>
                                    Kantor Kementerian Agama Kota Gorontalo
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="form-label text-muted small mb-2 fw-semibold">Kode Satuan Kerja</label>
                                <div class="info-value bg-light border-0 py-3 px-3 rounded">
                                    <i class="fas fa-hashtag text-muted me-2"></i>
                                    751200
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="info-group">
                                <label class="form-label text-muted small mb-2 fw-semibold">Alamat Lengkap</label>
                                <div class="info-value bg-light border-0 py-3 px-3 rounded">
                                    <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                    Jl. Jenderal Sudirman No. 123, Kota Gorontalo
                                    <br><small class="text-muted ms-4">Kode Pos: 96115</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="form-label text-muted small mb-2 fw-semibold">Telepon</label>
                                <div class="info-value bg-light border-0 py-3 px-3 rounded">
                                    <i class="fas fa-phone text-muted me-2"></i>
                                    (0435) 821234
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="form-label text-muted small mb-2 fw-semibold">Email</label>
                                <div class="info-value bg-light border-0 py-3 px-3 rounded">
                                    <i class="fas fa-envelope text-muted me-2"></i>
                                    kemenag@gorontalo.go.id
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="form-label text-muted small mb-2 fw-semibold">Website</label>
                                <div class="info-value bg-light border-0 py-3 px-3 rounded">
                                    <i class="fas fa-globe text-muted me-2"></i>
                                    kemenag-gorontalo.go.id
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="form-label text-muted small mb-2 fw-semibold">Kepala Satker</label>
                                <div class="info-value bg-light border-0 py-3 px-3 rounded">
                                    <i class="fas fa-user-tie text-muted me-2"></i>
                                    Drs. H. Muhammad Ali, M.Pd
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Info Cards -->
    <div class="row g-3 mt-4">
        <div class="col-md-4">
            <div class="card quick-action-card h-100 border-0 shadow-sm">
                <div class="card-body text-center d-flex flex-column">
                    <div class="quick-action-icon bg-primary mx-auto mb-3 flex-shrink-0">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <h6 class="card-title mb-2">Kelola Pegawai</h6>
                    <p class="text-muted small mb-3 flex-grow-1">Lihat dan kelola data seluruh pegawai satuan kerja</p>
                    <button class="btn btn-outline-primary btn-sm w-100 mt-auto" onclick="manageEmployees()">
                        <i class="fas fa-list me-1"></i> Lihat Daftar
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card quick-action-card h-100 border-0 shadow-sm">
                <div class="card-body text-center d-flex flex-column">
                    <div class="quick-action-icon bg-warning mx-auto mb-3 flex-shrink-0">
                        <i class="fas fa-user-clock text-white"></i>
                    </div>
                    <h6 class="card-title mb-2">Monitor Pensiun</h6>
                    <p class="text-muted small mb-3 flex-grow-1">Pantau jadwal pensiun pegawai secara berkala</p>
                    <button class="btn btn-outline-warning btn-sm w-100 mt-auto" onclick="monitorPensions()">
                        <i class="fas fa-eye me-1"></i> Pantau Sekarang
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card quick-action-card h-100 border-0 shadow-sm">
                <div class="card-body text-center d-flex flex-column">
                    <div class="quick-action-icon bg-success mx-auto mb-3 flex-shrink-0">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <h6 class="card-title mb-2">Laporan Statistik</h6>
                    <p class="text-muted small mb-3 flex-grow-1">Analisis data dan buat laporan statistik kinerja</p>
                    <button class="btn btn-outline-success btn-sm w-100 mt-auto" onclick="generateStatistics()">
                        <i class="fas fa-chart-bar me-1"></i> Buat Laporan
                    </button>
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

            .page-header .flex-shrink-0 {
                width: 100%;
            }
        }
    </style>

    <script>
        function editProfile() {
            showNotification('Membuka editor profil...', 'info');
        }

        function exportData() {
            showNotification('Mengekspor data...', 'info');
            setTimeout(() => {
                showNotification('Data berhasil diekspor', 'success');
            }, 2000);
        }

        function backupData() {
            showNotification('Membackup database...', 'info');
            setTimeout(() => {
                showNotification('Backup berhasil dibuat', 'success');
            }, 2000);
        }

        function generateReport() {
            showNotification('Membuat laporan...', 'info');
            setTimeout(() => {
                showNotification('Laporan berhasil dibuat', 'success');
            }, 2000);
        }

        function systemSettings() {
            showNotification('Membuka pengaturan sistem...', 'info');
        }

        function manageEmployees() {
            showNotification('Membuka manajemen pegawai...', 'info');
        }

        function monitorPensions() {
            showNotification('Membuka monitor pensiun...', 'info');
        }

        function generateStatistics() {
            showNotification('Membuat laporan statistik...', 'info');
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Profil Satker page loaded');
        });
    </script>
@endsection
