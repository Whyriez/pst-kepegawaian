@extends('layouts.admin.app')
@section('title', 'Beranda')

@section('content')
    {{-- Header Halaman (Mobile Friendly) --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6 col-12 mb-3 mb-md-0">
                <h2 class="h3 fw-bold text-dark mb-1">Dasbor Admin</h2>
                <p class="text-muted mb-0 small-on-mobile">
                    <i class="fas fa-calendar me-1"></i> {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    <span class="d-none d-sm-inline mx-2">•</span>
                    <span class="d-block d-sm-inline mt-1 mt-sm-0">
                         <i class="fas fa-clock me-1"></i> <span id="liveClock">{{ date('H:i:s') }}</span>
                    </span>
                </p>
            </div>
            <div class="col-md-6 col-12 text-md-end text-start">
                <div class="d-inline-flex align-items-center gap-3 bg-white px-3 py-2 rounded shadow-sm border">
                    <div class="text-end">
                        <small class="text-muted d-block" style="font-size: 0.7rem;">STATUS SYSTEM</small>
                        <span class="badge bg-success bg-opacity-10 text-success"><i class="fas fa-circle me-1" style="font-size: 6px;"></i> Online</span>
                    </div>
                    <div class="vr"></div>
                    <div class="text-end">
                        <small class="text-muted d-block" style="font-size: 0.7rem;">PENGGUNA AKTIF</small>
                        <span class="fw-bold text-primary">{{ $stats['user_active'] }} User</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KARTU STATISTIK UTAMA (REVISI: 1 Kolom di HP) --}}
    <div class="row g-3 mb-4">
        {{-- Card 1 --}}
        {{-- UBAHAN: col-12 (HP), col-sm-6 (Tablet Kecil), col-xl-3 (Desktop) --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div>
                            <h6 class="card-title text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.75rem;">Berkas Baru</h6>
                            <h3 class="fw-bold text-dark mb-0">{{ $stats['berkas_baru'] }}</h3>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                            <i class="fas fa-inbox fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2">Hari Ini</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div>
                            <h6 class="card-title text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.75rem;">Menunggu</h6>
                            <h3 class="fw-bold text-warning mb-0">{{ $stats['menunggu'] }}</h3>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2">Perlu Verifikasi</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3 --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div>
                            <h6 class="card-title text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.75rem;">Disetujui</h6>
                            <h3 class="fw-bold text-success mb-0">{{ $stats['disetujui'] }}</h3>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success rounded-3 p-3">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Bulan Ini</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4 --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div>
                            <h6 class="card-title text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.75rem;">Ditolak</h6>
                            <h3 class="fw-bold text-danger mb-0">{{ $stats['ditolak'] }}</h3>
                        </div>
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                            <i class="fas fa-times-circle fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2">Bulan Ini</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN GRAFIK --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;"><i class="fas fa-chart-line me-2 text-primary"></i>Tren Pengajuan</h5>
                    <small class="text-muted">30 Hari Terakhir</small>
                </div>
                <div class="card-body pt-0">
                    <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                        <canvas id="submissionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;"><i class="fas fa-chart-pie me-2 text-success"></i>Jenis Layanan</h5>
                </div>
                <div class="card-body pt-0 d-flex align-items-center justify-content-center">
                    <div class="chart-container" style="position: relative; height: 280px; width: 100%;">
                        <canvas id="typeDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- SIDEBAR KANAN (Kalender & Ringkasan) - Di Mobile turun ke bawah --}}
        <div class="col-xl-4 order-xl-2">
            <div class="row g-3">
                <div class="col-md-6 col-xl-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <h6 class="text-uppercase text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Kalender Hari Ini</h6>
                            <h2 class="fw-bold text-dark display-4 mb-0">{{ \Carbon\Carbon::now()->format('d') }}</h2>
                            <h5 class="text-primary mb-0">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM Y') }}</h5>
                            <div class="mt-3 pt-3 border-top">
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- KARTU RINGKASAN --}}
                <div class="col-md-6 col-xl-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0"><i class="fas fa-tachometer-alt me-2 text-success"></i>Ringkasan Total</h6>
                        </div>
                        <div class="card-body pt-0">
                            <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded bg-light">
                                <span class="text-muted small">Total Pengajuan</span>
                                <span class="fw-bold text-primary">{{ $stats['total_pengajuan'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded bg-light">
                                <span class="text-muted small">Sedang Diproses</span>
                                <span class="fw-bold text-warning">{{ $stats['menunggu'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                <span class="text-muted small">Selesai Bulan Ini</span>
                                <span class="fw-bold text-success">{{ $stats['disetujui'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL AKTIVITAS TERBARU --}}
        <div class="col-xl-8 order-xl-1">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;"><i class="fas fa-history me-2 text-primary"></i>Aktivitas Terbaru</h5>
                    <a href="#" class="btn btn-sm btn-light">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="min-width: 600px;">
                            <thead class="bg-light">
                            <tr>
                                <th class="border-0 ps-4 py-3 text-muted text-uppercase small fw-bold">Waktu</th>
                                <th class="border-0 py-3 text-muted text-uppercase small fw-bold">Layanan</th>
                                <th class="border-0 py-3 text-muted text-uppercase small fw-bold">Pegawai</th>
                                <th class="border-0 py-3 text-muted text-uppercase small fw-bold">Status</th>
                                <th class="border-0 py-3 text-center text-muted text-uppercase small fw-bold">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($recentActivities as $item)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark d-block">{{ $item->updated_at->format('H:i') }}</span>
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $item->updated_at->format('d M') }}</small>
                                    </td>
                                    <td>
                                        <span class="d-block text-truncate" style="max-width: 150px;" title="{{ $item->jenisLayanan->nama_layanan ?? '-' }}">
                                            {{ $item->jenisLayanan->nama_layanan ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($item->pegawai->nama_lengkap) }}&background=random" class="rounded-circle me-2 d-none d-md-block" width="30" height="30">
                                            <div>
                                                <span class="d-block text-dark fw-bold text-truncate" style="max-width: 130px;">{{ $item->pegawai->nama_lengkap }}</span>
                                                <small class="text-muted d-block" style="font-size: 0.7rem;">{{ $item->pegawai->nip }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($item->status == 'pending')
                                            <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1">Menunggu</span>
                                        @elseif($item->status == 'disetujui')
                                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">Disetujui</span>
                                        @elseif($item->status == 'ditolak')
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">Ditolak</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $route = match($item->jenisLayanan->slug) {
                                                'kp-fungsional' => route('admin.kp.fungsional'),
                                                'kp-struktural' => route('admin.kp.struktural'),
                                                'kp-reguler' => route('admin.kp.reguler'),
                                                'kp-penyesuaian-ijazah' => route('admin.kp.penyesuaian_ijazah'),
                                                default => '#'
                                            };
                                        @endphp
                                        <a href="{{ $route }}?search={{ $item->pegawai->nip }}" class="btn btn-sm btn-light text-primary rounded-circle shadow-sm" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-chevron-right" style="font-size: 0.8rem;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada aktivitas terbaru.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // --- JAM DIGITAL ---
        setInterval(() => {
            const now = new Date();
            const el = document.getElementById('liveClock');
            if(el) el.textContent = now.toLocaleTimeString('id-ID');
        }, 1000);

        // --- SETUP CHART ---
        document.addEventListener('DOMContentLoaded', function() {
            // Setup Responsive Font
            Chart.defaults.font.family = "'Nunito', sans-serif";
            Chart.defaults.color = '#6c757d';

            const lineLabels = @json($chartData->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->locale('id')->isoFormat('D MMM')));
            const lineTotal = @json($chartData->pluck('total'));
            const lineApproved = @json($chartData->pluck('disetujui'));

            const pieLabels = @json($pieData->pluck('nama_layanan'));
            const pieValues = @json($pieData->pluck('total'));

            const lineCtx = document.getElementById('submissionChart');
            if (lineCtx) {
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: lineLabels,
                        datasets: [
                            {
                                label: 'Masuk',
                                data: lineTotal,
                                borderColor: '#435ebe', // Primary Color
                                backgroundColor: 'rgba(67, 94, 190, 0.05)',
                                borderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                tension: 0.4, fill: true
                            },
                            {
                                label: 'Disetujui',
                                data: lineApproved,
                                borderColor: '#57caeb',
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                pointRadius: 3,
                                tension: 0.4, fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // PENTING UNTUK MOBILE
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                mode: 'index', intersect: false,
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#333',
                                bodyColor: '#666',
                                borderColor: '#eee',
                                borderWidth: 1
                            }
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: { grid: { borderDash: [4, 4], color: '#f0f0f0' }, beginAtZero: true }
                        }
                    }
                });
            }

            const pieCtx = document.getElementById('typeDistributionChart');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: pieLabels,
                        datasets: [{
                            data: pieValues,
                            backgroundColor: ['#435ebe', '#57caeb', '#ff7976', '#ff9f43', '#5f27cd'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } }
                        }
                    }
                });
            }
        });
    </script>
@endpush
