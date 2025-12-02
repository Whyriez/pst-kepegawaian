@extends('layouts.user.app')
@section('title', 'Edit KP Reguler')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Edit KP Reguler</h2>
                    <p class="text-muted mb-0">Perbaiki data atau dokumen pengajuan Anda</p>
                </div>
                <a href="{{ route('kp.reguler') }}" class="btn btn-secondary">
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
                <form id="form-kp-reguler"
                      action="{{ route('kp.reguler.update', $pengajuan->id) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT') {{-- Method PUT untuk Update --}}

                    {{-- STEP 1: DATA DIRI --}}
                    <div class="form-step active" id="step-1-kp-reguler">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-user-edit me-2"></i>Periksa Data Diri
                            </h5>
                            <p class="text-muted">Pastikan data diri sesuai dengan kondisi terkini.</p>
                        </div>

                        {{-- Hidden Inputs --}}
                        <input type="hidden" name="nip_display_kp_reguler" value="{{ $pengajuan->pegawai->nip }}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pegawai</label>
                                <input type="text" class="form-control bg-light"
                                       id="nama_pegawai_kp_reguler"
                                       value="{{ $pengajuan->pegawai->nama_lengkap }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan Pegawai <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="jabatan_kp_reguler" name="jabatan_kp_reguler"
                                       value="{{ $pengajuan->data_tambahan['jabatan'] ?? $pengajuan->pegawai->jabatan }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pangkat Pegawai <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="pangkat_kp_reguler" name="pangkat_kp_reguler"
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
                                       id="unit_kerja_kp_reguler" name="unit_kerja_kp_reguler"
                                       value="{{ $pengajuan->data_tambahan['unit_kerja'] ?? '' }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Golongan Ruang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="golongan_ruang_kp_reguler" name="golongan_ruang_kp_reguler"
                                       value="{{ $pengajuan->data_tambahan['golongan_ruang'] ?? '' }}" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5">
                            <button type="button" class="btn btn-primary btn-next-kp-reguler" data-next="2">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: DOKUMEN --}}
                    <div class="form-step" id="step-2-kp-reguler">
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
                                            <input type="file" class="form-control file-input-dynamic"
                                                   id="file_{{ $dokumen->id }}" name="file_{{ $dokumen->id }}"
                                                   accept=".pdf,.jpg,.jpeg,.png"
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
                                <select class="form-control" id="periode_kenaikan_pangkat_kp_reguler"
                                        name="periode_kenaikan_pangkat_kp_reguler" required>
                                    @php $periode = $pengajuan->data_tambahan['periode'] ?? ''; @endphp
                                    <option value="April" {{ $periode == 'April' ? 'selected' : '' }}>April</option>
                                    <option value="Oktober" {{ $periode == 'Oktober' ? 'selected' : '' }}>Oktober</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-kp-reguler" data-prev="1">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </button>
                            <button type="button" class="btn btn-primary btn-next-kp-reguler" data-next="3">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 3: KONFIRMASI --}}
                    <div class="form-step" id="step-3-kp-reguler">
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
                                        <p><strong>Nama:</strong> <span id="review-nama-kp-reguler">-</span></p>
                                        <p><strong>Jabatan:</strong> <span id="review-jabatan-kp-reguler">-</span></p>
                                        <p><strong>Pangkat:</strong> <span id="review-pangkat-kp-reguler">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Unit Kerja:</strong> <span id="review-unit-kerja-kp-reguler">-</span></p>
                                        <p><strong>Golongan/Ruang:</strong> <span id="review-golongan-ruang-kp-reguler">-</span></p>
                                        <p><strong>Periode:</strong> <span id="review-periode-kp-reguler">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Status Dokumen</h6>
                                <div id="review-documents-kp-reguler" class="small"></div>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirm-data-kp-reguler" required>
                            <label class="form-check-label" for="confirm-data-kp-reguler">
                                Saya menyatakan perbaikan data ini benar.
                            </label>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-kp-reguler" data-prev="2">
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

            // Navigasi
            const steps = document.querySelectorAll('.form-step');
            const progressSteps = document.querySelectorAll('.progress-steps .step');

            function showStep(idx) {
                steps.forEach(s => s.classList.remove('active'));
                progressSteps.forEach(s => s.classList.remove('active'));
                document.getElementById(`step-${idx}-kp-reguler`).classList.add('active');
                for (let i = 0; i < idx; i++) progressSteps[i].classList.add('active');
                if (idx == 3) updateReview();
            }

            // Button Listeners
            document.querySelectorAll('.btn-next-kp-reguler').forEach(btn => {
                btn.addEventListener('click', function () {
                    const next = parseInt(this.dataset.next);

                    if (next === 3) {
                        let valid = true;
                        // Hanya validasi file input jika required dan belum ada file
                        const reqInputs = document.querySelectorAll('#step-2-kp-reguler input[type="file"][required]');
                        reqInputs.forEach(input => {
                            if (input.files.length === 0) {
                                input.classList.add('is-invalid');
                                valid = false;
                            } else {
                                input.classList.remove('is-invalid');
                            }
                        });

                        if(!valid) {
                            Swal.fire('Perhatian', 'Lengkapi dokumen yang wajib diunggah.', 'warning');
                            return;
                        }
                    }
                    showStep(next);
                });
            });

            document.querySelectorAll('.btn-prev-kp-reguler').forEach(btn => {
                btn.addEventListener('click', function () { showStep(this.dataset.prev); });
            });

            // Preview File Baru
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', function () {
                    const preview = document.getElementById(`preview-${this.id}`);
                    if (this.files.length > 0) {
                        if (this.files[0].size > 2 * 1024 * 1024) {
                            Swal.fire('Error', 'File max 2MB', 'warning');
                            this.value = '';
                        } else {
                            preview.innerHTML = `<div class="text-success small"><i class="fas fa-check-circle me-1"></i> File Baru: ${this.files[0].name}</div>`;
                            preview.style.display = 'block';
                            this.classList.remove('is-invalid');
                        }
                    } else {
                        preview.innerHTML = '';
                    }
                });
            });

            // Update Review Page
            function updateReview() {
                const get = (id) => document.getElementById(id)?.value || '-';

                document.getElementById('review-nama-kp-reguler').textContent = get('nama_pegawai_kp_reguler') || '{{ $pengajuan->pegawai->nama_lengkap }}';
                document.getElementById('review-jabatan-kp-reguler').textContent = get('jabatan_kp_reguler');
                document.getElementById('review-pangkat-kp-reguler').textContent = get('pangkat_kp_reguler');
                document.getElementById('review-unit-kerja-kp-reguler').textContent = get('unit_kerja_kp_reguler');
                document.getElementById('review-golongan-ruang-kp-reguler').textContent = get('golongan_ruang_kp_reguler');
                document.getElementById('review-periode-kp-reguler').textContent = document.getElementById('periode_kenaikan_pangkat_kp_reguler').value;

                // Cek Dokumen Status
                const container = document.getElementById('review-documents-kp-reguler');
                container.innerHTML = '';

                document.querySelectorAll('.file-upload-card').forEach(card => {
                    const label = card.querySelector('label').innerText.replace('*','').trim();
                    const fileInput = card.querySelector('input[type="file"]');
                    const existingInfo = card.querySelector('.existing-file-info');

                    let statusHtml = '';

                    if (fileInput.files.length > 0) {
                        statusHtml = `<span class="text-warning fw-bold"><i class="fas fa-sync me-1"></i>Ganti: ${fileInput.files[0].name}</span>`;
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
