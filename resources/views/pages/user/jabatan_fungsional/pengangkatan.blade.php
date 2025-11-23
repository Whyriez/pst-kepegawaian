@extends('layouts.user.app')
@section('title', 'Jabatan Fungsional - Pengangkatan')

@section('content')
    {{-- HAPUS CLASS content-template AGAR LANGSUNG MUNCUL --}}
    <div class="container-fluid p-0">

        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Form Pengangkatan Jabatan Fungsional</h2>
                    <p class="text-muted mb-0">Formulir untuk pengajuan pengangkatan pertama kali dalam jabatan fungsional</p>
                </div>
                {{-- UBAH BUTTON JADI LINK KE DASHBOARD --}}
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>

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
                {{-- TAMBAHKAN ACTION, METHOD, CSRF, DAN ENCTYPE --}}
                <form id="form-jf-pengangkatan" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-step active" id="step-1-jf-pengangkatan">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-user me-2"></i>Data Diri Pegawai
                            </h5>
                            <p class="text-muted">Isi data diri pegawai yang mengajukan pengangkatan jabatan fungsional</p>
                        </div>

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Cek Data dengan NIP</h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="nip_pegawai_jf_pengangkatan"
                                                name="nip_pegawai_jf_pengangkatan" placeholder="Masukkan NIP Pegawai">
                                            <button class="btn btn-outline-primary" type="button"
                                                id="btn-cek-nip-jf-pengangkatan">
                                                <i class="fas fa-search me-2"></i>Cek NIP
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_pegawai_jf_pengangkatan" class="form-label">Nama Pegawai <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="nama_pegawai_jf_pengangkatan"
                                        name="nama_pegawai_jf_pengangkatan" required>
                                </div>
                                <div class="invalid-feedback">Harap isi nama pegawai</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nip_display_jf_pengangkatan" class="form-label">NIP Pegawai <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" class="form-control" id="nip_display_jf_pengangkatan"
                                        name="nip_display_jf_pengangkatan" required>
                                </div>
                                <div class="invalid-feedback">Harap isi NIP pegawai</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jabatan_jf_pengangkatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    <input type="text" class="form-control" id="jabatan_jf_pengangkatan"
                                        name="jabatan_jf_pengangkatan" required>
                                </div>
                                <div class="invalid-feedback">Harap isi jabatan pegawai</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="pangkat_jf_pengangkatan" class="form-label">Pangkat <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-star"></i></span>
                                    <input type="text" class="form-control" id="pangkat_jf_pengangkatan"
                                        name="pangkat_jf_pengangkatan" required>
                                </div>
                                <div class="invalid-feedback">Harap isi pangkat pegawai</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="usul_jabatan_jf_pengangkatan" class="form-label">Usul Jabatan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    <input type="text" class="form-control" id="usul_jabatan_jf_pengangkatan"
                                        name="usul_jabatan_jf_pengangkatan" required>
                                </div>
                                <div class="invalid-feedback">Harap isi usul jabatan pegawai</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="satuan_kerja_jf_pengangkatan" class="form-label">Satuan Kerja <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <input type="text" class="form-control" id="satuan_kerja_jf_pengangkatan"
                                        name="satuan_kerja_jf_pengangkatan" required>
                                </div>
                                <div class="invalid-feedback">Harap isi satuan kerja</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="golongan_ruang_jf_pengangkatan" class="form-label">Golongan/Ruang <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                    <input type="text" class="form-control" id="golongan_ruang_jf_pengangkatan"
                                        name="golongan_ruang_jf_pengangkatan" required>
                                </div>
                                <div class="invalid-feedback">Harap isi golongan dan ruang pegawai</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="usul_satuan_kerja_jf_pengangkatan" class="form-label">Usul Satuan Kerja <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <input type="text" class="form-control" id="usul_satuan_kerja_jf_pengangkatan"
                                        name="usul_satuan_kerja_jf_pengangkatan" required>
                                </div>
                                <div class="invalid-feedback">Harap isi usul satuan kerja</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <div></div> <button type="button" class="btn btn-primary btn-next-jf-pengangkatan" data-next="2">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-step" id="step-2-jf-pengangkatan">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-file-upload me-2"></i>Upload Dokumen Persyaratan
                            </h5>
                            <p class="text-muted">Unggah dokumen-dokumen yang diperlukan untuk pengajuan pengangkatan jabatan fungsional</p>
                        </div>

                        <div class="alert alert-info">
                            <div class="d-flex">
                                <i class="fas fa-info-circle me-3 mt-1"></i>
                                <div>
                                    <strong>Informasi:</strong> Format file yang diizinkan: PDF. Maksimal ukuran file: 2MB per dokumen.
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            <span id="upload-progress-jf-pengangkatan">0/9</span> dokumen terunggah
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="file-upload-card">
                                    <label for="sk_pns_jf_pengangkatan" class="form-label">SK PNS <span class="text-danger">*</span></label>
                                    <div class="file-input-wrapper">
                                        <input type="file" class="form-control" id="sk_pns_jf_pengangkatan"
                                            name="sk_pns_jf_pengangkatan" accept=".pdf" required>
                                        <div class="file-preview" id="preview-sk_pns_jf_pengangkatan"></div>
                                    </div>
                                    <div class="form-text">Type File: PDF, Max size: 2MB</div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="file-upload-card">
                                    <label for="surat_keterangan_sehat_jf_pengangkatan" class="form-label">Surat Keterangan Sehat Jasmani dan Rohani</label>
                                    <div class="file-input-wrapper">
                                        <input type="file" class="form-control"
                                            id="surat_keterangan_sehat_jf_pengangkatan"
                                            name="surat_keterangan_sehat_jf_pengangkatan" accept=".pdf">
                                        <div class="file-preview" id="preview-surat_keterangan_sehat_jf_pengangkatan"></div>
                                    </div>
                                    <div class="form-text">Type File: PDF, Max size: 2MB (Tidak Wajib)</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="file-upload-card">
                                    <label for="ijazah_jf_pengangkatan" class="form-label">Ijazah</label>
                                    <div class="file-input-wrapper">
                                        <input type="file" class="form-control" id="ijazah_jf_pengangkatan"
                                            name="ijazah_jf_pengangkatan" accept=".pdf">
                                        <div class="file-preview" id="preview-ijazah_jf_pengangkatan"></div>
                                    </div>
                                    <div class="form-text">Type File: PDF, Max size: 2MB (Tidak Wajib)</div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="file-upload-card">
                                    <label for="skp_2024_jf_pengangkatan" class="form-label">SKP Tahun 2024 <span class="text-danger">*</span></label>
                                    <div class="file-input-wrapper">
                                        <input type="file" class="form-control" id="skp_2024_jf_pengangkatan"
                                            name="skp_2024_jf_pengangkatan" accept=".pdf" required>
                                        <div class="file-preview" id="preview-skp_2024_jf_pengangkatan"></div>
                                    </div>
                                    <div class="form-text">Type File: PDF, Max size: 2MB</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="file-upload-card">
                                    <label for="skp_2023_jf_pengangkatan" class="form-label">SKP Tahun 2023</label>
                                    <div class="file-input-wrapper">
                                        <input type="file" class="form-control" id="skp_2023_jf_pengangkatan"
                                            name="skp_2023_jf_pengangkatan" accept=".pdf">
                                        <div class="file-preview" id="preview-skp_2023_jf_pengangkatan"></div>
                                    </div>
                                    <div class="form-text">Type File: PDF, Max size: 2MB (Tidak Wajib untuk Calon PNS)</div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="file-upload-card">
                                    <label for="sk_pak_jf_pengangkatan" class="form-label">SK PAK</label>
                                    <div class="file-input-wrapper">
                                        <input type="file" class="form-control" id="sk_pak_jf_pengangkatan"
                                            name="sk_pak_jf_pengangkatan" accept=".pdf">
                                        <div class="file-preview" id="preview-sk_pak_jf_pengangkatan"></div>
                                    </div>
                                    <div class="form-text">Type File: PDF, Max size: 2MB (Tidak Wajib untuk Calon PNS)</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="file-upload-card">
                                    <label for="analisis_jabatan_jf_pengangkatan" class="form-label">Analisis Jabatan dan Analisis Beban Kerja <span class="text-danger">*</span></label>
                                    <div class="file-input-wrapper">
                                        <input type="file" class="form-control" id="analisis_jabatan_jf_pengangkatan"
                                            name="analisis_jabatan_jf_pengangkatan" accept=".pdf" required>
                                        <div class="file-preview" id="preview-analisis_jabatan_jf_pengangkatan"></div>
                                    </div>
                                    <div class="form-text">Type File: PDF, Max size: 2MB</div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="file-upload-card">
                                    <label for="surat_pengumuman_uji_kompetensi_jf_pengangkatan" class="form-label">Surat Pengumuman Hasil Uji Kompetensi</label>
                                    <div class="file-input-wrapper">
                                        <input type="file" class="form-control"
                                            id="surat_pengumuman_uji_kompetensi_jf_pengangkatan"
                                            name="surat_pengumuman_uji_kompetensi_jf_pengangkatan" accept=".pdf">
                                        <div class="file-preview" id="preview-surat_pengumuman_uji_kompetensi_jf_pengangkatan"></div>
                                    </div>
                                    <div class="form-text">Type File: PDF, Max size: 2MB (Tidak Wajib untuk Calon PNS)</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="file-upload-card">
                                    <label for="surat_pengalaman_kerja_jf_pengangkatan" class="form-label">Surat Pengalaman Kerja pada Jabatan minimal 1 Tahun</label>
                                    <div class="file-input-wrapper">
                                        <input type="file" class="form-control"
                                            id="surat_pengalaman_kerja_jf_pengangkatan"
                                            name="surat_pengalaman_kerja_jf_pengangkatan" accept=".pdf">
                                        <div class="file-preview" id="preview-surat_pengalaman_kerja_jf_pengangkatan"></div>
                                    </div>
                                    <div class="form-text">Type File: PDF, Max size: 2MB (Tidak Wajib untuk Calon PNS)</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-jf-pengangkatan"
                                data-prev="1">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </button>
                            <button type="button" class="btn btn-primary btn-next-jf-pengangkatan" data-next="3">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-step" id="step-3-jf-pengangkatan">
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
                                        <p><strong>Nama:</strong> <span id="review-nama-jf-pengangkatan">-</span></p>
                                        <p><strong>NIP:</strong> <span id="review-nip-jf-pengangkatan">-</span></p>
                                        <p><strong>Jabatan:</strong> <span id="review-jabatan-jf-pengangkatan">-</span></p>
                                        <p><strong>Pangkat:</strong> <span id="review-pangkat-jf-pengangkatan">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Usul Jabatan:</strong> <span id="review-usul-jabatan-jf-pengangkatan">-</span></p>
                                        <p><strong>Satuan Kerja:</strong> <span id="review-satuan-kerja-jf-pengangkatan">-</span></p>
                                        <p><strong>Golongan/Ruang:</strong> <span id="review-golongan-ruang-jf-pengangkatan">-</span></p>
                                        <p><strong>Usul Satuan Kerja:</strong> <span id="review-usul-satuan-kerja-jf-pengangkatan">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Dokumen yang Diunggah</h6>
                                <div id="review-documents-jf-pengangkatan" class="small">
                                    </div>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirm-data-jf-pengangkatan" required>
                            <label class="form-check-label" for="confirm-data-jf-pengangkatan">
                                Saya menyatakan bahwa data yang saya berikan adalah benar dan siap menanggung konsekuensi hukum jika data tersebut tidak valid.
                            </label>
                            <div class="invalid-feedback">Anda harus menyetujui pernyataan ini sebelum mengajukan</div>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-jf-pengangkatan"
                                data-prev="2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i>Ajukan Pengangkatan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Progress Steps */
        .progress-steps { display: flex; justify-content: space-between; position: relative; }
        .progress-steps::before { content: ''; position: absolute; top: 15px; left: 0; right: 0; height: 3px; background-color: #e9ecef; z-index: 1; }
        .progress-steps .step { display: flex; flex-direction: column; align-items: center; position: relative; z-index: 2; }
        .step-circle { width: 40px; height: 40px; border-radius: 50%; background-color: #e9ecef; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-bottom: 8px; border: 3px solid #e9ecef; transition: all 0.3s ease; }
        .step.active .step-circle { background-color: #1a73e8; border-color: #1a73e8; color: white; }
        .step-label { font-size: 0.875rem; font-weight: 500; color: #6c757d; }
        .step.active .step-label { color: #1a73e8; font-weight: 600; }

        /* Form Steps */
        .form-step { display: none; }
        .form-step.active { display: block; animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* File Upload Cards */
        .file-upload-card { border: 2px dashed #dee2e6; border-radius: 8px; padding: 15px; transition: all 0.3s ease; background: white; }
        .file-upload-card:hover { border-color: #1a73e8; background-color: #f8f9fa; }
        .file-input-wrapper { position: relative; }
        .file-preview { margin-top: 10px; padding: 8px; background: #f8f9fa; border-radius: 4px; font-size: 0.875rem; display: none; }
        .file-preview.has-file { display: block; animation: slideDown 0.3s ease; }
        @keyframes slideDown { from { opacity: 0; max-height: 0; } to { opacity: 1; max-height: 100px; } }

        /* Input Groups & Responsive */
        .input-group-text { background-color: #f8f9fa; border-right: none; }
        .form-control { border-left: none; }
        .form-control:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); }
        @media (max-width: 768px) { .progress-steps { flex-direction: column; align-items: flex-start; } .progress-steps::before { display: none; } .step { flex-direction: row; margin-bottom: 10px; } .step-circle { margin-right: 10px; margin-bottom: 0; } }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            console.log('JF Pengangkatan Form Initialized');

            const form = document.getElementById('form-jf-pengangkatan');
            const btnCekNip = document.getElementById('btn-cek-nip-jf-pengangkatan');
            const nipInput = document.getElementById('nip_pegawai_jf_pengangkatan');
            const nipDisplay = document.getElementById('nip_display_jf_pengangkatan');
            
            const steps = document.querySelectorAll('.form-step');
            const progressSteps = document.querySelectorAll('.progress-steps .step');
            let currentStep = 1;

            // --- 1. NAVIGATION LOGIC ---
            function showStep(step) {
                steps.forEach(s => s.classList.remove('active'));
                progressSteps.forEach(s => s.classList.remove('active'));

                document.getElementById(`step-${step}-jf-pengangkatan`).classList.add('active');
                
                progressSteps.forEach(s => {
                    if(parseInt(s.dataset.step) <= step) {
                        s.classList.add('active');
                    }
                });

                currentStep = step;
                if(step === 3) updateReviewData();
            }

            document.querySelectorAll('.btn-next-jf-pengangkatan').forEach(button => {
                button.addEventListener('click', function() {
                    const nextStep = parseInt(this.getAttribute('data-next'));
                    if (validateStep(currentStep)) {
                        showStep(nextStep);
                    }
                });
            });

            document.querySelectorAll('.btn-prev-jf-pengangkatan').forEach(button => {
                button.addEventListener('click', function() {
                    const prevStep = parseInt(this.getAttribute('data-prev'));
                    showStep(prevStep);
                });
            });

            // --- 2. VALIDATION LOGIC ---
            function validateStep(step) {
                let isValid = true;
                
                if (step === 1) {
                    const fields = document.querySelectorAll('#step-1-jf-pengangkatan [required]');
                    fields.forEach(field => {
                        if (!field.value.trim()) {
                            field.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            field.classList.remove('is-invalid');
                            field.classList.add('is-valid');
                        }
                    });
                    if (!isValid) Swal.fire('Perhatian', 'Harap lengkapi semua field yang wajib diisi pada bagian Data Pegawai', 'warning');
                } 
                else if (step === 2) {
                    const fileInputs = document.querySelectorAll('#step-2-jf-pengangkatan input[type="file"][required]');
                    let uploadedCount = 0;
                    fileInputs.forEach(input => {
                        if (input.files.length > 0) uploadedCount++;
                        else input.classList.add('is-invalid');
                    });

                    if (uploadedCount < fileInputs.length) {
                        Swal.fire('Perhatian', `Harap unggah semua dokumen wajib. (${uploadedCount}/${fileInputs.length} terunggah)`, 'warning');
                        isValid = false;
                    }
                }

                return isValid;
            }

            // Real-time validation removal
            document.querySelectorAll('input, select').forEach(el => {
                el.addEventListener('input', function() {
                    if(this.value.trim()) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            });

            // --- 3. FILE UPLOAD LOGIC ---
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', function() {
                    handleFileUpload(this);
                    updateUploadProgress();
                });
            });

            function handleFileUpload(input) {
                const preview = document.getElementById(`preview-${input.id}`);
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (input.files.length > 0) {
                    const file = input.files[0];
                    if (file.size > maxSize) {
                        input.classList.add('is-invalid');
                        preview.innerHTML = `<div class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>File > 2MB</div>`;
                        preview.classList.add('has-file');
                        input.value = ''; 
                    } else {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                        preview.innerHTML = `<div class="text-success"><i class="fas fa-check-circle me-2"></i>${file.name}</div>`;
                        preview.classList.add('has-file');
                    }
                }
            }

            function updateUploadProgress() {
                const requiredFiles = document.querySelectorAll('#step-2-jf-pengangkatan input[type="file"][required]');
                let count = 0;
                requiredFiles.forEach(inp => { if(inp.files.length > 0) count++; });
                const progressEl = document.getElementById('upload-progress-jf-pengangkatan');
                if(progressEl) progressEl.textContent = `${count}/${requiredFiles.length}`;
            }

            // --- 4. CEK NIP LOGIC (DUMMY) ---
            if (btnCekNip) {
                btnCekNip.addEventListener('click', function() {
                    const nip = nipInput.value.trim();
                    if (!nip) {
                        Swal.fire('Info', 'Masukkan NIP terlebih dahulu', 'info');
                        return;
                    }

                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    this.disabled = true;

                    setTimeout(() => {
                        const data = cariDataPegawai(nip);
                        if(data) {
                            document.getElementById('nama_pegawai_jf_pengangkatan').value = data.nama;
                            document.getElementById('jabatan_jf_pengangkatan').value = data.jabatan;
                            document.getElementById('pangkat_jf_pengangkatan').value = data.pangkat;
                            document.getElementById('usul_jabatan_jf_pengangkatan').value = data.usul_jabatan;
                            document.getElementById('satuan_kerja_jf_pengangkatan').value = data.satuan_kerja;
                            document.getElementById('golongan_ruang_jf_pengangkatan').value = data.golongan_ruang;
                            document.getElementById('usul_satuan_kerja_jf_pengangkatan').value = data.usul_satuan_kerja;
                            nipDisplay.value = data.nip;
                            
                            document.querySelectorAll('#step-1-jf-pengangkatan input').forEach(i => i.classList.add('is-valid'));
                            
                            Swal.fire('Berhasil', 'Data pegawai ditemukan', 'success');
                        } else {
                            Swal.fire('Gagal', 'NIP tidak ditemukan', 'error');
                        }
                        this.innerHTML = originalHtml;
                        this.disabled = false;
                    }, 1000);
                });
            }

            function cariDataPegawai(nip) {
                const db = {
                    '123456789012345678': {
                        nama: 'Dr. Ahmad Fauzi, M.Kom.', nip: '123456789012345678',
                        jabatan: 'Kepala Bidang TI', pangkat: 'Pembina Tk. I',
                        usul_jabatan: 'Pranata Komputer Ahli Madya', satuan_kerja: 'Dinas Kominfo',
                        golongan_ruang: 'IV/b', usul_satuan_kerja: 'Dinas Kominfo Prov. Gorontalo'
                    },
                    '198765432109876543': {
                        nama: 'Siti Aminah, S.E.', nip: '198765432109876543',
                        jabatan: 'Kasubag Umum', pangkat: 'Penata Tk. I',
                        usul_jabatan: 'Analis Kepegawaian Ahli Muda', satuan_kerja: 'BKD',
                        golongan_ruang: 'III/d', usul_satuan_kerja: 'BKD Prov. Gorontalo'
                    }
                };
                return db[nip] || null;
            }

            // --- 5. UPDATE REVIEW ---
            function updateReviewData() {
                document.getElementById('review-nama-jf-pengangkatan').textContent = document.getElementById('nama_pegawai_jf_pengangkatan').value || '-';
                document.getElementById('review-nip-jf-pengangkatan').textContent = document.getElementById('nip_display_jf_pengangkatan').value || '-';
                document.getElementById('review-jabatan-jf-pengangkatan').textContent = document.getElementById('jabatan_jf_pengangkatan').value || '-';
                document.getElementById('review-pangkat-jf-pengangkatan').textContent = document.getElementById('pangkat_jf_pengangkatan').value || '-';
                document.getElementById('review-usul-jabatan-jf-pengangkatan').textContent = document.getElementById('usul_jabatan_jf_pengangkatan').value || '-';
                document.getElementById('review-satuan-kerja-jf-pengangkatan').textContent = document.getElementById('satuan_kerja_jf_pengangkatan').value || '-';
                document.getElementById('review-golongan-ruang-jf-pengangkatan').textContent = document.getElementById('golongan_ruang_jf_pengangkatan').value || '-';
                document.getElementById('review-usul-satuan-kerja-jf-pengangkatan').textContent = document.getElementById('usul_satuan_kerja_jf_pengangkatan').value || '-';

                const docContainer = document.getElementById('review-documents-jf-pengangkatan');
                let html = '';
                document.querySelectorAll('input[type="file"]').forEach(input => {
                    if(input.files.length > 0) {
                        const label = input.closest('.file-upload-card').querySelector('label').textContent.replace('*', '');
                        html += `<div class="text-success mb-1"><i class="fas fa-check-circle me-2"></i>${label}: ${input.files[0].name}</div>`;
                    }
                });
                docContainer.innerHTML = html || '<div class="text-muted">Belum ada dokumen</div>';
            }

            // FORM SUBMIT
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if(!document.getElementById('confirm-data-jf-pengangkatan').checked) {
                    Swal.fire('Perhatian', 'Anda harus menyetujui konfirmasi data', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Kirim Pengajuan?',
                    text: "Pastikan data sudah benar!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kirim',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // this.submit(); // Uncomment jika backend siap
                        Swal.fire('Terkirim!', 'Pengajuan Anda sedang diproses.', 'success').then(() => {
                            window.location.reload();
                        });
                    }
                });
            });

            // Sync NIP Input
            nipInput.addEventListener('input', function() {
                nipDisplay.value = this.value;
            });
        });
    </script>
@endsection