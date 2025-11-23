@extends('layouts.user.app')
@section('title', 'Kenaikan Pangkat Struktural')

{{-- Tambahkan SweetAlert --}}
@push('styles')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Form KP Struktural</h2>
                    <p class="text-muted mb-0">Formulir untuk kenaikan pangkat struktural</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>

        {{-- Progress Steps --}}
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
                {{-- ACTION KE ROUTE STORE --}}
                <form id="form-kp-struktural" action="{{ route('kp.struktural.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- STEP 1: DATA PEGAWAI --}}
                    <div class="form-step active" id="step-1-kp-struktural">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2"><i class="fas fa-user me-2"></i>Data Diri Pegawai</h5>
                            <p class="text-muted">Isi data diri pegawai yang mengajukan</p>
                        </div>

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Cek Data dengan NIP</h6>
                                <div class="input-group w-75">
                                    <input type="text" class="form-control" id="nip_pegawai_kp_struktural"
                                        placeholder="Masukkan NIP Pegawai" value="{{ Auth::user()->pegawai->nip ?? '' }}">
                                    <button class="btn btn-outline-primary" type="button" id="btn-cek-nip-kp-struktural">
                                        <i class="fas fa-search me-2"></i>Cek NIP
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pegawai <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_pegawai_kp_struktural" name="nama_pegawai_kp_struktural" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan Pegawai <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="jabatan_kp_struktural" name="jabatan_kp_struktural" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pangkat Pegawai <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pangkat_kp_struktural" name="pangkat_kp_struktural" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP Pegawai <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" id="nip_display_kp_struktural" name="nip_display_kp_struktural" required readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="unit_kerja_kp_struktural" name="unit_kerja_kp_struktural" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Golongan Ruang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="golongan_ruang_kp_struktural" name="golongan_ruang_kp_struktural" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary btn-next-kp-struktural" data-next="2">
                                Lanjut <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: DOKUMEN (DINAMIS) --}}
                    <div class="form-step" id="step-2-kp-struktural">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2"><i class="fas fa-file-upload me-2"></i>Upload Dokumen</h5>
                            <p class="text-muted">Dokumen wajib diunggah dalam format PDF (Max 2MB)</p>
                        </div>

                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle me-1"></i> <span id="upload-progress">0/{{ count($syarat) }}</span> dokumen terunggah.</small>
                        </div>

                        <div class="row">
                            @forelse($syarat as $dokumen)
                                <div class="col-md-6 mb-3">
                                    <div class="file-upload-card h-100 p-3 border rounded bg-white">
                                        <label class="form-label fw-bold small">
                                            {{ $dokumen->nama_dokumen }}
                                            @if($dokumen->is_required) <span class="text-danger">*</span> @else <span class="text-muted">(Opsional)</span> @endif
                                        </label>
                                        <div class="file-input-wrapper">
                                            <input type="file" class="form-control form-control-sm" 
                                                id="file_{{ $dokumen->id }}" 
                                                name="file_{{ $dokumen->id }}" 
                                                accept=".pdf"
                                                {{ $dokumen->is_required ? 'required' : '' }}>
                                            <div class="file-preview mt-2 small text-success" id="preview-file_{{ $dokumen->id }}"></div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12"><div class="alert alert-warning">Belum ada syarat dokumen yang diatur di database untuk layanan ini.</div></div>
                            @endforelse
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Periode Kenaikan Pangkat <span class="text-danger">*</span></label>
                                <select class="form-control" id="periode_kenaikan_pangkat_kp_struktural" name="periode_kenaikan_pangkat_kp_struktural" required>
                                    <option value="">Pilih Periode</option>
                                    <option value="April">April</option>
                                    <option value="Oktober">Oktober</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary btn-prev-kp-struktural" data-prev="1">Kembali</button>
                            <button type="button" class="btn btn-primary btn-next-kp-struktural" data-next="3">Lanjut</button>
                        </div>
                    </div>

                    {{-- STEP 3: KONFIRMASI --}}
                    <div class="form-step" id="step-3-kp-struktural">
                        <div class="step-header mb-4">
                            <h5 class="fw-bold text-primary mb-2"><i class="fas fa-check-circle me-2"></i>Konfirmasi</h5>
                            <p class="text-muted">Tinjau kembali data sebelum dikirim.</p>
                        </div>

                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold">Ringkasan</h6>
                                <div class="row small">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Nama:</strong> <span id="rev-nama">-</span></p>
                                        <p class="mb-1"><strong>NIP:</strong> <span id="rev-nip">-</span></p>
                                        <p class="mb-1"><strong>Jabatan:</strong> <span id="rev-jabatan">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Unit Kerja:</strong> <span id="rev-unit">-</span></p>
                                        <p class="mb-1"><strong>Periode:</strong> <span id="rev-periode">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold">Dokumen</h6>
                                <div id="rev-docs" class="small text-success"></div>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirm-data" required>
                            <label class="form-check-label" for="confirm-data">Saya menyatakan data benar.</label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary btn-prev-kp-struktural" data-prev="2">Kembali</button>
                            <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane me-2"></i> Kirim Pengajuan</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <style>
        .progress-steps { display: flex; justify-content: space-between; position: relative; margin-bottom: 20px; }
        .progress-steps::before { content: ''; position: absolute; top: 15px; left: 0; right: 0; height: 3px; background: #e9ecef; z-index: 1; }
        .step { position: relative; z-index: 2; text-align: center; }
        .step-circle { width: 35px; height: 35px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; margin: 0 auto 5px; }
        .step.active .step-circle { background: #0d6efd; color: white; }
        .form-step { display: none; }
        .form-step.active { display: block; animation: fadeIn 0.4s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- SWAL ALERTS ---
        @if(session('success')) Swal.fire('Berhasil', "{{ session('success') }}", 'success'); @endif
        @if(session('error')) Swal.fire('Gagal', "{{ session('error') }}", 'error'); @endif
        @if($errors->any()) Swal.fire('Validasi Gagal', 'Cek inputan Anda', 'warning'); @endif

        // --- 1. STEPPER ---
        const steps = document.querySelectorAll('.form-step');
        const progressSteps = document.querySelectorAll('.progress-steps .step');

        function showStep(idx) {
            steps.forEach(el => el.classList.remove('active'));
            progressSteps.forEach(el => el.classList.remove('active'));
            document.getElementById(`step-${idx}-kp-struktural`).classList.add('active');
            for(let i=0; i<idx; i++) progressSteps[i].classList.add('active');
            if(idx == 3) updateReview();
        }

        document.querySelectorAll('.btn-next-kp-struktural').forEach(btn => {
            btn.addEventListener('click', function() {
                const next = this.dataset.next;
                if(next == 2 && !document.getElementById('nama_pegawai_kp_struktural').value) {
                    Swal.fire('Data Kosong', 'Cek NIP terlebih dahulu!', 'warning'); return;
                }
                showStep(next);
            });
        });

        document.querySelectorAll('.btn-prev-kp-struktural').forEach(btn => {
            btn.addEventListener('click', function() { showStep(this.dataset.prev); });
        });

        // --- 2. CEK NIP (REAL AJAX) ---
        const btnCek = document.getElementById('btn-cek-nip-kp-struktural');
        if(btnCek) {
            btnCek.addEventListener('click', function() {
                const nip = document.getElementById('nip_pegawai_kp_struktural').value;
                if(!nip) { Swal.fire('Isi NIP!', '', 'warning'); return; }

                const oldHtml = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                fetch(`{{ url('/kenaikan-pangkat/ajax/cek-nip') }}/${nip}`)
                    .then(res => res.json())
                    .then(res => {
                        if(res.success) {
                            const d = res.data;
                            const set = (id, val) => document.getElementById(id).value = val || '';
                            set('nama_pegawai_kp_struktural', d.nama);
                            set('jabatan_kp_struktural', d.jabatan);
                            set('pangkat_kp_struktural', d.pangkat);
                            set('nip_display_kp_struktural', d.nip);
                            set('unit_kerja_kp_struktural', d.unit_kerja);
                            set('golongan_ruang_kp_struktural', d.golongan_ruang);
                            Swal.fire('Ditemukan', 'Data pegawai berhasil dimuat', 'success');
                        } else {
                            Swal.fire('Gagal', 'NIP tidak ditemukan', 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Gagal koneksi server', 'error'))
                    .finally(() => this.innerHTML = oldHtml);
            });
        }

        // --- 3. PREVIEW & REVIEW ---
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                const preview = document.getElementById(`preview-${this.id}`);
                if(this.files.length > 0) preview.innerHTML = `<i class="fas fa-check"></i> ${this.files[0].name}`;
            });
        });

        function updateReview() {
            const get = (id) => document.getElementById(id).value || '-';
            document.getElementById('rev-nama').textContent = get('nama_pegawai_kp_struktural');
            document.getElementById('rev-nip').textContent = get('nip_display_kp_struktural');
            document.getElementById('rev-jabatan').textContent = get('jabatan_kp_struktural');
            document.getElementById('rev-unit').textContent = get('unit_kerja_kp_struktural');
            
            const periode = document.getElementById('periode_kenaikan_pangkat_kp_struktural');
            document.getElementById('rev-periode').textContent = periode.options[periode.selectedIndex]?.text || '-';

            const docList = document.getElementById('rev-docs');
            docList.innerHTML = '';
            let hasFile = false;
            document.querySelectorAll('input[type="file"]').forEach(input => {
                if(input.files.length > 0) {
                    hasFile = true;
                    const name = input.files[0].name;
                    const label = input.closest('div').querySelector('label').textContent.trim();
                    docList.innerHTML += `<div><i class="fas fa-check-circle"></i> ${label}: ${name}</div>`;
                }
            });
            if(!hasFile) docList.innerHTML = '<span class="text-danger">Belum ada file.</span>';
        }

        // --- 4. SUBMIT FORM ---
        document.getElementById('form-kp-struktural').addEventListener('submit', function(e) {
            if(!document.getElementById('confirm-data').checked) {
                e.preventDefault();
                Swal.fire('Konfirmasi', 'Anda harus menyetujui kebenaran data', 'warning');
            } else {
                Swal.fire({
                    title: 'Sedang Mengirim...',
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