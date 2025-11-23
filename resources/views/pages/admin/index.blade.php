@extends('layouts.admin.app')
@section('title', 'Home')

@section('content')
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="flex-grow-1">
                <h2 class="h3 fw-bold text-dark mb-2">Dashboard Admin</h2>
                <p class="text-muted mb-0">
                    <i class="fas fa-calendar me-1"></i> {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-clock me-1"></i> <span id="liveClock">{{ date('H:i:s') }}</span>
                </p>
            </div>
            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                <div class="d-flex gap-3">
                    <div class="text-end">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge bg-success"><i class="fas fa-circle me-1" style="font-size: 6px;"></i> Online</span>
                    </div>
                    <div class="vr"></div>
                    <div class="text-end">
                        <small class="text-muted d-block">Pengguna</small>
                        <small class="fw-bold text-primary">{{ $stats['user_active'] }} Active</small>
                    </div>
                </div>
                {{-- Quick Actions Dropdown --}}
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle px-3" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bolt me-2"></i>Aksi Cepat
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('admin.kp.fungsional') }}"><i class="fas fa-user-tie me-2"></i>KP Fungsional</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.kp.reguler') }}"><i class="fas fa-layer-group me-2"></i>KP Reguler</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i>Cetak Laporan</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- KARTU STATISTIK UTAMA (DINAMIS) --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-4 col-6">
            <div class="card stat-card border-0 shadow-sm h-100 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="card-title text-muted mb-2">Berkas Baru</h6>
                            <h3 class="fw-bold text-warning mb-2">{{ $stats['berkas_baru'] }}</h3>
                            <p class="text-muted small mb-0">Hari ini</p>
                        </div>
                        <div class="icon-circle bg-warning flex-shrink-0"><i class="fas fa-inbox text-white"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="card-title text-muted mb-2">Menunggu</h6>
                            <h3 class="fw-bold text-warning mb-2">{{ $stats['menunggu'] }}</h3>
                            <p class="text-muted small mb-0">Perlu Verifikasi</p>
                        </div>
                        <div class="icon-circle bg-warning flex-shrink-0"><i class="fas fa-clock text-white"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="card-title text-muted mb-2">Disetujui</h6>
                            <h3 class="fw-bold text-success mb-2">{{ $stats['disetujui'] }}</h3>
                            <p class="text-muted small mb-0">Bulan Ini</p>
                        </div>
                        <div class="icon-circle bg-success flex-shrink-0"><i class="fas fa-check-circle text-white"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="card-title text-muted mb-2">Ditolak</h6>
                            <h3 class="fw-bold text-danger mb-2">{{ $stats['ditolak'] }}</h3>
                            <p class="text-muted small mb-0">Bulan Ini</p>
                        </div>
                        <div class="icon-circle bg-danger flex-shrink-0"><i class="fas fa-times-circle text-white"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CHARTS SECTION --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Tren Pengajuan (30 Hari)</h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        {{-- Canvas Chart.js --}}
                        <canvas id="submissionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-chart-pie me-2 text-success"></i>Jenis Layanan</h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        {{-- Canvas Chart.js --}}
                        <canvas id="typeDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- SIDEBAR KANAN (Kalender & Ringkasan) --}}
        <div class="col-xl-4 order-xl-2">
            <div class="row g-3 h-100">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="fw-bold mb-0"><i class="fas fa-calendar me-2 text-primary"></i>Kalender</h6>
                        </div>
                        <div class="card-body text-center p-3">
                            <div id="miniCalendar" class="mb-3"></div>
                        </div>
                    </div>
                </div>
                {{-- CARD RINGKASAN (SEKARANG DINAMIS) --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="fw-bold mb-0"><i class="fas fa-tachometer-alt me-2 text-success"></i>Ringkasan</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Total Pengajuan</span>
                                <span class="fw-bold text-primary">{{ $stats['total_pengajuan'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Sedang Diproses</span>
                                <span class="fw-bold text-warning">{{ $stats['menunggu'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Selesai Bulan Ini</span>
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
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-history me-2 text-primary"></i>Aktivitas Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-4">Waktu</th>
                                    <th class="border-0">Layanan</th>
                                    <th class="border-0">Pegawai</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold">{{ $item->updated_at->format('H:i') }}</div>
                                            <small class="text-muted">{{ $item->updated_at->format('d M') }}</small>
                                        </td>
                                        <td>{{ $item->jenisLayanan->nama_layanan ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($item->pegawai->nama_lengkap) }}&background=random" class="rounded-circle me-2" width="24">
                                                <span class="text-truncate" style="max-width: 150px;">{{ $item->pegawai->nama_lengkap }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($item->status == 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($item->status == 'disetujui')
                                                <span class="badge bg-success">Disetujui</span>
                                            @elseif($item->status == 'ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
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
                                            <a href="{{ $route }}?search={{ $item->pegawai->nip }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4">Belum ada aktivitas.</td></tr>
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
    {{-- 1. PANGGIL LIBRARY CHART.JS (Wajib ada agar chart muncul) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // --- JAM DIGITAL ---
        setInterval(() => {
            const now = new Date();
            document.getElementById('liveClock').textContent = now.toLocaleTimeString('id-ID');
        }, 1000);

        // --- KALENDER MINI ---
        const today = new Date();
        document.getElementById('miniCalendar').innerHTML = `
            <div class="mb-3">
                <h5 class="text-primary mb-1">${today.toLocaleString('id-ID', { month: 'long', year: 'numeric' })}</h5>
                <h2 class="fw-bold text-dark display-6">${today.getDate()}</h2>
                <small class="text-muted">${today.toLocaleString('id-ID', { weekday: 'long' })}</small>
            </div>`;

        // --- CHART JS LOGIC ---
        document.addEventListener('DOMContentLoaded', function() {
            
            // A. DATA DARI CONTROLLER (JSON)
            const lineLabels = @json($chartData->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M')));
            const lineTotal = @json($chartData->pluck('total'));
            const lineApproved = @json($chartData->pluck('disetujui'));

            const pieLabels = @json($pieData->pluck('nama_layanan'));
            const pieValues = @json($pieData->pluck('total'));

            // B. RENDER LINE CHART (Submission)
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
                                borderColor: '#1a73e8',
                                backgroundColor: 'rgba(26, 115, 232, 0.1)',
                                tension: 0.4, fill: true
                            },
                            {
                                label: 'Disetujui',
                                data: lineApproved,
                                borderColor: '#00d2d3',
                                backgroundColor: 'rgba(0, 210, 211, 0.1)',
                                tension: 0.4, fill: true
                            }
                        ]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }

            // C. RENDER PIE CHART (Distribution)
            const pieCtx = document.getElementById('typeDistributionChart');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: pieLabels,
                        datasets: [{
                            data: pieValues,
                            backgroundColor: ['#1a73e8', '#00d2d3', '#ff9f43', '#ff6b6b', '#5f27cd']
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }
        });
    </script>
@endpush