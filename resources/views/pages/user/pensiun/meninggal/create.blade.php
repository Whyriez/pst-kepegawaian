@extends('layouts.user.app')
@section('title', 'Pensiun Meninggal Dunia')

{{-- Tambahkan SweetAlert --}}
@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Form Pengajuan Pensiun Meninggal Dunia</h2>
                    <p class="text-muted mb-0">Formulir untuk pengajuan pensiun karena pegawai meninggal dunia</p>
                </div>
                <a href="{{ route('pensiun.meninggal') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
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
                {{-- ACTION FORM --}}
                <form id="form-pensiun-meninggal" action="{{ route('pensiun.meninggal.store') }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    {{-- STEP 1: DATA DIRI --}}
                    <div class="form-step active" id="step-1-meninggal">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-user me-2"></i>Data Diri Pegawai
                            </h5>
                            <p class="text-muted">Isi data diri pegawai yang meninggal dunia</p>
                        </div>

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Cek Data dengan NIP</h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="nip_pegawai_meninggal"
                                                   name="nip_pegawai_meninggal" placeholder="Masukkan NIP Pegawai"
                                                   value="{{ Auth::user()->pegawai->nip ?? '' }}">
                                            <button class="btn btn-outline-primary" type="button"
                                                    id="btn-cek-nip-meninggal">
                                                <i class="fas fa-search me-2"></i>Cek NIP
                                            </button>
                                        </div>
                                        <small class="text-muted fst-italic">*Klik Cek NIP untuk mengisi data otomatis</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_pegawai_meninggal" class="form-label">Nama Pegawai <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="nama_pegawai_meninggal"
                                           name="nama_pegawai_meninggal" required>
                                </div>
                                <div class="invalid-feedback">Harap isi nama pegawai</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nip_display_meninggal" class="form-label">NIP Pegawai <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" class="form-control bg-light" id="nip_display_meninggal"
                                           name="nip_display_meninggal" required readonly>
                                </div>
                                <div class="invalid-feedback">Harap isi NIP pegawai</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jabatan_meninggal" class="form-label">Jabatan Pegawai <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    <input type="text" class="form-control" id="jabatan_meninggal"
                                           name="jabatan_meninggal" required>
                                </div>
                                <div class="invalid-feedback">Harap isi jabatan pegawai</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="satuan_kerja_meninggal" class="form-label">Satuan Kerja <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <input type="text" class="form-control" id="satuan_kerja_meninggal"
                                           name="satuan_kerja_meninggal" required>
                                </div>
                                <div class="invalid-feedback">Harap isi satuan kerja</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pangkat_meninggal" class="form-label">Pangkat Pegawai <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-star"></i></span>
                                    <input type="text" class="form-control" id="pangkat_meninggal"
                                           name="pangkat_meninggal" required>
                                </div>
                                <div class="invalid-feedback">Harap isi pangkat pegawai</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="golongan_meninggal" class="form-label">Golongan/Ruang <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                    <input type="text" class="form-control" id="golongan_meninggal"
                                           name="golongan_meninggal" required>
                                </div>
                                <div class="invalid-feedback">Harap isi golongan/ruang</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <div></div>
                            <button type="button" class="btn btn-primary btn-next-meninggal" data-next="2">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: DOKUMEN --}}
                    <div class="form-step" id="step-2-meninggal">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-file-upload me-2"></i>Upload Dokumen Persyaratan
                            </h5>
                            <p class="text-muted">Unggah dokumen-dokumen yang diperlukan untuk pengajuan pensiun
                                meninggal dunia</p>
                        </div>

                        <div class="alert alert-info">
                            <div class="d-flex">
                                <i class="fas fa-info-circle me-3 mt-1"></i>
                                <div>
                                    <strong>Informasi:</strong> Format file yang diizinkan: PDF, JPG, JPEG, PNG.
                                    Maksimal ukuran file: 2MB per dokumen.
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            <span id="upload-progress-meninggal">0/{{ count($syarat) }}</span> dokumen
                                            terunggah
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            @forelse($syarat as $dokumen)
                                <div class="col-md-6 mb-3">
                                    <div class="file-upload-card h-100">
                                        <label for="file_{{ $dokumen->id }}" class="form-label fw-bold">
                                            {{ $dokumen->nama_dokumen }}
                                            @if ($dokumen->is_required)
                                                <span class="text-danger">*</span>
                                            @else
                                                <span class="text-muted fw-light">(Opsional)</span>
                                            @endif
                                        </label>

                                        <div class="file-input-wrapper">
                                            <input type="file" class="form-control file-input-dynamic"
                                                   id="file_{{ $dokumen->id }}" name="file_{{ $dokumen->id }}"
                                                   accept=".pdf,.jpg,.jpeg,.png"
                                                {{ $dokumen->is_required ? 'required' : '' }}>

                                            <div class="file-preview mt-2 small text-success"
                                                 id="preview-file_{{ $dokumen->id }}"></div>
                                        </div>
                                        <div class="form-text">Tipe: PDF/Gambar, Max: 2MB</div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        Belum ada syarat dokumen yang diatur di database untuk layanan ini
                                        (pensiun-meninggal).
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-meninggal" data-prev="1">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </button>
                            <button type="button" class="btn btn-primary btn-next-meninggal" data-next="3">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 3: KONFIRMASI --}}
                    <div class="form-step" id="step-3-meninggal">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-check-circle me-2"></i>Konfirmasi Pengajuan
                            </h5>
                            <p class="text-muted">Tinjau kembali data yang telah diisi sebelum mengajukan</p>
                        </div>

                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Ringkasan Data Pegawai</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Nama:</strong> <span id="review-nama-meninggal">-</span></p>
                                        <p><strong>NIP:</strong> <span id="review-nip-meninggal">-</span></p>
                                        <p><strong>Jabatan:</strong> <span id="review-jabatan-meninggal">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Satuan Kerja:</strong> <span id="review-satuan-kerja-meninggal">-</span>
                                        </p>
                                        <p><strong>Pangkat:</strong> <span id="review-pangkat-meninggal">-</span></p>
                                        <p><strong>Golongan:</strong> <span id="review-golongan-meninggal">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Dokumen yang Diunggah</h6>
                                <div id="review-documents-meninggal" class="small"></div>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirm-data-meninggal" required>
                            <label class="form-check-label" for="confirm-data-meninggal">
                                Saya menyatakan bahwa data yang saya berikan adalah benar dan siap menanggung
                                konsekuensi
                                hukum jika data tersebut tidak valid.
                            </label>
                            <div class="invalid-feedback">Anda harus menyetujui pernyataan ini sebelum mengajukan</div>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-meninggal" data-prev="2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i>Ajukan Pensiun
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- CSS Styles --}}
    <style>
        .progress-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 3px;
            background-color: #e9ecef;
            z-index: 1;
        }

        .progress-steps .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 8px;
            border: 3px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .step.active .step-circle {
            background-color: #1a73e8;
            border-color: #1a73e8;
            color: white;
        }

        .step-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6c757d;
        }

        .step.active .step-label {
            color: #1a73e8;
            font-weight: 600;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .file-upload-card {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s ease;
            background: white;
        }

        .file-upload-card:hover {
            border-color: #1a73e8;
            background-color: #f8f9fa;
        }

        .file-input-wrapper {
            position: relative;
        }

        .file-preview {
            margin-top: 10px;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 0.875rem;
            display: none;
        }

        .file-preview.has-file {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; max-height: 0; }
            to { opacity: 1; max-height: 100px; }
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }

        .form-control {
            border-left: none;
        }

        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        @media (max-width: 768px) {
            .progress-steps {
                flex-direction: column;
                align-items: flex-start;
            }
            .progress-steps::before { display: none; }
            .step { flex-direction: row; margin-bottom: 10px; }
            .step-circle { margin-right: 10px; margin-bottom: 0; }
        }
    </style>

    {{-- Javascript Logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // --- NOTIFIKASI SYSTEM ---
            @if (session('success'))
            Swal.fire('Berhasil', "{{ session('success') }}", 'success');
            @endif
            @if (session('error'))
            Swal.fire('Gagal', "{{ session('error') }}", 'error');
            @endif
            @if ($errors->any())
            Swal.fire('Validasi Gagal', 'Cek inputan Anda', 'warning');
            @endif

            // --- VARIABLES ---
            const steps = document.querySelectorAll('.form-step');
            const progressSteps = document.querySelectorAll('.progress-steps .step');

            // --- FUNGSI NAVIGASI STEP ---
            function showStep(idx) {
                // Update UI Step
                steps.forEach(s => s.classList.remove('active'));
                progressSteps.forEach(s => s.classList.remove('active'));

                document.getElementById(`step-${idx}-meninggal`).classList.add('active');
                for (let i = 0; i < idx; i++) {
                    progressSteps[i].classList.add('active');
                }

                if (idx == 3) updateReview();
            }

            // --- VALIDASI MANUAL SEBELUM NEXT ---
            function validateStep(currentStep) {
                let isValid = true;
                let errorMsg = '';

                // VALIDASI STEP 1: Data Diri
                if (currentStep == 1) {
                    const nama = document.getElementById('nama_pegawai_meninggal').value.trim();
                    const jabatan = document.getElementById('jabatan_meninggal').value.trim();

                    if (!nama || !jabatan) {
                        isValid = false;
                        errorMsg = 'Silakan lakukan "Cek NIP" dan lengkapi data pegawai terlebih dahulu!';
                    }
                }

                // VALIDASI STEP 2: Dokumen Required
                if (currentStep == 2) {
                    const requiredInputs = document.querySelectorAll('#step-2-meninggal input[type="file"][required]');
                    let emptyCount = 0;

                    requiredInputs.forEach(input => {
                        if (input.files.length === 0) {
                            emptyCount++;
                            input.classList.add('is-invalid');
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });

                    if (emptyCount > 0) {
                        isValid = false;
                        errorMsg = `Masih ada ${emptyCount} dokumen wajib yang belum diunggah!`;
                    }
                }

                if (!isValid) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: errorMsg
                    });
                }

                return isValid;
            }

            // --- EVENT LISTENER TOMBOL NEXT ---
            document.querySelectorAll('.btn-next-meninggal').forEach(btn => {
                btn.addEventListener('click', function () {
                    const nextStepIndex = parseInt(this.dataset.next);
                    const currentStepIndex = nextStepIndex - 1;

                    // Cek validasi strict
                    if (validateStep(currentStepIndex)) {
                        showStep(nextStepIndex);
                    }
                });
            });

            // --- EVENT LISTENER TOMBOL PREV ---
            document.querySelectorAll('.btn-prev-meninggal').forEach(btn => {
                btn.addEventListener('click', function () {
                    showStep(this.dataset.prev);
                });
            });

            // --- FILE UPLOAD HANDLER ---
            function handleFileUpload(input) {
                const previewId = `preview-${input.id}`;
                const previewEl = document.getElementById(previewId);
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (input.files.length > 0) {
                    const file = input.files[0];

                    // Validasi Size
                    if (file.size > maxSize) {
                        input.value = ''; // Reset
                        input.classList.add('is-invalid');
                        input.classList.remove('is-valid');
                        if (previewEl) {
                            previewEl.innerHTML = `<div class="text-danger small"><i class="fas fa-exclamation-circle me-1"></i>Gagal: Ukuran file > 2MB!</div>`;
                            previewEl.style.display = 'block';
                        }
                        Swal.fire('File Terlalu Besar', 'Maksimal ukuran file adalah 2MB.', 'warning');
                        return;
                    }

                    // Sukses
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');

                    if (previewEl) {
                        previewEl.innerHTML = `<div class="text-success small"><i class="fas fa-check-circle me-1"></i> ${file.name}</div>`;
                        previewEl.classList.add('has-file');
                        previewEl.style.display = 'block';
                    }
                } else {
                    input.classList.remove('is-valid');
                    if (previewEl) previewEl.style.display = 'none';
                }
            }

            function updateUploadProgress() {
                const allFileInputs = document.querySelectorAll('#step-2-meninggal input[type="file"]');
                let filledCount = 0;
                allFileInputs.forEach(input => {
                    if (input.files.length > 0) filledCount++;
                });
                const progressEl = document.getElementById('upload-progress-meninggal');
                if (progressEl) progressEl.textContent = `${filledCount}/${allFileInputs.length}`;
            }

            // Attach Event ke Input File
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', function () {
                    handleFileUpload(this);
                    updateUploadProgress();
                });
            });

            // --- LOGIKA CEK NIP ---
            const btnCek = document.getElementById('btn-cek-nip-meninggal');
            if (btnCek) {
                btnCek.addEventListener('click', function () {
                    const nip = document.getElementById('nip_pegawai_meninggal').value;
                    if (!nip) {
                        Swal.fire('Isi NIP!', 'Mohon masukkan NIP terlebih dahulu', 'warning');
                        return;
                    }

                    const oldHtml = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                    this.disabled = true;

                    fetch(`{{ url('/kenaikan-pangkat/ajax/cek-nip') }}/${nip}`)
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                const d = res.data;
                                const set = (id, val) => {
                                    const el = document.getElementById(id);
                                    if (el) el.value = val || '';
                                }

                                set('nama_pegawai_meninggal', d.nama);
                                set('jabatan_meninggal', d.jabatan);
                                set('pangkat_meninggal', d.pangkat);
                                set('nip_display_meninggal', d.nip);
                                set('satuan_kerja_meninggal', d.unit_kerja);
                                set('golongan_meninggal', d.golongan_ruang);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data Ditemukan',
                                    text: 'Data pegawai berhasil dimuat.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire('Gagal', 'NIP tidak ditemukan dalam database kepegawaian.', 'error');
                            }
                        })
                        .catch(() => Swal.fire('Error', 'Gagal koneksi ke server', 'error'))
                        .finally(() => {
                            this.innerHTML = oldHtml;
                            this.disabled = false;
                        });
                });
            }

            // --- LOGIKA REVIEW (STEP 3) - SMART ICON ---
            function updateReview() {
                const get = (id) => document.getElementById(id)?.value || '-';
                const setText = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = val;
                };

                setText('review-nama-meninggal', get('nama_pegawai_meninggal'));
                setText('review-nip-meninggal', get('nip_display_meninggal'));
                setText('review-jabatan-meninggal', get('jabatan_meninggal'));
                setText('review-satuan-kerja-meninggal', get('satuan_kerja_meninggal'));
                setText('review-pangkat-meninggal', get('pangkat_meninggal'));
                setText('review-golongan-meninggal', get('golongan_meninggal'));

                // Smart Icon Logic
                const docContainer = document.getElementById('review-documents-meninggal');
                docContainer.innerHTML = '';
                let hasFile = false;

                document.querySelectorAll('input[type="file"]').forEach(input => {
                    if (input.files.length > 0) {
                        hasFile = true;
                        const fileName = input.files[0].name;
                        const fileSize = (input.files[0].size / 1024).toFixed(1) + ' KB';

                        const labelEl = input.closest('.file-upload-card').querySelector('label');
                        let labelText = labelEl.innerText.replace('*', '').replace('(Opsional)', '').trim();

                        // Detect Extension
                        const ext = fileName.split('.').pop().toLowerCase();
                        let iconClass = 'fa-file';
                        let iconColor = 'text-secondary';

                        if (ext === 'pdf') {
                            iconClass = 'fa-file-pdf';
                            iconColor = 'text-danger';
                        } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
                            iconClass = 'fa-file-image';
                            iconColor = 'text-primary';
                        }

                        const item = document.createElement('div');
                        item.className = 'd-flex align-items-center mb-2 p-2 border rounded bg-white shadow-sm';
                        item.innerHTML = `
                            <div class="me-3">
                                <i class="fas ${iconClass} ${iconColor} fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">${labelText}</div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <span class="text-success small"><i class="fas fa-check-circle me-1"></i>${fileName}</span>
                                    <span class="text-muted small" style="font-size: 0.75rem;">${fileSize}</span>
                                </div>
                            </div>
                        `;
                        docContainer.appendChild(item);
                    }
                });

                if (!hasFile) {
                    docContainer.innerHTML = '<div class="alert alert-warning py-2 small"><i class="fas fa-exclamation-triangle me-1"></i>Belum ada dokumen yang diunggah.</div>';
                }
            }

            // --- SUBMIT FORM ---
            document.getElementById('form-pensiun-meninggal').addEventListener('submit', function (e) {
                if (!document.getElementById('confirm-data-meninggal').checked) {
                    e.preventDefault();
                    Swal.fire('Konfirmasi Diperlukan', 'Anda harus mencentang pernyataan kebenaran data sebelum mengajukan.', 'warning');
                } else {
                    Swal.fire({
                        title: 'Sedang Mengirim...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                }
            });

            showStep(1);
        });
    </script>
@endsection
