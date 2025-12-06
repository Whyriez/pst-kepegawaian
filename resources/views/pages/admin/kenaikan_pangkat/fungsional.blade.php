@extends('layouts.admin.app')
@section('title', 'Kenaikan Pangkat Fungsional')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold text-primary mb-1">Kenaikan Pangkat Fungsional</h2>
            <p class="text-muted mb-0">Kelola persetujuan pengajuan kenaikan pangkat fungsional pegawai</p>
        </div>

        <div class="dropdown">
            <button class="btn btn-white border shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-filter me-2 text-muted"></i>
                {{ request('status') == 'ditunda' ? 'Status: Ditunda' : (request('status') ? ucfirst(request('status')) : 'Semua Status') }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                <li><a class="dropdown-item" href="{{ route('admin.kp.fungsional') }}">Semua</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.kp.fungsional', ['status' => 'pending']) }}">Menunggu
                        Verifikasi</a></li>
                <li><a class="dropdown-item"
                        href="{{ route('admin.kp.fungsional', ['status' => 'disetujui']) }}">Disetujui</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.kp.fungsional', ['status' => 'ditunda']) }}">Ditunda</a>
                </li>
                <li><a class="dropdown-item" href="{{ route('admin.kp.fungsional', ['status' => 'ditolak']) }}">Ditolak</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary rounded-circle p-3 me-3">
                        <i class="fas fa-file-alt fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-1">Total Pengajuan</h6>
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

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-primary fw-bold"><i class="fas fa-list me-2"></i>Daftar Pengajuan</h5>
            <form action="" method="GET" class="w-25">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light"
                        placeholder="Cari nama/NIP..." value="{{ request('search') }}">
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
                            <th class="py-3">Jabatan & Pangkat</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center">Dokumen</th>
                            <th class="py-3 text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuan as $item)
                            <tr>
                                {{-- 1. Tanggal --}}
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $item->tanggal_pengajuan->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $item->created_at->format('H:i') }} WIB</small>
                                </td>

                                {{-- 2. Data Pegawai (Relasi ke Pegawai) --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{-- Avatar UI --}}
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($item->pegawai->nama_lengkap) }}&background=random"
                                            class="rounded-circle me-2" width="32" height="32">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $item->pegawai->nama_lengkap }}</div>
                                            <small class="text-muted">NIP. {{ $item->pegawai->nip }}</small>
                                        </div>
                                    </div>
                                </td>

                                {{-- 3. Jabatan & Pangkat (Dari Data Pegawai & Data Tambahan JSON) --}}
                                <td>
                                    <div class="small fw-bold">{{ $item->pegawai->jabatan }}</div>

                                    {{-- Ambil Pangkat Baru dari kolom JSON data_tambahan --}}
                                    <small class="text-muted">
                                        Usul: {{ $item->data_tambahan['pangkat_kp_fungsional'] ?? '-' }}
                                        ({{ $item->data_tambahan['golongan_ruang'] ?? '-' }})
                                    </small>
                                </td>

                                {{-- 4. Status (Menggunakan Accessor dari Model Pengajuan yang Anda kirim) --}}
                                <td>
                                    @if ($item->status == 'pending')
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>
                                            Menunggu</span>
                                    @elseif($item->status == 'disetujui')
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>
                                            Disetujui</span>
                                    @elseif($item->status == 'ditolak')
                                        <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>
                                            Ditolak</span>
                                    @elseif($item->status == 'ditunda')
                                        <span class="badge bg-secondary"><i class="fas fa-pause-circle me-1"></i>
                                            Ditunda</span>
                                    @endif
                                </td>

                                {{-- 5. Tombol Lihat Berkas --}}
                                <td class="text-center">
                                    {{-- Kita map dokumenPengajuans agar sesuai format JSON JS --}}
                                    @php
                                        $berkasList = $item->dokumenPengajuans->map(function ($doc) {
                                            // 1. Bersihkan path dari 'public/' jika ada
                                            $cleanPath = str_replace('public/', '', $doc->path_file);

                                            // 2. Cek apakah file ini PDF (Cek dari path filenya, bukan nama syaratnya)
                                            $isPdf = str_ends_with(strtolower($cleanPath), '.pdf');

                                            return [
                                                'nama_dokumen' => $doc->syaratDokumen->nama_dokumen ?? 'Dokumen',
                                                'url' => asset('storage/' . $cleanPath),
                                                'is_pdf' => $isPdf, // <--- KITA TAMBAHKAN INI
                                            ];
                                        });
                                    @endphp

                                    <button class="btn btn-sm btn-light border btn-preview"
                                        data-nama="{{ $item->pegawai->nama_lengkap }}"
                                        data-files="{{ $berkasList->toJson() }}" onclick="loadPreview(this)">
                                        <i class="fas fa-paperclip me-1 text-primary"></i>
                                        {{ $item->dokumenPengajuans->count() }} Berkas
                                    </button>
                                </td>

                                {{-- 6. Aksi --}}
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
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <p>Belum ada data pengajuan.</p>
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
                <form action="{{ route('admin.kp.fungsional.reject') }}" method="POST" id="formTolak">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Tolak Pengajuan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="rejectId">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori Penolakan</label>
                            <select class="form-select" name="kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="dokumen_tidak_lengkap">Dokumen Tidak Lengkap</option>
                                <option value="data_tidak_sesuai">Data Tidak Sesuai</option>
                                <option value="masa_kerja_kurang">Masa Kerja Belum Mencukupi</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan Perbaikan</label>
                            <textarea class="form-control" name="alasan" rows="4" required
                                placeholder="Jelaskan secara detail kekurangan dokumen..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Kirim Penolakan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL TUNDA --}}
    <div class="modal fade" id="postponeModalPage" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.kp.fungsional.postpone') }}" method="POST" id="formTunda">
                    @csrf
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title"><i class="fas fa-pause-circle me-2"></i>Tunda Pengajuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="postponeId">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Prioritas Tindak Lanjut</label>
                            <select class="form-select" name="prioritas">
                                <option value="rendah">Rendah</option>
                                <option value="sedang" selected>Sedang</option>
                                <option value="tinggi">Tinggi (Segera)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Rencana Tindak Lanjut</label>
                            <input type="date" class="form-control" name="tanggal_tindak_lanjut" required
                                value="{{ date('Y-m-d', strtotime('+3 days')) }}">
                            <div class="form-text text-muted">Default: 3 hari dari sekarang</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Alasan Penundaan</label>
                            <textarea class="form-control" name="alasan" rows="3" required
                                placeholder="Contoh: Menunggu konfirmasi BKN..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Simpan Penundaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="formApproveHidden" action="{{ route('admin.kp.fungsional.approve') }}" method="POST"
        style="display: none;">
        @csrf
        <input type="hidden" name="id" id="approveId">
    </form>
