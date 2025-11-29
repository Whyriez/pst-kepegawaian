@extends('layouts.admin.app')
@section('title', 'Jabatan Fungsional - Pengangkatan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold text-primary mb-1">Pengangkatan Jabatan Fungsional</h2>
            <p class="text-muted mb-0">Verifikasi usulan pengangkatan pejabat fungsional</p>
        </div>

        <div class="dropdown">
            <button class="btn btn-white border shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-filter me-2 text-muted"></i>
                {{ request('status') == 'ditunda' ? 'Status: Ditunda' : (request('status') ? ucfirst(request('status')) : 'Semua Status') }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                <li><a class="dropdown-item" href="{{ route('admin.jf.pengangkatan') }}">Semua</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.jf.pengangkatan', ['status' => 'pending']) }}">Menunggu
                        Verifikasi</a></li>
                <li><a class="dropdown-item"
                        href="{{ route('admin.jf.pengangkatan', ['status' => 'disetujui']) }}">Disetujui</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.jf.pengangkatan', ['status' => 'ditunda']) }}">Ditunda</a>
                </li>
                <li><a class="dropdown-item" href="{{ route('admin.jf.pengangkatan', ['status' => 'ditolak']) }}">Ditolak</a>
                </li>
            </ul>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary rounded-circle p-3 me-3">
                        <i class="fas fa-briefcase fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-1">Total Usulan</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-warning border-4">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning rounded-circle p-3 me-3">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-1">Menunggu</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['pending'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-success border-4">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success-subtle text-success rounded-circle p-3 me-3">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-1">Disetujui</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['disetujui'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-danger border-4">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-danger-subtle text-danger rounded-circle p-3 me-3">
                        <i class="fas fa-times-circle fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-1">Ditolak</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['ditolak'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-primary fw-bold"><i class="fas fa-list me-2"></i>Daftar Pengajuan</h5>
            <form action="" method="GET" class="w-25">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light"
                        placeholder="Cari Nama/NIP..." value="{{ request('search') }}">
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Tanggal</th>
                            <th class="py-3">Pegawai</th>
                            <th class="py-3">Jabatan Saat Ini</th>
                            <th class="py-3">Usulan Jabatan</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center">Dokumen</th>
                            <th class="py-3 text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuan as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $item->tanggal_pengajuan->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $item->created_at->format('H:i') }} WIB</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($item->pegawai->nama_lengkap) }}&background=random"
                                            class="rounded-circle me-2" width="32" height="32">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $item->pegawai->nama_lengkap }}</div>
                                            <small class="text-muted">NIP. {{ $item->pegawai->nip }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-bold">{{ $item->data_tambahan['jabatan'] ?? '-' }}</div>
                                    <small class="text-muted">{{ $item->data_tambahan['unit_kerja'] ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="small fw-bold text-primary">
                                        {{ $item->data_tambahan['usul_jabatan'] ?? '-' }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $item->data_tambahan['usul_unit_kerja'] ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    @if ($item->status == 'pending')
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @elseif($item->status == 'disetujui')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($item->status == 'ditolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @elseif($item->status == 'ditunda')
                                        <span class="badge bg-secondary">Ditunda</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $berkasList = $item->dokumenPengajuans->map(function ($doc) {
                                            $cleanPath = str_replace('public/', '', $doc->path_file);
                                            $isPdf = str_ends_with(strtolower($cleanPath), '.pdf');
                                            return [
                                                'nama_dokumen' => $doc->syaratDokumen->nama_dokumen ?? 'Dokumen',
                                                'url' => asset('storage/' . $cleanPath),
                                                'is_pdf' => $isPdf,
                                            ];
                                        });
                                    @endphp
                                    <button class="btn btn-sm btn-light border btn-preview"
                                        data-nama="{{ $item->pegawai->nama_lengkap }}"
                                        data-files="{{ $berkasList->toJson() }}" onclick="loadPreview(this)">
                                        <i class="fas fa-paperclip me-1 text-primary"></i>
                                        {{ $item->dokumenPengajuans->count() }}
                                    </button>
                                </td>
                                <td class="text-end pe-4">
                                    @if ($item->status == 'pending' || $item->status == 'ditunda')
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-success" title="Setujui"
                                                onclick="confirmApprove({{ $item->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" title="Tunda"
                                                onclick="showPostponeModal({{ $item->id }})">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" title="Tolak"
                                                onclick="showRejectModal({{ $item->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @elseif($item->status == 'disetujui')
                                        <button class="btn btn-sm btn-light text-muted" disabled>
                                            <i class="fas fa-check-double"></i> Selesai
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-light text-danger" disabled>Ditolak</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <p>Belum ada pengajuan pengangkatan JF.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3 d-flex justify-content-end">
            {{ $pengajuan->links() }}
        </div>
    </div>

    {{-- MODAL TOLAK --}}
    <div class="modal fade" id="rejectModalPage" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.jf.pengangkatan.reject') }}" method="POST" id="formTolak">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Tolak Pengajuan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="rejectId">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alasan Penolakan</label>
                            <select class="form-select" name="kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="formasi_tidak_tersedia">Formasi Tidak Tersedia</option>
                                <option value="tidak_lulus_uji_kompetensi">Tidak Lulus Uji Kompetensi</option>
                                <option value="angka_kredit_kurang">Angka Kredit Belum Mencukupi</option>
                                <option value="dokumen_tidak_lengkap">Dokumen Tidak Lengkap</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Detail Alasan</label>
                            <textarea class="form-control" name="alasan" rows="4" required placeholder="Jelaskan alasan penolakan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL TUNDA --}}
    <div class="modal fade" id="postponeModalPage" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.jf.pengangkatan.postpone') }}" method="POST" id="formTunda">
                    @csrf
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">Tunda Pengajuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="postponeId">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Prioritas</label>
                            <select class="form-select" name="prioritas">
                                <option value="sedang">Sedang</option>
                                <option value="tinggi">Tinggi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tindak Lanjut</label>
                            <input type="date" class="form-control" name="tanggal_tindak_lanjut" required
                                value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alasan</label>
                            <textarea class="form-control" name="alasan" rows="3" required placeholder="Menunggu hasil Ukom..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Tunda</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- HIDDEN APPROVE --}}
    <form id="formApproveHidden" action="{{ route('admin.jf.pengangkatan.approve') }}" method="POST"
        style="display: none;">
        @csrf
        <input type="hidden" name="id" id="approveId">
    </form>
@endsection

@push('scripts')
    <script>
        function confirmApprove(id) {
            Swal.fire({
                title: 'Setujui Pengangkatan?',
                text: "Status akan berubah menjadi Disetujui.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                confirmButtonText: 'Ya, Setujui'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approveId').value = id;
                    document.getElementById('formApproveHidden').submit();
                }
            });
        }

        function showRejectModal(id) {
            document.getElementById('rejectId').value = id;
            document.getElementById('formTolak').reset();
            new bootstrap.Modal(document.getElementById('rejectModalPage')).show();
        }

        function showPostponeModal(id) {
            document.getElementById('postponeId').value = id;
            document.getElementById('formTunda').reset();
            new bootstrap.Modal(document.getElementById('postponeModalPage')).show();
        }

        function loadPreview(element) {
            const nama = element.getAttribute('data-nama');
            let files = [];
            try {
                files = JSON.parse(element.getAttribute('data-files'));
            } catch (e) {
                files = [];
            }

            if (files.length === 0) return;

            const titleEl = document.getElementById('previewTitle');
            if (titleEl) titleEl.textContent = `Preview Berkas - ${nama}`;

            const listContainer = document.getElementById('fileListContainer');
            if (listContainer) {
                listContainer.innerHTML = '';
                files.forEach((file, index) => {
                    const icon = file.is_pdf ? 'fa-file-pdf text-danger' : 'fa-file-image text-primary';
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = `list-group-item list-group-item-action p-3 ${index === 0 ? 'active' : ''}`;
                    item.innerHTML = `
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                <i class="fas ${icon}"></i>
                                <span class="text-truncate" style="max-width: 150px;">${file.nama_dokumen}</span>
                            </div>
                        </div>`;
                    item.onclick = (e) => {
                        e.preventDefault();
                        listContainer.querySelectorAll('a').forEach(el => el.classList.remove('active'));
                        item.classList.add('active');
                        showFileInViewer(file);
                    };
                    listContainer.appendChild(item);
                });
            }
            if (files.length > 0) showFileInViewer(files[0]);
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        }

        function showFileInViewer(file) {
            const pdfViewer = document.getElementById('pdfViewer');
            const imgViewer = document.getElementById('imageViewer');
            const btnDownload = document.getElementById('btnDownloadFile');

            if (pdfViewer) pdfViewer.style.display = 'none';
            if (imgViewer) imgViewer.style.display = 'none';
            if (btnDownload) btnDownload.href = file.url;

            if (file.is_pdf) {
                if (pdfViewer) {
                    const clone = pdfViewer.cloneNode(true);
                    clone.setAttribute('src', file.url + '?t=' + new Date().getTime());
                    clone.style.display = 'block';
                    pdfViewer.parentNode.replaceChild(clone, pdfViewer);
                }
            } else {
                if (imgViewer) {
                    imgViewer.src = file.url;
                    imgViewer.style.display = 'block';
                }
            }
        }
    </script>
@endpush