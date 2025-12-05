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
                        <h3 class="fw-bold mb-2">Halo, {{ Auth::user()->name }}</h3>
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
                            <p class="small text-muted mb-2">Pengajuan kenaikan pangkat reguler untuk periode 2024.
                                Pastikan
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
                            <p class="small text-muted mb-2">Program pelatihan untuk meningkatkan kompetensi pegawai
                                dalam
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
                            <p class="small text-muted mb-2">Daftar pegawai yang akan memasuki masa pensiun pada
                                semester
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

    @if(session('show_periode_modal') && isset($activePeriods) && $activePeriods->count() > 0)
        <div class="modal fade" id="periodeModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg"
                     style="background: linear-gradient(135deg, #ffffff 0%, #f3f4f6 100%);">

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger">
                            <i class="fas fa-clock me-2"></i> Periode Pengajuan Sedang Dibuka!
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted text-center mb-4">
                            Halo <strong>{{ Auth::user()->name }}</strong>, sistem mendeteksi ada layanan kepegawaian
                            yang sedang dibuka.
                            <br>Segera lengkapi berkas Anda sebelum waktu habis!
                        </p>

                        <div class="row g-3 justify-content-center">
                            @foreach($activePeriods as $periode)
                                <div class="col-md-6">
                                    <div class="card h-100 border shadow-sm">
                                        <div class="card-body text-center p-3">
                                            {{-- Judul Layanan --}}
                                            <h6 class="fw-bold text-dark mb-1">{{ $periode->jenisLayanan->nama_layanan }}</h6>
                                            <small class="text-muted d-block mb-2">{{ $periode->nama_periode }}</small>

                                            {{-- Batas Waktu --}}
                                            <div class="badge bg-danger mb-3">
                                                Batas: {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->translatedFormat('d F Y') }}
                                            </div>

                                            {{-- Timer Container --}}
                                            <div class="d-flex justify-content-center gap-2 countdown-timer"
                                                 {{-- PERBAIKAN DISINI: Tambahkan ->format('Y-m-d') --}}
                                                 data-deadline="{{ $periode->tanggal_selesai->format('Y-m-d') }} 23:59:59">

                                                <div class="text-center bg-dark text-white rounded p-2"
                                                     style="min-width: 50px;">
                                                    <span class="days fw-bold fs-5 d-block">0</span>
                                                    <small
                                                        style="font-size: 0.6rem; text-transform: uppercase;">Hari</small>
                                                </div>
                                                <div class="text-center bg-dark text-white rounded p-2"
                                                     style="min-width: 50px;">
                                                    <span class="hours fw-bold fs-5 d-block">0</span>
                                                    <small
                                                        style="font-size: 0.6rem; text-transform: uppercase;">Jam</small>
                                                </div>
                                                <div class="text-center bg-dark text-white rounded p-2"
                                                     style="min-width: 50px;">
                                                    <span class="minutes fw-bold fs-5 d-block">0</span>
                                                    <small
                                                        style="font-size: 0.6rem; text-transform: uppercase;">Mnt</small>
                                                </div>
                                                <div class="text-center bg-warning text-dark rounded p-2"
                                                     style="min-width: 50px;">
                                                    <span class="seconds fw-bold fs-5 d-block">0</span>
                                                    <small
                                                        style="font-size: 0.6rem; text-transform: uppercase;">Dtk</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="modal-footer border-0 justify-content-center pb-4">
                        <button type="button" class="btn btn-primary px-5 rounded-pill shadow-sm"
                                data-bs-dismiss="modal">
                            Saya Mengerti, Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // 1. Tampilkan Modal Secara Otomatis
                var modalEl = document.getElementById('periodeModal');
                if (modalEl) {
                    var myModal = new bootstrap.Modal(modalEl);
                    myModal.show();
                }

                // 2. Logika Hitung Mundur (Countdown)
                const timers = document.querySelectorAll('.countdown-timer');

                timers.forEach(timer => {
                    // Ambil waktu deadline dari atribut data-deadline
                    const deadlineStr = timer.getAttribute('data-deadline');
                    const deadline = new Date(deadlineStr).getTime();

                    // Update hitungan setiap 1 detik
                    const interval = setInterval(function () {
                        const now = new Date().getTime();
                        const distance = deadline - now;

                        // Jika waktu habis
                        if (distance < 0) {
                            clearInterval(interval);
                            timer.innerHTML = '<div class="alert alert-secondary w-100 py-1 m-0">Waktu Habis</div>';
                            return;
                        }

                        // Kalkulasi Waktu
                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        // Update HTML
                        timer.querySelector('.days').innerText = days;
                        timer.querySelector('.hours').innerText = hours;
                        timer.querySelector('.minutes').innerText = minutes;
                        timer.querySelector('.seconds').innerText = seconds;

                    }, 1000);
                });
            });
        </script>
    @endif
@endsection
