@extends('layouts.user.app')
@section('title', 'Edit KP Struktural')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Edit KP Struktural</h2>
                    <p class="text-muted mb-0">Perbaiki data atau dokumen pengajuan Anda</p>
                </div>
                <a href="{{ route('kp.struktural') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Batal
                </a>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="progress-steps">
                    <div class="step active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-label">Data Pegawai</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-label">Dokumen</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-circle">3</div>
                        <div class="step-label">Konfirmasi</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                {{-- ACTION FORM UPDATE --}}
                <form id="form-kp-struktural"
                      action="{{ route('kp.struktural.update', $pengajuan->id) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT') {{-- Method PUT untuk Update --}}

                    {{-- STEP 1: DATA DIRI --}}
                    <div class="form-step active" id="step-1-kp-struktural">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-user-edit me-2"></i>Periksa Data Diri
                            </h5>
                            <p class="text-muted">Pastikan data diri sesuai dengan kondisi terkini.</p>
                        </div>

                        {{-- Hidden Inputs --}}
                        <input type="hidden" name="nip_display_kp_struktural" value="{{ $pengajuan->pegawai->nip }}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pegawai</label>
                                <input type="text" class="form-control bg-light"
                                       id="nama_pegawai_kp_struktural"
                                       value="{{ $pengajuan->pegawai->nama_lengkap }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan Pegawai <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="jabatan_kp_struktural" name="jabatan_kp_struktural"
                                       value="{{ $pengajuan->data_tambahan['jabatan'] ?? $pengajuan->pegawai->jabatan }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pangkat Pegawai <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="pangkat_kp_struktural" name="pangkat_kp_struktural"
                                       value="{{ $pengajuan->data_tambahan['pangkat'] ?? $pengajuan->pegawai->pangkat }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP Pegawai</label>
                                <input type="text" class="form-control bg-light"
                                       value="{{ $pengajuan->pegawai->nip }}" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="unit_kerja_kp_struktural" name="unit_kerja_kp_struktural"
                                       value="{{ $pengajuan->data_tambahan['unit_kerja'] ?? '' }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Golongan Ruang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="golongan_ruang_kp_struktural" name="golongan_ruang_kp_struktural"
                                       value="{{ $pengajuan->data_tambahan['golongan_ruang'] ?? '' }}" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5">
                            <button type="button" class="btn btn-primary btn-next-kp-struktural" data-next="2">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: DOKUMEN --}}
                    <div class="form-step" id="step-2-kp-struktural">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-file-upload me-2"></i>Perbaikan Dokumen
                            </h5>
                            <p class="text-muted">Upload file baru JIKA ingin mengganti dokumen lama.</p>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Biarkan input file kosong jika dokumen tidak perlu diganti.
                        </div>

                        <div class="row">
                            @forelse($syarat as $dokumen)
                                @php
                                    $uploaded = $pengajuan->dokumenPengajuans->firstWhere('syarat_dokumen_id', $dokumen->id);
                                $acceptTypes = collect(explode(',', $dokumen->allowed_types))
        ->map(fn($item) => '.' . trim($item))
        ->implode(',');
                                    @endphp
                                <div class="col-md-6 mb-3">
                                    <div class="file-upload-card h-100">
                                        <label for="file_{{ $dokumen->id }}" class="form-label fw-bold">
                                            {{ $dokumen->nama_dokumen }}
                                            @if ($dokumen->is_required)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        {{-- Info File Lama --}}
                                        @if($uploaded)
                                            <div class="mb-2 p-2 bg-light border rounded small existing-file-info"
                                                 data-label="{{ $dokumen->nama_dokumen }}" data-filename="{{ $uploaded->nama_file_asli }}">
                                                <i class="fas fa-check-circle text-success me-1"></i>
                                                File saat ini: <strong>{{ Str::limit($uploaded->nama_file_asli, 25) }}</strong>
                                                <a href="{{ Storage::url($uploaded->path_file) }}" target="_blank" class="ms-1 text-primary"><i class="fas fa-download"></i></a>
                                            </div>
                                        @else
                                            <div class="mb-2 small text-danger">
                                                <i class="fas fa-times-circle me-1"></i> Belum ada file
                                            </div>
                                        @endif

                                        <div class="file-input-wrapper">
                                            {{-- Input required hanya jika file lama belum ada --}}
                                            <input type="file"
                                                   class="form-control file-input-dynamic"
                                                   id="file_{{ $dokumen->id }}"
                                                   name="file_{{ $dokumen->id }}"
                                                   accept="{{ $acceptTypes }}"
                                                   data-max-size="{{ $dokumen->max_size_kb }}"
                                                   data-allowed-types="{{ $dokumen->allowed_types }}"
                                                {{ ($dokumen->is_required && !$uploaded) ? 'required' : '' }}>

                                            <div class="file-preview mt-2 small text-success"
                                                 id="preview-file_{{ $dokumen->id }}"></div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12"><div class="alert alert-warning">Tidak ada syarat dokumen.</div></div>
                            @endforelse
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Periode Kenaikan Pangkat <span class="text-danger">*</span></label>
                                <select class="form-control" id="periode_kenaikan_pangkat_kp_struktural"
                                        name="periode_kenaikan_pangkat_kp_struktural" required>
                                    @php
                                        $periode = $pengajuan->data_tambahan['periode'] ?? '';
                                        $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                                    @endphp
                                    <option value="">Pilih Periode</option>
                                    @foreach($bulan as $b)
                                        <option
                                            value="{{ $b }}" {{ $periode == $b ? 'selected' : '' }}>{{ $b }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-kp-struktural" data-prev="1">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </button>
                            <button type="button" class="btn btn-primary btn-next-kp-struktural" data-next="3">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 3: KONFIRMASI --}}
                    <div class="form-step" id="step-3-kp-struktural">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-check-circle me-2"></i>Konfirmasi Perubahan
                            </h5>
                            <p class="text-muted">Cek kembali sebelum menyimpan perubahan.</p>
                        </div>

                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Ringkasan Data Pegawai</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Nama:</strong> <span id="review-nama-kp-struktural">-</span></p>
                                        <p><strong>Jabatan:</strong> <span id="review-jabatan-kp-struktural">-</span></p>
                                        <p><strong>Pangkat:</strong> <span id="review-pangkat-kp-struktural">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Unit Kerja:</strong> <span id="review-unit-kerja-kp-struktural">-</span></p>
                                        <p><strong>Golongan/Ruang:</strong> <span id="review-golongan-ruang-kp-struktural">-</span></p>
                                        <p><strong>Periode:</strong> <span id="review-periode-kp-struktural">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Status Dokumen</h6>
                                <div id="review-documents-kp-struktural" class="small"></div>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirm-data-kp-struktural" required>
                            <label class="form-check-label" for="confirm-data-kp-struktural">
                                Saya menyatakan perbaikan data ini benar.
                            </label>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-kp-struktural" data-prev="2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </button>
                            <button type="submit" class="btn btn-warning text-white">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- CSS Styles --}}
    <style>
        .progress-steps { display: flex; justify-content: space-between; position: relative; }
        .progress-steps::before { content: ''; position: absolute; top: 15px; left: 0; right: 0; height: 3px; background-color: #e9ecef; z-index: 1; }
        .progress-steps .step { display: flex; flex-direction: column; align-items: center; position: relative; z-index: 2; }
        .step-circle { width: 40px; height: 40px; border-radius: 50%; background-color: #e9ecef; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-bottom: 8px; border: 3px solid #e9ecef; transition: all 0.3s ease; }
        .step.active .step-circle { background-color: #1a73e8; border-color: #1a73e8; color: white; }
        .step-label { font-size: 0.875rem; font-weight: 500; color: #6c757d; }
        .step.active .step-label { color: #1a73e8; font-weight: 600; }
        .form-step { display: none; }
        .form-step.active { display: block; animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .file-upload-card { border: 2px dashed #dee2e6; border-radius: 8px; padding: 15px; background: white; }
        .file-upload-card:hover { border-color: #1a73e8; background-color: #f8f9fa; }
        .file-preview.has-file { display: block; animation: slideDown 0.3s ease; }
        @keyframes slideDown { from { opacity: 0; max-height: 0; } to { opacity: 1; max-height: 100px; } }
    </style>

    {{-- Javascript Logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Notifikasi
            @if (session('error')) Swal.fire('Gagal', "{{ session('error') }}", 'error'); @endif
            @if ($errors->any()) Swal.fire('Validasi Gagal', 'Cek inputan Anda', 'warning'); @endif

            function handleFileUpload(input) {
                const previewId = `preview-${input.id}`;
                const previewEl = document.getElementById(previewId);

                // Ambil aturan dari database via atribut HTML
                const dbMaxSizeKb = parseInt(input.getAttribute('data-max-size')) || 2048;
                const maxSizeBytes = dbMaxSizeKb * 1024;

                const rawTypes = input.getAttribute('data-allowed-types') || 'pdf,jpg,png';
                const allowedExtensions = rawTypes.split(',').map(t => t.trim().toLowerCase());

                if (input.files.length > 0) {
                    const file = input.files[0];
                    const fileExt = file.name.split('.').pop().toLowerCase();

                    // A. VALIDASI UKURAN
                    if (file.size > maxSizeBytes) {
                        input.value = ''; // Reset
                        input.classList.add('is-invalid');
                        input.classList.remove('is-valid');

                        let sizeMsg = dbMaxSizeKb >= 1024
                            ? (dbMaxSizeKb/1024).toFixed(1) + ' MB'
                            : dbMaxSizeKb + ' KB';

                        if (previewEl) previewEl.innerHTML = `<span class="text-danger small">File terlalu besar! Max: ${sizeMsg}</span>`;
                        Swal.fire('File Terlalu Besar', `Maksimal ukuran: ${sizeMsg}`, 'warning');
                        return;
                    }

                    // B. VALIDASI TIPE
                    if (!allowedExtensions.includes(fileExt)) {
                        input.value = ''; // Reset
                        input.classList.add('is-invalid');
                        input.classList.remove('is-valid');

                        if (previewEl) previewEl.innerHTML = `<span class="text-danger small">Format salah!</span>`;
                        Swal.fire('Format Salah', `Hanya menerima: ${allowedExtensions.join(', ').toUpperCase()}`, 'warning');
                        return;
                    }

                    // C. SUKSES
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                    if (previewEl) previewEl.innerHTML = `<div class="text-success small"><i class="fas fa-check-circle me-1"></i> File Baru: ${file.name}</div>`;
                } else {
                    // Cancel upload
                    input.classList.remove('is-valid');
                    input.classList.remove('is-invalid');
                    if (previewEl) previewEl.innerHTML = '';
                }
            }

            // Navigasi
            const steps = document.querySelectorAll('.form-step');
            const progressSteps = document.querySelectorAll('.progress-steps .step');

            function showStep(idx) {
                steps.forEach(s => s.classList.remove('active'));
                progressSteps.forEach(s => s.classList.remove('active'));
                document.getElementById(`step-${idx}-kp-struktural`).classList.add('active');
                for (let i = 0; i < idx; i++) progressSteps[i].classList.add('active');
                if (idx == 3) updateReview();
            }

            // Button Listeners
            document.querySelectorAll('.btn-next-kp-struktural').forEach(btn => {
                btn.addEventListener('click', function () {
                    const next = parseInt(this.dataset.next);

                    // Validasi saat mau ke Step 3 (Konfirmasi)
                    if (next === 3) {
                        // Cek 1: Apakah ada file error (is-invalid)?
                        if (document.querySelectorAll('#step-2-kp-struktural input.is-invalid').length > 0) {
                            Swal.fire('Dokumen Bermasalah', 'Perbaiki dokumen yang bertanda merah sebelum lanjut.', 'error');
                            return;
                        }

                        // Cek 2: Dokumen wajib yang kosong
                        let valid = true;
                        document.querySelectorAll('#step-2-kp-struktural input[type="file"][required]').forEach(input => {
                            if (input.files.length === 0) {
                                input.classList.add('is-invalid');
                                valid = false;
                            }
                        });

                        if(!valid) {
                            Swal.fire('Perhatian', 'Harap lengkapi dokumen wajib yang belum diunggah.', 'warning');
                            return;
                        }
                    }
                    showStep(next);
                });
            });

            document.querySelectorAll('.btn-prev-kp-struktural').forEach(btn => {
                btn.addEventListener('click', function () { showStep(this.dataset.prev); });
            });

            // Preview File Baru
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', function () {
                    handleFileUpload(this);
                });
            });

            // Update Review Page
            function updateReview() {
                const get = (id) => document.getElementById(id)?.value || '-';

                // Gunakan default value dari PHP blade jika input kosong (readonly inputs)
                const defaultNama = '{{ $pengajuan->pegawai->nama_lengkap }}';

                document.getElementById('review-nama-kp-struktural').textContent = document.getElementById('nama_pegawai_kp_struktural').value || defaultNama;
                document.getElementById('review-jabatan-kp-struktural').textContent = get('jabatan_kp_struktural');
                document.getElementById('review-pangkat-kp-struktural').textContent = get('pangkat_kp_struktural');
                document.getElementById('review-unit-kerja-kp-struktural').textContent = get('unit_kerja_kp_struktural');
                document.getElementById('review-golongan-ruang-kp-struktural').textContent = get('golongan_ruang_kp_struktural');

                // Ambil text periode
                const pSelect = document.getElementById('periode_kenaikan_pangkat_kp_struktural');
                document.getElementById('review-periode-kp-struktural').textContent = pSelect.options[pSelect.selectedIndex]?.text || '-';

                // Render List Dokumen
                const container = document.getElementById('review-documents-kp-struktural');
                container.innerHTML = '';

                document.querySelectorAll('.file-upload-card').forEach(card => {
                    const label = card.querySelector('label').innerText.replace('*','').trim();
                    const fileInput = card.querySelector('input[type="file"]');
                    const existingInfo = card.querySelector('.existing-file-info');

                    let statusHtml = '';

                    if (fileInput.files.length > 0 && !fileInput.classList.contains('is-invalid')) {
                        statusHtml = `<span class="text-warning fw-bold"><i class="fas fa-sync me-1"></i>Ganti Baru: ${fileInput.files[0].name}</span>`;
                    } else if (existingInfo) {
                        const oldName = existingInfo.getAttribute('data-filename');
                        statusHtml = `<span class="text-success"><i class="fas fa-check-circle me-1"></i>Tetap: ${oldName}</span>`;
                    } else {
                        statusHtml = `<span class="text-danger"><i class="fas fa-times me-1"></i>Tidak ada file</span>`;
                    }

                    const div = document.createElement('div');
                    div.className = 'd-flex justify-content-between border-bottom py-2';
                    div.innerHTML = `<span>${label}</span> ${statusHtml}`;
                    container.appendChild(div);
                });
            }

            showStep(1);
        });
    </script>
@endsection
