@extends('layouts.user.app')
@section('title', 'Kenaikan Pangkat Fungsional')

@section('content')
    {{-- HAPUS CLASS content-template AGAR LANGSUNG MUNCUL --}}
    <div class="container-fluid p-0">

        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Form KP Fungsional</h2>
                    <p class="text-muted mb-0">Formulir untuk kenaikan pangkat fungsional</p>
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
                {{-- TAMBAHKAN FORM ACTION DAN ENCTYPE --}}
                <form id="form-kp-fungsional" action="{{ route('kp.fungsional.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-step active" id="step-1-kp-fungsional">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-user me-2"></i>Data Diri Pegawai
                            </h5>
                            <p class="text-muted">Isi data diri pegawai yang mengajukan kenaikan pangkat fungsional</p>
                        </div>

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Cek Data dengan NIP</h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="nip_pegawai_kp_fungsional"
                                                name="nip_pegawai_kp_fungsional" placeholder="Masukkan NIP Pegawai"
                                                value="{{ Auth::user()->pegawai->nip ?? '' }}">
                                            <button class="btn btn-outline-primary" type="button"
                                                id="btn-cek-nip-kp-fungsional">
                                                <i class="fas fa-search me-2"></i>Cek NIP
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_pegawai_kp_fungsional" class="form-label">Nama Pegawai <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="nama_pegawai_kp_fungsional"
                                        name="nama_pegawai_kp_fungsional" required>
                                </div>
                                <div class="invalid-feedback">Harap isi nama pegawai</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="jabatan_kp_fungsional" class="form-label">Jabatan Pegawai <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    <input type="text" class="form-control" id="jabatan_kp_fungsional"
                                        name="jabatan_kp_fungsional" required>
                                </div>
                                <div class="invalid-feedback">Harap isi jabatan pegawai</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pangkat_kp_fungsional" class="form-label">Pangkat Pegawai <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-star"></i></span>
                                    <input type="text" class="form-control" id="pangkat_kp_fungsional"
                                        name="pangkat_kp_fungsional" required>
                                </div>
                                <div class="invalid-feedback">Harap isi pangkat pegawai</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nip_display_kp_fungsional" class="form-label">NIP Pegawai <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" class="form-control" id="nip_display_kp_fungsional"
                                        name="nip_display_kp_fungsional" required>
                                </div>
                                <div class="invalid-feedback">Harap isi NIP pegawai</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="unit_kerja_kp_fungsional" class="form-label">Unit Kerja Pegawai <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <input type="text" class="form-control" id="unit_kerja_kp_fungsional"
                                        name="unit_kerja_kp_fungsional" required>
                                </div>
                                <div class="invalid-feedback">Harap isi unit kerja pegawai</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="golongan_ruang_kp_fungsional" class="form-label">Golongan Ruang Pegawai <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                    <input type="text" class="form-control" id="golongan_ruang_kp_fungsional"
                                        name="golongan_ruang_kp_fungsional" required>
                                </div>
                                <div class="invalid-feedback">Harap isi golongan ruang pegawai</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <div></div> <button type="button" class="btn btn-primary btn-next-kp-fungsional"
                                data-next="2">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-step" id="step-2-kp-fungsional">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-file-upload me-2"></i>Upload Dokumen Persyaratan
                            </h5>
                            <p class="text-muted">Unggah dokumen yang diperlukan (Otomatis dari Database)</p>
                        </div>

                        <div class="alert alert-info">
                            <div class="d-flex">
                                <i class="fas fa-info-circle me-3 mt-1"></i>
                                <div>
                                    <strong>Informasi:</strong> Format file PDF. Maksimal 2MB.
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            <span id="upload-progress-kp-fungsional">0/{{ count($syarat) }}</span> dokumen
                                            terunggah
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PERBAIKAN: Menggunakan Loop agar 'name' sesuai dengan Controller (file_1, file_2, dst) --}}
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
                                            {{-- PENTING: name="file_{id}" agar terbaca oleh Controller --}}
                                            <input type="file" class="form-control file-input-dynamic"
                                                id="file_{{ $dokumen->id }}" name="file_{{ $dokumen->id }}"
                                                accept=".pdf" {{ $dokumen->is_required ? 'required' : '' }}>

                                            {{-- Div untuk preview nama file setelah upload --}}
                                            <div class="file-preview mt-2 small text-success"
                                                id="preview-file_{{ $dokumen->id }}"></div>
                                        </div>
                                        <div class="form-text">Tipe: PDF, Max: 2MB</div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        Belum ada syarat dokumen yang diatur di database untuk layanan ini (kp-fungsional).
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label for="periode_kenaikan_pangkat_kp_fungsional" class="form-label">Periode Kenaikan
                                    Pangkat <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <select class="form-control" id="periode_kenaikan_pangkat_kp_fungsional"
                                        name="periode_kenaikan_pangkat_kp_fungsional" required>
                                        <option value="">Pilih Periode</option>
                                        <option value="April">April</option>
                                        <option value="Oktober">Oktober</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-kp-fungsional"
                                data-prev="1">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </button>
                            <button type="button" class="btn btn-primary btn-next-kp-fungsional" data-next="3">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-step" id="step-3-kp-fungsional">
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
                                        <p><strong>Nama:</strong> <span id="review-nama-kp-fungsional">-</span></p>
                                        <p><strong>NIP:</strong> <span id="review-nip-kp-fungsional">-</span></p>
                                        <p><strong>Jabatan:</strong> <span id="review-jabatan-kp-fungsional">-</span></p>
                                        <p><strong>Unit Kerja:</strong> <span id="review-unit-kerja-kp-fungsional">-</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Pangkat:</strong> <span id="review-pangkat-kp-fungsional">-</span></p>
                                        <p><strong>Golongan Ruang:</strong> <span
                                                id="review-golongan-ruang-kp-fungsional">-</span></p>
                                        <p><strong>Periode:</strong> <span id="review-periode-kp-fungsional">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Dokumen yang Diunggah</h6>
                                <div id="review-documents-kp-fungsional" class="small">
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirm-data-kp-fungsional" required>
                            <label class="form-check-label" for="confirm-data-kp-fungsional">
                                Saya menyatakan bahwa data yang saya berikan adalah benar dan siap menanggung konsekuensi
                                hukum jika data tersebut tidak valid.
                            </label>
                            <div class="invalid-feedback">Anda harus menyetujui pernyataan ini sebelum mengajukan</div>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-kp-fungsional"
                                data-prev="2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i>Ajukan KP Fungsional
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Progress Steps */
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

        /* Form Steps */
        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* File Upload Cards */
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
            from {
                opacity: 0;
                max-height: 0;
            }

            to {
                opacity: 1;
                max-height: 100px;
            }
        }

        /* Input Groups & Responsive */
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

            .progress-steps::before {
                display: none;
            }

            .step {
                flex-direction: row;
                margin-bottom: 10px;
            }

            .step-circle {
                margin-right: 10px;
                margin-bottom: 0;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- NOTIFIKASI ---
            @if (session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonText: 'OK' });
            @endif
            @if (session('error'))
                Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", confirmButtonText: 'Tutup' });
            @endif
            @if ($errors->any())
                Swal.fire({ icon: 'warning', title: 'Perhatian!', html: '<ul style="text-align: left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>' });
            @endif

            // --- 1. STEPPER LOGIC ---
            const steps = document.querySelectorAll('.form-step');
            const progressSteps = document.querySelectorAll('.progress-steps .step');
            let currentStep = 1;

            function showStep(stepIndex) {
                steps.forEach(el => el.classList.remove('active'));
                progressSteps.forEach(el => el.classList.remove('active'));

                document.getElementById(`step-${stepIndex}-kp-fungsional`).classList.add('active');

                for (let i = 0; i < stepIndex; i++) {
                    if (progressSteps[i]) progressSteps[i].classList.add('active');
                }
                currentStep = parseInt(stepIndex);
                if (stepIndex == 3) updateReviewData();
            }

            // --- 2. FILE UPLOAD & PROGRESS (BAGIAN YANG DIREVISI) ---
            
            // Fungsi Update Counter (0/8)
            function updateUploadProgress() {
                // Ambil SEMUA input file di step 2 (baik required maupun tidak)
                const allFileInputs = document.querySelectorAll('#step-2-kp-fungsional input[type="file"]');
                let filledCount = 0;

                allFileInputs.forEach(input => {
                    if (input.files.length > 0) {
                        filledCount++;
                    }
                });

                // Update teks HTML
                const progressEl = document.getElementById('upload-progress-kp-fungsional');
                if (progressEl) {
                    progressEl.textContent = `${filledCount}/${allFileInputs.length}`;
                }
            }

            // Fungsi Preview File
            function handleFileUpload(input) {
                const previewId = `preview-${input.id}`;
                const previewEl = document.getElementById(previewId);
                
                // Definisi Max Size 2MB (2 * 1024 * 1024 bytes)
                const maxSize = 2 * 1024 * 1024; 

                if (input.files.length > 0) {
                    const file = input.files[0];

                    // --- VALIDASI UKURAN FILE ---
                    if (file.size > maxSize) {
                        // 1. Reset input (hapus file yang dipilih)
                        input.value = ''; 
                        
                        // 2. Beri tanda error pada input
                        input.classList.add('is-invalid');
                        input.classList.remove('is-valid');

                        // 3. Tampilkan pesan error di bawah input
                        if (previewEl) {
                            previewEl.innerHTML = `<div class="text-danger small"><i class="fas fa-exclamation-circle me-1"></i>Gagal: Ukuran file melebihi 2MB!</div>`;
                            previewEl.style.display = 'block';
                        }
                        
                        // Opsional: Tampilkan SweetAlert
                        Swal.fire('File Terlalu Besar', 'Maksimal ukuran file adalah 2MB.', 'warning');
                        
                        return; // Stop, jangan lanjut ke preview sukses
                    }
                    // ----------------------------

                    // Jika lolos validasi, tampilkan preview sukses
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                    
                    if (previewEl) {
                        previewEl.innerHTML = `<div class="text-success small"><i class="fas fa-check-circle me-1"></i> ${file.name}</div>`;
                        previewEl.style.display = 'block';
                    }
                }
            }

            // Pasang Event Listener ke Semua Input File
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', function() {
                    handleFileUpload(this);     // Tampilkan nama file
                    updateUploadProgress();     // Update angka 0/8
                });
            });


            // --- 3. TOMBOL NEXT & VALIDASI ---
            document.querySelectorAll('.btn-next-kp-fungsional').forEach(btn => {
                btn.addEventListener('click', function() {
                    const nextStep = this.getAttribute('data-next');
                    
                    // Validasi Step 1 (Data Diri)
                    if (currentStep == 1) {
                        const nama = document.getElementById('nama_pegawai_kp_fungsional').value;
                        if (!nama) {
                            Swal.fire('Data Kosong', 'Silakan klik tombol "Cek NIP" dulu!', 'warning');
                            return;
                        }
                    }
                    
                    // Validasi Step 2 (Dokumen Wajib)
                    if (currentStep == 2) {
                        // Cek Periode
                        const periode = document.getElementById('periode_kenaikan_pangkat_kp_fungsional');
                        if (!periode.value) {
                            periode.classList.add('is-invalid');
                            Swal.fire('Peringatan', 'Silakan pilih Periode Kenaikan Pangkat!', 'warning');
                            return;
                        } else {
                            periode.classList.remove('is-invalid');
                        }

                        // Cek Dokumen Wajib
                        const requiredFiles = document.querySelectorAll('#step-2-kp-fungsional input[type="file"][required]');
                        let emptyFiles = 0;
                        requiredFiles.forEach(input => {
                            if (input.files.length === 0) {
                                input.classList.add('is-invalid');
                                emptyFiles++;
                            } else {
                                input.classList.remove('is-invalid');
                            }
                        });

                        if (emptyFiles > 0) {
                            Swal.fire('Dokumen Kurang', 'Harap lengkapi semua dokumen bertanda bintang (*)!', 'warning');
                            return; // Stop jangan lanjut
                        }
                    }

                    showStep(nextStep);
                });
            });

            document.querySelectorAll('.btn-prev-kp-fungsional').forEach(btn => {
                btn.addEventListener('click', function() {
                    showStep(this.getAttribute('data-prev'));
                });
            });

            // --- 4. CEK NIP ---
            const btnCek = document.getElementById('btn-cek-nip-kp-fungsional');
            if (btnCek) {
                btnCek.addEventListener('click', function() {
                    const nip = document.getElementById('nip_pegawai_kp_fungsional').value;
                    if (!nip) { Swal.fire('Error', 'Masukkan NIP!', 'warning'); return; }

                    const oldHtml = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    
                    fetch(`{{ url('/kenaikan-pangkat/ajax/cek-nip') }}/${nip}`)
                        .then(res => res.json())
                        .then(res => {
                            if(res.success) {
                                const d = res.data;
                                const set = (id, val) => { const el = document.getElementById(id); if(el) el.value = val || ''; }
                                set('nama_pegawai_kp_fungsional', d.nama);
                                set('nip_display_kp_fungsional', d.nip);
                                set('jabatan_kp_fungsional', d.jabatan);
                                set('pangkat_kp_fungsional', d.pangkat);
                                set('unit_kerja_kp_fungsional', d.unit_kerja);
                                set('golongan_ruang_kp_fungsional', d.golongan_ruang);
                                Swal.fire('Berhasil', 'Data ditemukan', 'success');
                            } else {
                                Swal.fire('Gagal', 'NIP tidak ditemukan', 'error');
                            }
                        })
                        .catch(() => Swal.fire('Error', 'Gagal koneksi', 'error'))
                        .finally(() => this.innerHTML = oldHtml);
                });
            }

            // --- 5. REVIEW DATA (STEP 3) ---
            function updateReviewData() {
                const get = (id) => document.getElementById(id).value || '-';
                const setText = (id, txt) => { const el = document.getElementById(id); if(el) el.textContent = txt; }

                setText('review-nama-kp-fungsional', get('nama_pegawai_kp_fungsional'));
                setText('review-nip-kp-fungsional', get('nip_display_kp_fungsional'));
                setText('review-jabatan-kp-fungsional', get('jabatan_kp_fungsional'));
                setText('review-unit-kerja-kp-fungsional', get('unit_kerja_kp_fungsional'));
                setText('review-pangkat-kp-fungsional', get('pangkat_kp_fungsional'));
                setText('review-golongan-ruang-kp-fungsional', get('golongan_ruang_kp_fungsional'));
                
                const periode = document.getElementById('periode_kenaikan_pangkat_kp_fungsional');
                setText('review-periode-kp-fungsional', periode.options[periode.selectedIndex]?.text || '-');

                const docContainer = document.getElementById('review-documents-kp-fungsional');
                docContainer.innerHTML = '';
                let hasFile = false;
                document.querySelectorAll('#step-2-kp-fungsional input[type="file"]').forEach(input => {
                    if(input.files.length > 0) {
                        hasFile = true;
                        const label = input.closest('.file-upload-card').querySelector('label').textContent.replace('*','').replace('(Opsional)','').trim();
                        docContainer.innerHTML += `<div class="text-success"><i class="fas fa-check"></i> ${label}</div>`;
                    }
                });
                if(!hasFile) docContainer.innerHTML = '<span class="text-muted">Belum ada file</span>';
            }

            // --- 6. SUBMIT FORM ---
            document.getElementById('form-kp-fungsional').addEventListener('submit', function(e) {
                if(!document.getElementById('confirm-data-kp-fungsional').checked) {
                    e.preventDefault();
                    Swal.fire('Peringatan', 'Anda harus menyetujui kebenaran data!', 'warning');
                } else {
                    Swal.fire({ title: 'Mengirim...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                }
            });

            showStep(1);
        });
    </script>
@endsection
