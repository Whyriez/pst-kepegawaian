@extends('layouts.user.app')
@section('title', 'Edit KP Fungsional')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Edit Pengajuan KP Fungsional</h2>
                    <p class="text-muted mb-0">Perbaiki data atau dokumen pengajuan Anda</p>
                </div>
                <a href="{{ route('kp.fungsional') }}" class="btn btn-secondary">
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
                <form id="form-kp-fungsional" action="{{ route('kp.fungsional.update', $pengajuan->id) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT') {{-- PENTING: Method PUT untuk Update --}}

                    {{-- STEP 1: DATA DIRI --}}
                    <div class="form-step active" id="step-1-kp-fungsional">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-user-edit me-2"></i>Periksa Data Diri
                            </h5>
                            <p class="text-muted">Pastikan data diri sesuai dengan kondisi terkini.</p>
                        </div>

                        {{-- Hidden NIP Display untuk Controller --}}
                        <input type="hidden" id="nip_display_kp_fungsional" name="nip_display_kp_fungsional"
                               value="{{ $pengajuan->pegawai->nip }}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pegawai</label>
                                <input type="text" class="form-control bg-light"
                                       id="nama_pegawai_kp_fungsional" name="nama_pegawai_kp_fungsional"
                                       value="{{ $pengajuan->pegawai->nama_lengkap }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan Pegawai <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="jabatan_kp_fungsional" name="jabatan_kp_fungsional"
                                       value="{{ $pengajuan->data_tambahan['jabatan_saat_ini'] ?? $pengajuan->pegawai->jabatan }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pangkat Pegawai <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="pangkat_kp_fungsional" name="pangkat_kp_fungsional"
                                       value="{{ $pengajuan->data_tambahan['pangkat_saat_ini'] ?? $pengajuan->pegawai->pangkat }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP Pegawai</label>
                                <input type="text" class="form-control bg-light"
                                       value="{{ $pengajuan->pegawai->nip }}" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit Kerja Pegawai <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="unit_kerja_kp_fungsional" name="unit_kerja_kp_fungsional"
                                       value="{{ $pengajuan->data_tambahan['unit_kerja'] ?? $pengajuan->pegawai->satuanKerja->nama_satuan_kerja ?? '' }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Golongan Ruang Diusulkan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="golongan_ruang_kp_fungsional" name="golongan_ruang_kp_fungsional"
                                       value="{{ $pengajuan->data_tambahan['golongan_ruang'] ?? '' }}" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5">
                            <button type="button" class="btn btn-primary btn-next-kp-fungsional" data-next="2">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: DOKUMEN --}}
                    <div class="form-step" id="step-2-kp-fungsional">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-file-upload me-2"></i>Perbaikan Dokumen
                            </h5>
                            <p class="text-muted">Upload file baru JIKA ingin mengganti dokumen lama.</p>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Jika tidak ingin mengganti dokumen, biarkan input file kosong. Dokumen lama akan tetap digunakan.
                        </div>

                        <div class="row">
                            @forelse($syarat as $dokumen)
                                @php
                                    // Cari apakah dokumen ini sudah pernah diupload sebelumnya
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

                                        {{-- Tampilkan Info File Lama --}}
                                        @if($uploaded)
                                            <div class="mb-2 p-2 bg-light border rounded small existing-file-info"
                                                 data-label="{{ $dokumen->nama_dokumen }}" data-filename="{{ $uploaded->nama_file_asli }}">
                                                <i class="fas fa-check-circle text-success me-1"></i>
                                                File saat ini: <strong>{{ Str::limit($uploaded->nama_file_asli, 25) }}</strong>
                                                <a href="{{ Storage::url($uploaded->path_file) }}" target="_blank" class="ms-1 text-primary"><i class="fas fa-download"></i></a>
                                            </div>
                                        @else
                                            <div class="mb-2 small text-danger">
                                                <i class="fas fa-times-circle me-1"></i> Belum ada file diupload
                                            </div>
                                        @endif

                                        <div class="file-input-wrapper">
                                            {{-- Input file: Jika sudah ada file ($uploaded), maka TIDAK required --}}
                                            <input type="file" class="form-control file-input-dynamic"
                                                   id="file_{{ $dokumen->id }}" name="file_{{ $dokumen->id }}"
                                                   accept=".pdf,.jpg,.jpeg,.png"
                                                {{ ($dokumen->is_required && !$uploaded) ? 'required' : '' }}>

                                            <div class="file-preview mt-2 small text-success"
                                                 id="preview-file_{{ $dokumen->id }}"></div>
                                        </div>
                                        <div class="form-text text-muted">
                                            {{ $uploaded ? 'Upload baru untuk mengganti file lama.' : 'Tipe: PDF/Gambar, Max: 2MB' }}
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
                                <select class="form-control" id="periode_kenaikan_pangkat_kp_fungsional"
                                        name="periode_kenaikan_pangkat_kp_fungsional" required>
                                    @php $periode = $pengajuan->data_tambahan['periode'] ?? ''; @endphp
                                    <option value="April" {{ $periode == 'April' ? 'selected' : '' }}>April</option>
                                    <option value="Oktober" {{ $periode == 'Oktober' ? 'selected' : '' }}>Oktober</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-kp-fungsional" data-prev="1">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </button>
                            <button type="button" class="btn btn-primary btn-next-kp-fungsional" data-next="3">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 3: KONFIRMASI --}}
                    <div class="form-step" id="step-3-kp-fungsional">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-check-circle me-2"></i>Konfirmasi Perubahan
                            </h5>
                            <p class="text-muted">Cek kembali sebelum menyimpan perubahan.</p>
                        </div>

                        {{-- Ringkasan Data --}}
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Ringkasan Data Pegawai</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Nama:</strong> <span id="review-nama-kp-fungsional">-</span></p>
                                        <p><strong>Jabatan:</strong> <span id="review-jabatan-kp-fungsional">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Gol. Diusulkan:</strong> <span id="review-golongan-ruang-kp-fungsional">-</span></p>
                                        <p><strong>Periode:</strong> <span id="review-periode-kp-fungsional">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Status Dokumen</h6>
                                <div id="review-documents-kp-fungsional" class="small"></div>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirm-data-kp-fungsional" required>
                            <label class="form-check-label" for="confirm-data-kp-fungsional">
                                Saya menyatakan perbaikan data ini benar.
                            </label>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-kp-fungsional" data-prev="2">
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
                document.getElementById(`step-${idx}-kp-fungsional`).classList.add('active');
                for (let i = 0; i < idx; i++) progressSteps[i].classList.add('active');
                if (idx == 3) updateReview();
            }

            // Validasi Sederhana
            document.querySelectorAll('.btn-next-kp-fungsional').forEach(btn => {
                btn.addEventListener('click', function () {
                    const next = parseInt(this.dataset.next);

                    // Validasi Step 2: Cek required file inputs
                    if (next === 3) {
                        let valid = true;
                        // Hanya cek input yang visible dan required
                        const reqInputs = document.querySelectorAll('#step-2-kp-fungsional input[type="file"][required]');
                        reqInputs.forEach(input => {
                            if (input.files.length === 0) {
                                input.classList.add('is-invalid');
                                valid = false;
                            } else {
                                input.classList.remove('is-invalid');
                            }
                        });

                        if(!valid) {
                            Swal.fire('Perhatian', 'Lengkapi dokumen yang wajib diunggah (yang belum memiliki file lama).', 'warning');
                            return;
                        }
                    }
                    showStep(next);
                });
            });

            document.querySelectorAll('.btn-prev-kp-fungsional').forEach(btn => {
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

            // Update Review Page (Logika Edit)
            function updateReview() {
                const get = (id) => document.getElementById(id)?.value || '-';
                document.getElementById('review-nama-kp-fungsional').textContent = get('nama_pegawai_kp_fungsional');
                document.getElementById('review-jabatan-kp-fungsional').textContent = get('jabatan_kp_fungsional');
                document.getElementById('review-golongan-ruang-kp-fungsional').textContent = get('golongan_ruang_kp_fungsional');
                document.getElementById('review-periode-kp-fungsional').textContent = document.getElementById('periode_kenaikan_pangkat_kp_fungsional').value;

                // Cek Dokumen (Gabungan Existing + New)
                const container = document.getElementById('review-documents-kp-fungsional');
                container.innerHTML = '';

                // Loop setiap card dokumen
                document.querySelectorAll('.file-upload-card').forEach(card => {
                    const label = card.querySelector('label').innerText.replace('*','').trim();
                    const fileInput = card.querySelector('input[type="file"]');
                    const existingInfo = card.querySelector('.existing-file-info');

                    let statusHtml = '';

                    if (fileInput.files.length > 0) {
                        // Ada upload baru
                        statusHtml = `<span class="text-warning fw-bold"><i class="fas fa-sync me-1"></i>Akan Diganti dengan: ${fileInput.files[0].name}</span>`;
                    } else if (existingInfo) {
                        // Tidak ada upload baru, tapi ada file lama
                        const oldName = existingInfo.getAttribute('data-filename');
                        statusHtml = `<span class="text-success"><i class="fas fa-check-circle me-1"></i>Tetap menggunakan: ${oldName}</span>`;
                    } else {
                        // Tidak ada file sama sekali
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