@endsection

@push('scripts')
    <script>
        // 1. Approve Logic
        function confirmApprove(id) {
            Swal.fire({
                title: 'Setujui Pengajuan?',
                text: "Status akan berubah menjadi Disetujui.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Masukkan ID ke form hidden
                    document.getElementById('approveId').value = id;
                    // Submit Form
                    document.getElementById('formApproveHidden').submit();
                }
            });
        }

        // 2. Modal Tolak Logic
        function showRejectModal(id) {
            document.getElementById('rejectId').value = id;
            // Reset form agar bersih saat dibuka
            document.getElementById('formTolak').reset();
            new bootstrap.Modal(document.getElementById('rejectModalPage')).show();
        }

        // 3. Modal Tunda Logic
        function showPostponeModal(id) {
            document.getElementById('postponeId').value = id;
            // Reset form
            document.getElementById('formTunda').reset();
            new bootstrap.Modal(document.getElementById('postponeModalPage')).show();
        }

        // 4. Modal Preview Logic (INI YANG KEMARIN KOSONG)
        function loadPreview(element) {
            const nama = element.getAttribute('data-nama');
            let files = [];
            try {
                files = JSON.parse(element.getAttribute('data-files'));
            } catch (e) {
                files = [];
            }

            // Jika kosong (Dummy Data) - Kita set is_pdf manual
            if (files.length === 0) {
                files = [{
                        nama_dokumen: 'Contoh SK.pdf',
                        url: '#',
                        is_pdf: true
                    },
                    {
                        nama_dokumen: 'Contoh Foto.jpg',
                        url: '#',
                        is_pdf: false
                    }
                ];
            }

            // Set Judul
            const titleEl = document.getElementById('previewTitle');
            if (titleEl) titleEl.textContent = `Preview Berkas - ${nama}`;

            // Render List Sidebar
            const listContainer = document.getElementById('fileListContainer');
            if (listContainer) {
                listContainer.innerHTML = '';
                files.forEach((file, index) => {
                    // Gunakan properti is_pdf untuk menentukan ikon
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
                    <i class="fas fa-chevron-right small opacity-50"></i>
                </div>`;

                    item.onclick = (e) => {
                        e.preventDefault();
                        listContainer.querySelectorAll('a').forEach(el => el.classList.remove('active'));
                        item.classList.add('active');
                        showFileInViewer(file); // Panggil fungsi viewer
                    };
                    listContainer.appendChild(item);
                });
            }

            // Tampilkan file pertama
            if (files.length > 0) showFileInViewer(files[0]);

            new bootstrap.Modal(document.getElementById('previewModal')).show();
        }

        // Helper: Menampilkan file di iframe atau img tag
        function showFileInViewer(file) {
            const pdfViewer = document.getElementById('pdfViewer');
            const imgViewer = document.getElementById('imageViewer');
            const placeholder = document.getElementById('pdfPlaceholder');
            const btnDownload = document.getElementById('btnDownloadFile');

            // 1. Reset Tampilan (Sembunyikan Semua)
            if (pdfViewer) pdfViewer.style.display = 'none';
            if (imgViewer) imgViewer.style.display = 'none';
            if (placeholder) placeholder.style.display = 'none';

            // 2. Set Link Download
            if (btnDownload) btnDownload.href = file.url;

            // 3. Logika Tampilan Berdasarkan is_pdf
            if (file.is_pdf) {

                if (pdfViewer) {
                    // Trik cloning untuk memaksa browser refresh PDF Viewer
                    const clone = pdfViewer.cloneNode(true);

                    // Tambahkan parameter waktu agar tidak dicache browser/IDM
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
