@extends('layouts.user.app')
@section('title', 'Home')

@section('content')
    <div class="content-template active" id="dashboard-content">
        <div class="page-header mb-4">
            <h2 class="h3 fw-bold text-dark mb-1">Dashboard User</h2>
        </div>

        <div class="card welcome-card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="fw-bold mb-2">Halo, {{ Auth::user()->name }}</h3>
                        <p class="mb-0">Selamat datang. Berikut adalah jadwal layanan kepegawaian yang akan segera berakhir.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="fas fa-user-circle fa-5x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Informasi Terbaru</h5>
                <span class="badge bg-danger rounded-pill">{{ $detailPeriods->count() }} Segera Berakhir</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($detailPeriods as $periode)
                        <div class="list-group-item d-flex align-items-start p-4">
                            <div class="flex-shrink-0">
                                <span class="badge bg-warning rounded-circle p-2 me-3">
                                    <i class="fas fa-bullhorn text-white"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="fw-bold mb-0">{{ $periode->jenisLayanan->nama_layanan }}</h6>

                                    {{-- Tidak perlu cek unlimited, langsung tampilkan batas --}}
                                    @php
                                        $daysLeft = now()->diffInDays($periode->tanggal_selesai, false);
                                        $badgeColor = $daysLeft < 7 ? 'danger' : 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }} small">
                                        Batas: {{ $periode->tanggal_selesai->translatedFormat('d F Y') }}
                                    </span>
                                </div>
                                <p class="small text-muted mb-2">
                                    Periode <strong>{{ $periode->nama_periode }}</strong>.
                                    Segera lengkapi dokumen sebelum ditutup.
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-5">
                            <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" alt="Empty" width="150" class="opacity-50 mb-3">
                            <p class="text-muted">Tidak ada periode layanan mendesak saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL POPUP (Hanya yang punya deadline) --}}
    @if(session('show_periode_modal') && isset($groupedPeriods) && $groupedPeriods->count() > 0)
        <div class="modal fade" id="periodeModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg"
                     style="background: linear-gradient(135deg, #ffffff 0%, #f3f4f6 100%);">

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger">
                            <i class="fas fa-clock me-2"></i> Peringatan Batas Waktu!
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted text-center mb-4">
                            Halo <strong>{{ Auth::user()->name }}</strong>, layanan berikut memiliki batas waktu pengajuan.
                            <br>Jangan sampai terlambat!
                        </p>

                        <div class="row g-3 justify-content-center">
                            @foreach($groupedPeriods as $key => $group)
                                @php
                                    $p = $group->first();
                                    $jumlahLayanan = $group->count();
                                @endphp
                                <div class="col-md-6">
                                    <div class="card h-100 border shadow-sm">
                                        <div class="card-body text-center p-3">

                                            <h5 class="fw-bold text-primary mb-1">{{ $p->jenisLayanan->kategori }}</h5>
                                            <small class="text-muted d-block mb-2">{{ $p->nama_periode }}</small>

                                            <div class="badge bg-light text-dark mb-3 border">
                                                {{ $jumlahLayanan }} Layanan Terkait
                                            </div>

                                            {{-- PASTI ADA DEADLINE (Karena unlimited difilter controller) --}}
                                            <div class="badge bg-danger mb-3 d-block">
                                                Batas: {{ $p->tanggal_selesai->translatedFormat('d F Y') }}
                                            </div>

                                            <div class="d-flex justify-content-center gap-2 countdown-timer"
                                                 data-deadline="{{ $p->tanggal_selesai->format('Y-m-d') }} 23:59:59">

                                                <div class="text-center bg-dark text-white rounded p-2" style="min-width: 50px;">
                                                    <span class="days fw-bold fs-5 d-block">0</span>
                                                    <small style="font-size: 0.6rem; text-transform: uppercase;">Hari</small>
                                                </div>
                                                <div class="text-center bg-dark text-white rounded p-2" style="min-width: 50px;">
                                                    <span class="hours fw-bold fs-5 d-block">0</span>
                                                    <small style="font-size: 0.6rem; text-transform: uppercase;">Jam</small>
                                                </div>
                                                <div class="text-center bg-dark text-white rounded p-2" style="min-width: 50px;">
                                                    <span class="minutes fw-bold fs-5 d-block">0</span>
                                                    <small style="font-size: 0.6rem; text-transform: uppercase;">Mnt</small>
                                                </div>
                                                <div class="text-center bg-warning text-dark rounded p-2" style="min-width: 50px;">
                                                    <span class="seconds fw-bold fs-5 d-block">0</span>
                                                    <small style="font-size: 0.6rem; text-transform: uppercase;">Dtk</small>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="modal-footer border-0 justify-content-center pb-4">
                        <button type="button" class="btn btn-primary px-5 rounded-pill shadow-sm" data-bs-dismiss="modal">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modalEl = document.getElementById('periodeModal');
                if (modalEl) {
                    var myModal = new bootstrap.Modal(modalEl);
                    myModal.show();
                }

                const timers = document.querySelectorAll('.countdown-timer');
                timers.forEach(timer => {
                    const deadlineStr = timer.getAttribute('data-deadline');
                    // Karena sudah pasti ada deadline, kita langsung eksekusi
                    const deadline = new Date(deadlineStr).getTime();

                    const interval = setInterval(function () {
                        const now = new Date().getTime();
                        const distance = deadline - now;

                        if (distance < 0) {
                            clearInterval(interval);
                            timer.innerHTML = '<div class="alert alert-secondary w-100 py-1 m-0">Waktu Habis</div>';
                            return;
                        }

                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        if(timer.querySelector('.days')) timer.querySelector('.days').innerText = days;
                        if(timer.querySelector('.hours')) timer.querySelector('.hours').innerText = hours;
                        if(timer.querySelector('.minutes')) timer.querySelector('.minutes').innerText = minutes;
                        if(timer.querySelector('.seconds')) timer.querySelector('.seconds').innerText = seconds;

                    }, 1000);
                });
            });
        </script>
    @endif
@endsection
