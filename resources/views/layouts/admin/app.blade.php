<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - PTSP Gorontalo')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style-admin.css') }}">
    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 60px;
            --primary-color: #435ebe;
            --secondary-color: #6c757d;
            --bg-light: #f2f7ff;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        /* LAYOUT WRAPPER */
        #app {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        #sidebar {
            width: var(--sidebar-width);
            background-color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            overflow-y: auto;
        }

        #sidebar.active {
            margin-left: calc(var(--sidebar-width) * -1);
        }

        /* MAIN CONTENT */
        #main {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        #main.active {
            margin-left: 0;
        }

        /* HEADER / TOPBAR */
        header {
            height: var(--header-height);
            background-color: #fff;
            display: flex;
            align-items: center;
            padding: 0 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 99;
        }

        /* CONTENT AREA */
        .page-content {
            padding: 2rem;
            flex: 1;
        }

        /* FOOTER */
        footer {
            padding: 1rem 2rem;
            background-color: #fff;
            text-align: center;
            font-size: 0.875rem;
            color: #6c757d;
        }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            #sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
            }

            #sidebar.active {
                margin-left: 0;
            }

            #main {
                margin-left: 0;
            }

            /* Overlay for mobile sidebar */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 99;
                display: none;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        /* UTILITIES */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 25px 0 rgba(169, 169, 169, 0.1);
            margin-bottom: 1.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #2f48a0;
            border-color: #2f48a0;
        }
    </style>
</head>

