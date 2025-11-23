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

            // --- SWEETALERT UNTUK SESSION LARAVEL ---
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    confirmButtonText: 'OK'
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonText: 'Tutup'
                });
            @endif
            @if ($errors->any())
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    html: '<ul style="text-align: left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>'
                });
            @endif

            // --- 1. LOGIKA AUTOFILL NIP ---
            const btnCek = document.getElementById('btn-cek-nip-kp-fungsional');
            if (btnCek) {
                btnCek.addEventListener('click', function() {
                    const nipInput = document.getElementById('nip_pegawai_kp_fungsional');
                    const nip = nipInput ? nipInput.value.trim() : '';
                    const btn = this;

                    if (!nip) {
                        Swal.fire('Error', 'Harap masukkan NIP terlebih dahulu!', 'warning');
                        return;
                    }

                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                    btn.disabled = true;

                    fetch(`{{ url('/kenaikan-pangkat/ajax/cek-nip') }}/${nip}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Data pegawai tidak ditemukan.');
                            return response.json();
                        })
                        .then(result => {
                            if (result.success) {
                                const data = result.data;
                                // Helper function to set value safely
                                const setVal = (id, val) => {
                                    const el = document.getElementById(id);
                                    if (el) el.value = val;
                                };

                                setVal('nama_pegawai_kp_fungsional', data.nama || '');
                                setVal('nip_display_kp_fungsional', data.nip || '');
                                setVal('jabatan_kp_fungsional', data.jabatan || '');
                                setVal('pangkat_kp_fungsional', data.pangkat || '');
                                setVal('unit_kerja_kp_fungsional', data.unit_kerja || '');
                                setVal('golongan_ruang_kp_fungsional', data.golongan_ruang || '');

                                btn.innerHTML = '<i class="fas fa-check"></i> Ditemukan';
                                btn.classList.replace('btn-outline-primary', 'btn-success');
                                btn.classList.remove('btn-danger');

                                setTimeout(() => {
                                    btn.innerHTML = originalText;
                                    btn.classList.replace('btn-success', 'btn-outline-primary');
                                    btn.disabled = false;
                                }, 2000);
                            } else {
                                throw new Error(result.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Gagal', error.message, 'error');

                            btn.innerHTML = '<i class="fas fa-times"></i> Gagal';
                            btn.classList.replace('btn-outline-primary', 'btn-danger');

                            setTimeout(() => {
                                btn.innerHTML = originalText;
                                btn.classList.replace('btn-danger', 'btn-outline-primary');
                                btn.disabled = false;
                            }, 2000);
                        });
                });
            }

            // --- 2. LOGIKA STEPPER ---
            const steps = document.querySelectorAll('.form-step');
            const progressSteps = document.querySelectorAll('.progress-steps .step');

            function showStep(stepIndex) {
                steps.forEach(el => el.classList.remove('active'));
                progressSteps.forEach(el => el.classList.remove('active'));

                document.getElementById(`step-${stepIndex}-kp-fungsional`).classList.add('active');

                for (let i = 0; i < stepIndex; i++) {
                    if (progressSteps[i]) progressSteps[i].classList.add('active');
                }
                if (stepIndex == 3) updateReviewData();
            }

            function updateReviewData() {
                const getValue = (id) => {
                    const el = document.getElementById(id);
                    return el ? (el.value || '-') : '-';
                };
                const setText = (id, text) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = text;
                };

                setText('review-nama-kp-fungsional', getValue('nama_pegawai_kp_fungsional'));
                setText('review-nip-kp-fungsional', getValue('nip_display_kp_fungsional'));
                setText('review-jabatan-kp-fungsional', getValue('jabatan_kp_fungsional'));
                setText('review-unit-kerja-kp-fungsional', getValue('unit_kerja_kp_fungsional'));
                setText('review-pangkat-kp-fungsional', getValue('pangkat_kp_fungsional'));
                setText('review-golongan-ruang-kp-fungsional', getValue('golongan_ruang_kp_fungsional'));

                const periodeSelect = document.getElementById('periode_kenaikan_pangkat_kp_fungsional');
                const periodeText = periodeSelect.options[periodeSelect.selectedIndex]?.text || '-';
                setText('review-periode-kp-fungsional', periodeText);

                const docContainer = document.getElementById('review-documents-kp-fungsional');
                docContainer.innerHTML = '';
                let hasFile = false;

                document.querySelectorAll('input[type="file"]').forEach(input => {
                    if (input.files.length > 0) {
                        hasFile = true;
                        const fileName = input.files[0].name;
                        const labelText = input.closest('.file-upload-card').querySelector('label')
                            .childNodes[0].textContent.trim();
                        const item = document.createElement('div');
                        item.className = 'd-flex align-items-center mb-2 text-success';
                        item.innerHTML =
                            `<i class="fas fa-check-circle me-2"></i> <strong>${labelText}:</strong> <span class="ms-1 text-dark">${fileName}</span>`;
                        docContainer.appendChild(item);
                    }
                });

                if (!hasFile) {
                    docContainer.innerHTML =
                        '<p class="text-muted fst-italic">Belum ada dokumen yang diunggah.</p>';
                }
            }

            document.querySelectorAll('.btn-next-kp-fungsional').forEach(btn => {
                btn.addEventListener('click', function() {
                    const next = this.getAttribute('data-next');
                    if (next == 2) {
                        const namaEl = document.getElementById('nama_pegawai_kp_fungsional');
                        if (namaEl && !namaEl.value) {
                            Swal.fire('Data Kosong',
                                'Silakan klik tombol "Cek NIP" terlebih dahulu untuk mengisi data pegawai!',
                                'warning');
                            return;
                        }
                    }
                    showStep(next);
                });
            });

            document.querySelectorAll('.btn-prev-kp-fungsional').forEach(btn => {
                btn.addEventListener('click', function() {
                    showStep(this.getAttribute('data-prev'));
                });
            });

            // --- 3. LOGIC SUBMIT FORM ---
            const form = document.getElementById('form-kp-fungsional');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Fix ID selection
                    const checkbox = document.getElementById('confirm-data-kp-fungsional');
                    if (checkbox && !checkbox.checked) {
                        e.preventDefault();
                        Swal.fire('Peringatan', 'Anda harus menyetujui konfirmasi kebenaran data!',
                            'warning');
                    } else {
                        // Show Loading on Submit
                        Swal.fire({
                            title: 'Mengirim Data...',
                            text: 'Mohon tunggu',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                    }
                });
            }

            showStep(1);
        });
    </script>
@endsection
