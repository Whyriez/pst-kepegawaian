@extends('layouts.user.app')
@section('title', 'Pensiun Uzur')

{{-- Tambahkan SweetAlert --}}
@push('styles')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    {{-- HAPUS CLASS content-template AGAR LANGSUNG MUNCUL --}}
    <div class="container-fluid p-0">

        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Form Pengajuan Pensiun Uzur</h2>
                    <p class="text-muted mb-0">Formulir untuk pengajuan pensiun karena uzur/penyakit kronis</p>
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
                {{-- ACTION FORM KE ROUTE STORE --}}
                <form id="form-pensiun-uzur" action="{{ route('pensiun.uzur.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- STEP 1: DATA DIRI --}}
                    <div class="form-step active" id="step-1-uzur">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-user me-2"></i>Data Diri Pegawai
                            </h5>
                            <p class="text-muted">Isi data diri pegawai yang mengajukan pensiun uzur</p>
                        </div>

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Cek Data dengan NIP</h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="nip_pegawai_uzur"
                                                name="nip_pegawai_uzur" placeholder="Masukkan NIP Pegawai"
                                                value="{{ Auth::user()->pegawai->nip ?? '' }}">
                                            <button class="btn btn-outline-primary" type="button" id="btn-cek-nip-uzur">
                                                <i class="fas fa-search me-2"></i>Cek NIP
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_pegawai_uzur" class="form-label">Nama Pegawai <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="nama_pegawai_uzur" name="nama_pegawai_uzur" required>
                                </div>
                                <div class="invalid-feedback">Harap isi nama pegawai</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nip_display_uzur" class="form-label">NIP Pegawai <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" class="form-control" id="nip_display_uzur" name="nip_display_uzur" required readonly>
                                </div>
                                <div class="invalid-feedback">Harap isi NIP pegawai</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jabatan_uzur" class="form-label">Jabatan Pegawai <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    <input type="text" class="form-control" id="jabatan_uzur" name="jabatan_uzur" required>
                                </div>
                                <div class="invalid-feedback">Harap isi jabatan pegawai</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="satuan_kerja_uzur" class="form-label">Satuan Kerja <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <input type="text" class="form-control" id="satuan_kerja_uzur" name="satuan_kerja_uzur" required>
                                </div>
                                <div class="invalid-feedback">Harap isi satuan kerja</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pangkat_uzur" class="form-label">Pangkat Pegawai <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-star"></i></span>
                                    <input type="text" class="form-control" id="pangkat_uzur" name="pangkat_uzur" required>
                                </div>
                                <div class="invalid-feedback">Harap isi pangkat pegawai</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="golongan_uzur" class="form-label">Golongan/Ruang <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                    <input type="text" class="form-control" id="golongan_uzur" name="golongan_uzur" required>
                                </div>
                                <div class="invalid-feedback">Harap isi golongan/ruang</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <div></div> <button type="button" class="btn btn-primary btn-next-uzur" data-next="2">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: DOKUMEN (DINAMIS DENGAN DESAIN ASLI) --}}
                    <div class="form-step" id="step-2-uzur">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-file-upload me-2"></i>Upload Dokumen Persyaratan
                            </h5>
                            <p class="text-muted">Unggah dokumen-dokumen yang diperlukan (Otomatis dari Database)</p>
                        </div>

                        <div class="alert alert-info">
                            <div class="d-flex">
                                <i class="fas fa-info-circle me-3 mt-1"></i>
                                <div>
                                    <strong>Informasi:</strong> Format file yang diizinkan: PDF, JPG, JPEG, PNG. Maksimal ukuran file: 2MB per dokumen.
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            <span id="upload-progress-uzur">0/{{ count($syarat) }}</span> dokumen terunggah
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
                                            @if($dokumen->is_required)
                                                <span class="text-danger">*</span>
                                            @else
                                                <span class="text-muted fw-light">(Opsional)</span>
                                            @endif
                                        </label>
                                        
                                        <div class="file-input-wrapper">
                                            <input type="file" class="form-control file-input-dynamic" 
                                                id="file_{{ $dokumen->id }}" 
                                                name="file_{{ $dokumen->id }}" 
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                {{ $dokumen->is_required ? 'required' : '' }}>
                                            
                                            <div class="file-preview mt-2 small text-success" id="preview-file_{{ $dokumen->id }}"></div>
                                        </div>
                                        <div class="form-text">Type File: PDF/Gambar, Max: 2MB</div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        Belum ada syarat dokumen yang diatur di database untuk layanan ini (pensiun-uzur).
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-uzur" data-prev="1">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </button>
                            <button type="button" class="btn btn-primary btn-next-uzur" data-next="3">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 3: KONFIRMASI --}}
                    <div class="form-step" id="step-3-uzur">
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
                                        <p><strong>Nama:</strong> <span id="review-nama-uzur">-</span></p>
                                        <p><strong>NIP:</strong> <span id="review-nip-uzur">-</span></p>
                                        <p><strong>Jabatan:</strong> <span id="review-jabatan-uzur">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Satuan Kerja:</strong> <span id="review-satuan-kerja-uzur">-</span></p>
                                        <p><strong>Pangkat:</strong> <span id="review-pangkat-uzur">-</span></p>
                                        <p><strong>Golongan:</strong> <span id="review-golongan-uzur">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Dokumen yang Diunggah</h6>
                                <div id="review-documents-uzur" class="small"></div>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirm-data-uzur" required>
                            <label class="form-check-label" for="confirm-data-uzur">
                                Saya menyatakan bahwa data yang saya berikan adalah benar dan siap menanggung konsekuensi hukum jika data tersebut tidak valid.
                            </label>
                            <div class="invalid-feedback">Anda harus menyetujui pernyataan ini sebelum mengajukan</div>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-uzur" data-prev="2">
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

    <style>
        /* DESIGN SAMA PERSIS DENGAN SEBELUMNYA */
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
            
            // --- NOTIFIKASI SESSION ---
            @if(session('success')) Swal.fire('Berhasil', "{{ session('success') }}", 'success'); @endif
            @if(session('error')) Swal.fire('Gagal', "{{ session('error') }}", 'error'); @endif
            @if($errors->any()) Swal.fire('Validasi Gagal', 'Cek inputan Anda', 'warning'); @endif

            // --- 1. LOGIKA STEPPER ---
            const steps = document.querySelectorAll('.form-step');
            const progressSteps = document.querySelectorAll('.progress-steps .step');

            function showStep(idx) {
                steps.forEach(el => el.classList.remove('active'));
                progressSteps.forEach(el => el.classList.remove('active'));
                document.getElementById(`step-${idx}-uzur`).classList.add('active');
                for(let i=0; i<idx; i++) progressSteps[i].classList.add('active');
                if(idx == 3) updateReview();
            }

            document.querySelectorAll('.btn-next-uzur').forEach(btn => {
                btn.addEventListener('click', function() {
                    const next = this.dataset.next;
                    if(next == 2 && !document.getElementById('nama_pegawai_uzur').value) {
                        Swal.fire('Data Kosong', 'Silakan Cek NIP dulu!', 'warning'); return;
                    }
                    showStep(next);
                });
            });

            document.querySelectorAll('.btn-prev-uzur').forEach(btn => {
                btn.addEventListener('click', function() { showStep(this.dataset.prev); });
            });

            // --- 2. LOGIKA CEK NIP ---
            const btnCek = document.getElementById('btn-cek-nip-uzur');
            if(btnCek) {
                btnCek.addEventListener('click', function() {
                    const nip = document.getElementById('nip_pegawai_uzur').value;
                    if(!nip) { Swal.fire('Isi NIP!', '', 'warning'); return; }

                    const oldHtml = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    
                    fetch(`{{ url('/kenaikan-pangkat/ajax/cek-nip') }}/${nip}`)
                        .then(res => res.json())
                        .then(res => {
                            if(res.success) {
                                const d = res.data;
                                // Helper set value
                                const set = (id, val) => { const el = document.getElementById(id); if(el) el.value = val || ''; }
                                
                                set('nama_pegawai_uzur', d.nama);
                                set('jabatan_uzur', d.jabatan);
                                set('pangkat_uzur', d.pangkat);
                                set('nip_display_uzur', d.nip);
                                set('satuan_kerja_uzur', d.unit_kerja);
                                set('golongan_uzur', d.golongan_ruang);
                                Swal.fire('Ditemukan', 'Data pegawai dimuat', 'success');
                            } else {
                                Swal.fire('Gagal', 'NIP tidak ditemukan', 'error');
                            }
                        })
                        .catch(() => Swal.fire('Error', 'Gagal koneksi server', 'error'))
                        .finally(() => this.innerHTML = oldHtml);
                });
            }

            // --- 3. LOGIKA UPLOAD FILE ---
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', function() {
                    const previewId = `preview-${this.id}`; // preview-file_1
                    const previewEl = document.getElementById(previewId);
                    
                    if (this.files.length > 0) {
                        const fileName = this.files[0].name;
                        if (previewEl) {
                            previewEl.innerHTML = `<i class="fas fa-check-circle me-1"></i> ${fileName}`;
                            previewEl.classList.add('has-file');
                        }
                    }
                });
            });

            // --- 4. LOGIKA REVIEW ---
            function updateReview() {
                const get = (id) => document.getElementById(id).value || '-';
                const setText = (id, val) => document.getElementById(id).textContent = val;

                setText('review-nama-uzur', get('nama_pegawai_uzur'));
                setText('review-nip-uzur', get('nip_display_uzur'));
                setText('review-jabatan-uzur', get('jabatan_uzur'));
                setText('review-satuan-kerja-uzur', get('satuan_kerja_uzur'));
                setText('review-pangkat-uzur', get('pangkat_uzur'));
                setText('review-golongan-uzur', get('golongan_uzur'));
                
                const docContainer = document.getElementById('review-documents-uzur');
                docContainer.innerHTML = '';
                let hasFile = false;

                document.querySelectorAll('input[type="file"]').forEach(input => {
                    if(input.files.length > 0) {
                        hasFile = true;
                        const fileName = input.files[0].name;
                        // Ambil label
                        const label = input.closest('.file-upload-card').querySelector('label').innerText.replace('*','').replace('(Opsional)','').trim();
                        
                        const item = document.createElement('div');
                        item.className = 'd-flex align-items-center mb-2 text-success';
                        item.innerHTML = `<i class="fas fa-check-circle me-2"></i> <strong>${label}:</strong> <span class="ms-1 text-dark">${fileName}</span>`;
                        docContainer.appendChild(item);
                    }
                });

                if (!hasFile) {
                    docContainer.innerHTML = '<p class="text-muted fst-italic">Belum ada dokumen yang diunggah.</p>';
                }
            }

            // --- 5. SUBMIT FORM ---
            document.getElementById('form-pensiun-uzur').addEventListener('submit', function(e) {
                if(!document.getElementById('confirm-data-uzur').checked) {
                    e.preventDefault();
                    Swal.fire('Konfirmasi', 'Anda harus menyetujui data', 'warning');
                } else {
                    Swal.fire({
                        title: 'Mengirim...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                }
            });

            showStep(1);
        });
    </script>
@endsection