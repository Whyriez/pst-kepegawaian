@extends('layouts.user.app')
@section('title', 'Edit Konversi AK Pendidikan')

{{-- Tambahkan SweetAlert --}}
@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Edit Konversi AK Pendidikan</h2>
                    <p class="text-muted mb-0">Perbaiki data atau dokumen pengajuan konversi angka kredit.</p>
                </div>
                <a href="{{ route('konversi_ak_pendidikan') }}" class="btn btn-secondary">
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
                <form id="form-konversi-ak-pendidikan"
                      action="{{ route('konversi_ak_pendidikan.update', $pengajuan->id) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- STEP 1: DATA DIRI --}}
                    <div class="form-step active" id="step-1-konversi-ak-pendidikan">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-user-edit me-2"></i>Periksa Data Diri
                            </h5>
                            <p class="text-muted">Pastikan data pegawai sesuai dengan kondisi terkini.</p>
                        </div>

                        {{-- Hidden NIP Display --}}
                        <input type="hidden" name="nip_display_konversi_ak_pendidikan" value="{{ $pengajuan->pegawai->nip }}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pegawai</label>
                                <input type="text" class="form-control bg-light"
                                       id="nama_pegawai_konversi_ak_pendidikan" name="nama_pegawai_konversi_ak_pendidikan"
                                       value="{{ $pengajuan->data_tambahan['nama'] ?? $pengajuan->pegawai->nama_lengkap }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP Pegawai</label>
                                <input type="text" class="form-control bg-light"
                                       id="nip_display_konversi_ak_pendidikan"
                                       value="{{ $pengajuan->pegawai->nip }}" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="jabatan_konversi_ak_pendidikan" name="jabatan_konversi_ak_pendidikan"
                                       value="{{ $pengajuan->data_tambahan['jabatan'] ?? '' }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pangkat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="pangkat_konversi_ak_pendidikan" name="pangkat_konversi_ak_pendidikan"
                                       value="{{ $pengajuan->data_tambahan['pangkat'] ?? '' }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Satuan Kerja <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="satuan_kerja_konversi_ak_pendidikan" name="satuan_kerja_konversi_ak_pendidikan"
                                       value="{{ $pengajuan->data_tambahan['satuan_kerja'] ?? '' }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Golongan Ruang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                       id="golongan_ruang_konversi_ak_pendidikan" name="golongan_ruang_konversi_ak_pendidikan"
                                       value="{{ $pengajuan->data_tambahan['golongan_ruang'] ?? '' }}" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5">
                            <button type="button" class="btn btn-primary btn-next-konversi-ak-pendidikan" data-next="2">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: DOKUMEN --}}
                    <div class="form-step" id="step-2-konversi-ak-pendidikan">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                <i class="fas fa-file-upload me-2"></i>Perbaikan Dokumen
                            </h5>
                            <p class="text-muted">Upload file baru JIKA ingin mengganti dokumen lama.</p>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Biarkan input file kosong jika tidak ingin mengganti dokumen yang sudah ada.
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
                                                 data-label="{{ $dokumen->nama_dokumen }}"
                                                 data-filename="{{ $uploaded->nama_file_asli }}">
                                                <i class="fas fa-check-circle text-success me-1"></i>
                                                File saat ini: <strong>{{ Str::limit($uploaded->nama_file_asli, 25) }}</strong>
                                                <a href="{{ Storage::url($uploaded->path_file) }}" target="_blank" class="ms-1 text-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        @else
                                            <div class="mb-2 small text-danger">
                                                <i class="fas fa-times-circle me-1"></i> Belum ada file
                                            </div>
                                        @endif

                                        {{-- Input File --}}
                                        <div class="file-input-wrapper">
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

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-konversi-ak-pendidikan"
                                    data-prev="1">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </button>
                            <button type="button" class="btn btn-primary btn-next-konversi-ak-pendidikan" data-next="3">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 3: KONFIRMASI --}}
                    <div class="form-step" id="step-3-konversi-ak-pendidikan">
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
                                        <p><strong>Nama:</strong> <span id="review-nama-konversi-ak-pendidikan">-</span></p>
                                        <p><strong>NIP:</strong> <span id="review-nip-konversi-ak-pendidikan">-</span></p>
                                        <p><strong>Jabatan:</strong> <span id="review-jabatan-konversi-ak-pendidikan">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Pangkat:</strong> <span id="review-pangkat-konversi-ak-pendidikan">-</span></p>
                                        <p><strong>Satuan Kerja:</strong> <span id="review-satuan-kerja-konversi-ak-pendidikan">-</span></p>
                                        <p><strong>Golongan/Ruang:</strong> <span id="review-golongan-ruang-konversi-ak-pendidikan">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Status Dokumen</h6>
                                <div id="review-documents-konversi-ak-pendidikan" class="small"></div>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirm-data-konversi-ak-pendidikan" required>
                            <label class="form-check-label" for="confirm-data-konversi-ak-pendidikan">
                                Saya menyatakan bahwa data yang saya berikan adalah benar dan siap menanggung
                                konsekuensi
                                hukum jika data tersebut tidak valid.
                            </label>
                            <div class="invalid-feedback">Anda harus menyetujui pernyataan ini sebelum mengajukan</div>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-secondary btn-prev-konversi-ak-pendidikan"
                                    data-prev="2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </button>
                            <button type="submit" class="btn btn-success">
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
            // Notifikasi System
            @if (session('error')) Swal.fire('Gagal', "{{ session('error') }}", 'error'); @endif
            @if ($errors->any()) Swal.fire('Validasi Gagal', 'Cek inputan Anda', 'warning'); @endif

            const steps = document.querySelectorAll('.form-step');
            const progressSteps = document.querySelectorAll('.progress-steps .step');

            // Navigasi Step
            function showStep(idx) {
                steps.forEach(s => s.classList.remove('active'));
                progressSteps.forEach(s => s.classList.remove('active'));
                document.getElementById(`step-${idx}-konversi-ak-pendidikan`).classList.add('active');
                for (let i = 0; i < idx; i++) progressSteps[i].classList.add('active');
                if (idx == 3) updateReview();
            }

            // Tombol Next
            document.querySelectorAll('.btn-next-konversi-ak-pendidikan').forEach(btn => {
                btn.addEventListener('click', function () {
                    const next = parseInt(this.dataset.next);

                    // Validasi Step 2 (Dokumen)
                    if (next === 3) {
                        let valid = true;
                        // Hanya cek dokumen required yang belum punya file lama
                        const reqInputs = document.querySelectorAll('#step-2-konversi-ak-pendidikan input[type="file"][required]');
                        reqInputs.forEach(input => {
                            if (input.files.length === 0) {
                                input.classList.add('is-invalid');
                                valid = false;
                            } else {
                                input.classList.remove('is-invalid');
                            }
                        });

                        if(!valid) {
                            Swal.fire('Perhatian', 'Harap lengkapi dokumen wajib yang belum terunggah.', 'warning');
                            return;
                        }
                    }
                    showStep(next);
                });
            });

            // Tombol Prev
            document.querySelectorAll('.btn-prev-konversi-ak-pendidikan').forEach(btn => {
                btn.addEventListener('click', function () { showStep(this.dataset.prev); });
            });

            // Preview File
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

            // Update Review Page (Ringkasan)
            function updateReview() {
                const get = (id) => document.getElementById(id)?.value || '-';

                document.getElementById('review-nama-konversi-ak-pendidikan').textContent = document.getElementById('nama_pegawai_konversi_ak_pendidikan').value;
                document.getElementById('review-nip-konversi-ak-pendidikan').textContent = get('nip_display_konversi_ak_pendidikan');
                document.getElementById('review-jabatan-konversi-ak-pendidikan').textContent = get('jabatan_konversi_ak_pendidikan');
                document.getElementById('review-pangkat-konversi-ak-pendidikan').textContent = get('pangkat_konversi_ak_pendidikan');
                document.getElementById('review-satuan-kerja-konversi-ak-pendidikan').textContent = get('satuan_kerja_konversi_ak_pendidikan');
                document.getElementById('review-golongan-ruang-konversi-ak-pendidikan').textContent = get('golongan_ruang_konversi_ak_pendidikan');

                // Review Dokumen
                const container = document.getElementById('review-documents-konversi-ak-pendidikan');
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

            // --- SUBMIT FORM ---
            document.getElementById('form-konversi-ak-pendidikan').addEventListener('submit', function (e) {
                if (!document.getElementById('confirm-data-konversi-ak-pendidikan').checked) {
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