<body>
    <div id="app">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        @include('layouts.admin.partials.sidebar')

        <div id="main">
            <header>
                <button class="btn btn-light border-0 me-3" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <h5 class="mb-0 fw-bold text-primary d-none d-md-block">Panel Administrator</h5>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <a href="#" class="text-secondary position-relative" data-bs-toggle="dropdown">
                            <i class="fas fa-bell fa-lg"></i>
                            <span
                                class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">New alerts</span>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                            <li>
                                <h6 class="dropdown-header">Notifikasi</h6>
                            </li>
                            <li><a class="dropdown-item small" href="#">Pengajuan baru dari Ahmad</a></li>
                            <li><a class="dropdown-item small" href="#">Berkas BUP perlu verifikasi</a></li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                            data-bs-toggle="dropdown">
                            <div class="me-2 text-end d-none d-sm-block">
                                <small class="d-block fw-bold text-dark">{{ Auth::user()->name ?? 'Admin' }}</small>
                                <small class="d-block text-muted" style="font-size: 10px;">Administrator</small>
                            </div>
                            <img src="https://ui-avatars.com/api/?name=Admin&background=435ebe&color=fff" alt="Admin"
                                class="rounded-circle" width="36" height="36">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profil Saya</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <div class="page-content">
                @yield('content')
            </div>

            <footer>
                &copy; {{ date('Y') }} PTSP Kemenag Gorontalo. Developed with <i
                    class="fas fa-heart text-danger"></i> by UNG Students.
            </footer>
        </div>
    </div>

    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Tolak Pengajuan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori Penolakan</label>
                            <select class="form-select" id="rejectCategory" required>
                                <option value="" selected disabled>Pilih alasan...</option>
                                <option value="dokumen_tidak_lengkap">Dokumen Tidak Lengkap</option>
                                <option value="data_tidak_valid">Data Tidak Valid / Tidak Sesuai</option>
                                <option value="masa_kerja_kurang">Masa Kerja Belum Mencukupi</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan Perbaikan</label>
                            <textarea class="form-control" id="rejectReason" rows="4"
                                placeholder="Jelaskan detail kekurangan dokumen atau data..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="submitRejection()">Kirim Penolakan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="height: 90vh;">
                <div class="modal-header bg-white border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark" id="previewTitle">
                        Preview Berkas
                    </h5>
                    <div class="ms-auto d-flex gap-2">
                        <a href="#" id="btnDownloadFile" target="_blank"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-1"></i> Download File Ini
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                <div class="modal-body p-0">
                    <div class="row h-100 m-0">

                        <div class="col-lg-3 col-md-4 border-end bg-white p-0 d-flex flex-column">
                            <div class="p-3 bg-light border-bottom">
                                <small class="text-uppercase text-muted fw-bold"
                                    style="font-size: 0.7rem; letter-spacing: 1px;">Daftar Dokumen</small>
                            </div>
                            <div class="list-group list-group-flush overflow-auto flex-grow-1" id="fileListContainer">
                            </div>
                        </div>

                        <div
                            class="col-lg-9 col-md-8 bg-secondary bg-opacity-10 p-0 position-relative d-flex align-items-center justify-content-center">

                            <div id="pdfPlaceholder" class="text-center text-muted p-5">
                                <div class="mb-3">
                                    <span class="fa-stack fa-3x">
                                        <i class="fas fa-circle fa-stack-2x text-white"></i>
                                        <i class="fas fa-file-alt fa-stack-1x text-secondary"></i>
                                    </span>
                                </div>
                                <h6 class="fw-bold text-dark">Pilih Dokumen</h6>
                                <p class="small">Silakan pilih salah satu dokumen di panel kiri untuk melihat preview.
                                </p>
                            </div>

                            <embed id="pdfViewer" type="application/pdf" style="display:none; width:100%; height:500px;">

                            <img id="imageViewer" src="" class="img-fluid shadow-sm rounded border bg-white"
                                style="display: none; max-height: 90%; max-width: 90%; object-fit: contain;">

                            <div id="unsupportedFormat" class="text-center text-muted p-5" style="display: none;">
                                <div class="mb-3">
                                    <span class="fa-stack fa-3x">
                                        <i class="fas fa-circle fa-stack-2x text-white"></i>
                                        <i class="fas fa-eye-slash fa-stack-1x text-danger"></i>
                                    </span>
                                </div>
                                <h6 class="fw-bold text-dark">Preview Tidak Tersedia</h6>
                                <p class="small">Format file ini tidak dapat ditampilkan langsung di sini.</p>
                                <p class="small">Silakan gunakan tombol download di atas.</p>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <small class="text-muted me-auto fst-italic"><i class="fas fa-info-circle me-1"></i> Pastikan
                        dokumen terbaca dengan jelas sebelum menyetujui.</small>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Toggle Sidebar Mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay'); // Sesuaikan ID overlay

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                if (overlay) overlay.classList.toggle('show');

                // Jika di mobile, kunci scroll body saat sidebar terbuka
                if (window.innerWidth <= 992) {
                    document.body.classList.toggle('overflow-hidden');
                }
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            if (overlay) {
                overlay.addEventListener('click', toggleSidebar);
            }

            // 2. Dropdown Menu Logic (FIXED)
            // Target elemen <a> yang merupakan anak langsung dari .has-submenu
            const menuTriggers = document.querySelectorAll('.has-submenu > .menu-item');

            menuTriggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault(); // Mencegah navigasi

                    // Ambil parent <li>
                    const parentLi = this.parentElement;

                    // Cek apakah menu ini sedang aktif/terbuka
                    const isOpen = parentLi.classList.contains('active') && parentLi.classList
                        .contains('open');

                    // Tutup semua menu lain (Accordion Effect - Opsional, hapus loop ini jika ingin multi-open)
                    /*
                    document.querySelectorAll('.has-submenu').forEach(item => {
                        if(item !== parentLi) {
                            item.classList.remove('active', 'open');
                            const sub = item.querySelector('.submenu');
                            if(sub) sub.style.display = 'none';
                        }
                    });
                    */

                    // Toggle menu yang diklik
                    if (isOpen) {
                        parentLi.classList.remove('active', 'open');
                        const submenu = parentLi.querySelector('.submenu');
                        if (submenu) $(submenu).slideUp(
                            300); // Jika pakai jQuery, atau gunakan style.display manual
                        // Manual JS slide up
                        if (submenu) submenu.style.display = 'none';
                    } else {
                        parentLi.classList.add('active', 'open');
                        const submenu = parentLi.querySelector('.submenu');
                        // Manual JS slide down
                        if (submenu) submenu.style.display = 'block';
                    }
                });
            });

            // 3. Search Sidebar
            const searchInput = document.getElementById('sidebarSearch');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const value = this.value.toLowerCase();
                    const menuItems = document.querySelectorAll('#mainMenu > li');

                    menuItems.forEach(item => {
                        // Skip footer/search items if any
                        if (item.classList.contains('sidebar-search') || item.querySelector(
                                '.user-mini')) return;

                        const text = item.textContent.toLowerCase();
                        if (text.indexOf(value) > -1) {
                            item.style.display = "";
                        } else {
                            item.style.display = "none";
                        }
                    });
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
