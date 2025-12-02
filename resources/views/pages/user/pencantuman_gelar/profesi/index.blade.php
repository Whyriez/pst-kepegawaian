@extends('layouts.user.app')
@section('title', 'Riwayat Gelar Profesi')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .card-status { transition: transform 0.2s; cursor: default; }
        .card-status:hover { transform: translateY(-5px); }
        .table align-middle td { vertical-align: top !important; padding-top: 1rem; padding-bottom: 1rem; }
        .rejection-note {
            background-color: #fff5f5; border-left: 3px solid #dc3545;
            padding: 8px 10px; margin-top: 8px; border-radius: 4px;
            font-size: 0.8rem; color: #495057; text-align: left;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-0">

        {{-- HEADER --}}
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Pencantuman Gelar Profesi</h2>
                    <p class="text-muted mb-0">Riwayat pengajuan pencantuman gelar profesi.</p>
                </div>
                <a href="{{ route('gelar.profesi.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus-circle me-2"></i>Ajukan Gelar
                </a>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm card-status border-start border-primary border-4">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">Total Pengajuan</div>
                        <div class="h2 mb-0 fw-bold text-dark">{{ $pengajuans->total() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm card-status border-start border-warning border-4">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">Menunggu</div>
                        <div class="h2 mb-0 fw-bold text-dark">{{ $pengajuans->where('status', 'pending')->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm card-status border-start border-success border-4">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">Disetujui</div>
                        <div class="h2 mb-0 fw-bold text-dark">{{ $pengajuans->where('status', 'disetujui')->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm card-status border-start border-danger border-4">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">Ditolak/Revisi</div>
                        <div class="h2 mb-0 fw-bold text-dark">{{ $pengajuans->whereIn('status', ['ditolak', 'perbaikan'])->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN TABLE --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="fas fa-history me-2 text-primary"></i>Riwayat Pengajuan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light text-secondary">
                        <tr>
                            <th class="py-3 px-3">Jenjang Pendidikan</th>
                            <th class="py-3">Profesi yang Diusulkan</th>
                            <th class="py-3">Jabatan</th>
                            <th class="py-3" style="width: 250px;">Status</th>
                            <th class="py-3 text-end px-3">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($pengajuans as $item)
                            <tr>
                                {{-- 1. Jenjang Pendidikan --}}
                                <td class="px-3">
                                    <div class="fw-bold text-dark">{{ $item->data_tambahan['jenjang_pendidikan'] ?? '-' }}</div>
                                </td>

                                {{-- 2. Profesi Diusulkan --}}
                                <td>
                                    <span class="text-primary fw-bold">
                                        {{ $item->data_tambahan['usul_profesi'] ?? '-' }}
                                    </span>
                                </td>

                                {{-- 3. Jabatan --}}
                                <td>
                                    {{ $item->data_tambahan['jabatan'] ?? '-' }}
                                    <div class="small text-muted">{{ $item->data_tambahan['unit_kerja'] ?? '' }}</div>
                                </td>

                                {{-- 4. Status --}}
                                <td>
                                    <span class="badge bg-{{ $item->status_badge }} px-3 py-2 rounded-pill text-uppercase mb-1">
                                        {{ $item->status }}
                                    </span>
                                    @if(in_array($item->status, ['ditolak', 'perbaikan']) && !empty($item->catatan_admin))
                                        <div class="rejection-note fade-in">
                                            <strong><i class="fas fa-exclamation-circle text-danger me-1"></i> Catatan:</strong><br>
                                            {{ $item->catatan_admin }}
                                        </div>
                                    @endif
                                </td>

                                {{-- 5. Aksi --}}
                                <td class="text-end px-3">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0)"
                                                   onclick="showDetail('{{ $item->nomor_tiket }}', '{{ $item->status }}', '{{ $item->catatan_admin ?? 'Tidak ada catatan.' }}')">
                                                    <i class="fas fa-eye me-2 text-info"></i> Detail
                                                </a>
                                            </li>
                                            @if(in_array($item->status, ['pending', 'perbaikan']))
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item fw-bold text-warning"
                                                       href="{{ route('gelar.profesi.edit', ['id' => $item->id]) }}">
                                                        <i class="fas fa-edit me-2"></i> Edit
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="py-4">
                                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486747.png" alt="Empty" width="80" class="mb-3 opacity-50">
                                        <h6 class="text-muted fw-bold">Belum ada pengajuan</h6>
                                        <a href="{{ route('gelar.profesi.create') }}" class="btn btn-sm btn-primary mt-2">
                                            Ajukan Sekarang
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PAGINATION --}}
            @if($pengajuans->hasPages())
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small text-muted">
                            Menampilkan {{ $pengajuans->firstItem() }} - {{ $pengajuans->lastItem() }} dari {{ $pengajuans->total() }}
                        </div>
                        <div>{{ $pengajuans->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function showDetail(tiket, status, catatan) {
            Swal.fire({
                title: 'Detail Pengajuan #' + tiket,
                html: `
                    <div class="text-start">
                        <table class="table table-sm table-borderless">
                            <tr><td width="35%"><strong>Layanan</strong></td><td>: Gelar Profesi</td></tr>
                            <tr><td><strong>Status</strong></td><td>: <span class="badge bg-secondary">${status}</span></td></tr>
                        </table>
                        <hr>
                        <p class="mb-1 fw-bold">Catatan Verifikator:</p>
                        <div class="alert alert-light border p-3 bg-light text-start">${catatan}</div>
                    </div>
                `,
                confirmButtonText: 'Tutup'
            });
        }
        @if(session('success')) Swal.fire('Berhasil!', '{{ session('success') }}', 'success'); @endif
    </script>
@endsection
