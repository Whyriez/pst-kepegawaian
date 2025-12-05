@extends('layouts.admin.app')
@section('title', 'Cetak SPTJM')

@section('content')
    <div class="row">

        {{-- KOLOM KIRI: DAFTAR PEGAWAI (TABEL UTAMA) --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    {{-- Tombol Cetak Biru di Atas Kanan --}}
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cetakSptjmModal">
                            <i class="fas fa-print me-1"></i> Cetak SPTJM
                        </button>
                    </div>

                    {{-- Search Bar --}}
                    <div class="d-flex justify-content-end align-items-center mb-3">
                        <label for="searchTable" class="me-2 text-muted small">Search:</label>
                        <input type="text" id="searchTable" class="form-control form-control-sm w-auto d-inline-block"
                               placeholder="">
                    </div>

                    {{-- Tabel Data --}}
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="bg-light">
                            <tr class="text-center">
                                <th width="5%">No</th>
                                <th width="30%">Nama Pegawai</th>
                                <th width="45%">Jabatan</th>
                                <th width="20%">Perihal</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{-- Gunakan @forelse untuk handle jika data kosong --}}
                            @forelse ($data_pegawai as $index => $pegawai)
                                <tr>
                                    <td class="text-center">
                                        {{-- Nomor Urut Dinamis (Mengikuti Halaman) --}}
                                        {{ $data_pegawai->firstItem() + $index }}.
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $pegawai->nama }}</div>
                                        <div class="text-muted small">NIP: {{ $pegawai->nip }}</div>
                                    </td>
                                    <td>
                                        {{ $pegawai->jabatan }}
                                    </td>
                                    <td class="text-center">
                                        {{ $pegawai->perihal }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">
                                        Tidak ada data yang disetujui untuk ditampilkan.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Asli Laravel --}}
                    <div class="mt-3 d-flex justify-content-end">
                        {{-- Ini akan memunculkan link Previous 1 2 3 Next otomatis --}}
                        {{ $data_pegawai->links() }}
                    </div>

                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: SIDEBAR TEMPLATE (UPLOAD SURAT) --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    {{-- Tombol Upload Hijau/Teal --}}
                    <div class="d-grid mb-4">
                        <button class="btn btn-info text-white"
                                style="background-color: #17a2b8; border-color: #17a2b8;"
                                data-bs-toggle="modal" data-bs-target="#uploadSptjmModal">
                            <i class="fas fa-upload me-1"></i> Upload SPTJM
                        </button>
                    </div>

                    {{-- Tabel Sidebar --}}
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="bg-light text-center">
                            <tr>
                                <th width="10%">No</th>
                                <th>Perihal / File</th>
                                <th width="35%">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($templates as $index => $tmpl)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}.</td>
                                    <td>
                                        {{-- Nama Layanan --}}
                                        <span class="fw-bold d-block">
                                            {{ $tmpl->jenisLayanan->nama_layanan ?? 'Umum' }}
                                        </span>

                                        {{-- Periode (Jika ada) --}}
                                        @if($tmpl->periode)
                                            <span class="text-muted small d-block">
                                                (Periode: {{ ucfirst($tmpl->periode) }})
                                            </span>
                                        @endif

                                        {{-- Nama File --}}
                                        <small class="text-success fst-italic" style="font-size: 0.7rem;">
                                            <i class="fas fa-file-pdf me-1"></i> {{ Str::limit($tmpl->file_name, 20) }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-1">

                                            {{-- Tombol Lihat (Buka PDF) --}}
                                            <a href="{{ Storage::url($tmpl->file_path) }}"
                                               target="_blank"
                                               class="text-dark text-decoration-none small">
                                                <i class="fas fa-search"></i> Lihat
                                            </a>

                                            {{-- Tombol Edit (Trigger Modal Edit) --}}
                                            <button type="button"
                                                    class="btn btn-sm btn-info text-white p-1 btn-edit-arsip"
                                                    style="font-size: 0.7rem; line-height: 1;"
                                                    data-id="{{ $tmpl->id }}"
                                                    data-jenis="{{ $tmpl->jenis_layanan_id }}"
                                                    data-periode="{{ $tmpl->periode }}"
                                                    data-filename="{{ $tmpl->file_name }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">
                                        Belum ada SPTJM yang diupload.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>


    {{-- MODAL UPLOAD SPTJM --}}
    <div class="modal fade" id="uploadSptjmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-normal fs-4">Upload SPTJM</h4>
                </div>

                <div class="modal-body pt-4">
                    {{-- Form mengarah ke route store_arsip yang sama dengan Pengantar --}}
                    <form action="{{ route('admin.cetak_surat.store_arsip') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- KUNCI PEMBEDA: Value diset SPTJM --}}
                        <input type="hidden" name="jenis_dokumen" value="SPTJM">

                        {{-- 1. Perihal / Jenis Layanan --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Perihal / Jenis Layanan <span class="text-danger">*</span></label>
                            {{-- ID unik untuk JS --}}
                            <select class="form-select @error('jenis_layanan_id') is-invalid @enderror"
                                    name="jenis_layanan_id" id="upload_jenis_sptjm" required>
                                <option value="" selected disabled>- Pilih Layanan -</option>
                                @foreach($jenis_layanan as $layanan)
                                    <option value="{{ $layanan->id }}" data-kategori="{{ $layanan->kategori }}">
                                        {{ $layanan->nama_layanan }} ({{ $layanan->kategori }})
                                    </option>
                                @endforeach
                            </select>

                            @error('jenis_layanan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 2. Periode (DINAMIS: Hidden by Default) --}}
                        <div class="mb-3" id="div_upload_periode_sptjm" style="display: none;">
                            <label class="form-label fw-bold">
                                Periode Kenaikan Pangkat
                                <span class="text-danger fw-normal small ms-1">Khusus Kenaikan Pangkat</span>
                            </label>
                            <select class="form-select" name="periode">
                                <option value="" selected disabled>- Pilih Bulan -</option>
                                <option value="januari">Januari</option>
                                <option value="februari">Februari</option>
                                <option value="maret">Maret</option>
                                <option value="april">April</option>
                                <option value="mei">Mei</option>
                                <option value="juni">Juni</option>
                                <option value="juli">Juli</option>
                                <option value="agustus">Agustus</option>
                                <option value="september">September</option>
                                <option value="oktober">Oktober</option>
                                <option value="november">November</option>
                                <option value="desember">Desember</option>
                            </select>
                        </div>

                        {{-- 3. Dokumen SPTJM --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Dokumen SPTJM <span class="text-danger">*</span></label>

                            <div class="input-group">
                                <input type="text" class="form-control bg-white" id="fileNameDisplaySPTJM"
                                       placeholder="Upload Berkas" readonly style="cursor: pointer;"
                                       onclick="document.getElementById('fileInputSPTJM').click()">
                                <button class="btn btn-light border" type="button"
                                        onclick="document.getElementById('fileInputSPTJM').click()"
                                        style="background-color: #e9ecef;">Browse
                                </button>
                            </div>

                            <input type="file" name="dokumen" id="fileInputSPTJM" class="d-none" accept=".pdf"
                                   onchange="updateFileNameSPTJM(this)">

                            @error('dokumen')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            <div class="form-text text-muted mt-1">Type File: PDF, Max size : 2MB</div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-primary px-4 py-2">Simpan</button>
                            <button type="button" class="btn btn-danger px-4 py-2" data-bs-dismiss="modal">Kembali</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- MODAL CETAK SPTJM --}}
    <div class="modal fade" id="cetakSptjmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-normal fs-4">Cetak SPTJM</h4>
                </div>

                <div class="modal-body pt-4">
                    <form action="{{ route('admin.cetak_surat.sptjm.export') }}" method="POST" target="_blank">
                        @csrf

                        {{-- 1. Nomor Surat --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Surat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nomor_surat" required
                                   placeholder="Contoh: B-....">
                        </div>

                        {{-- 2. Perihal / Jenis Layanan --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Perihal Surat <span class="text-danger">*</span></label>
                            {{-- ID unik untuk JS --}}
                            <select class="form-select" name="jenis_layanan_id" id="cetak_jenis_sptjm" required>
                                <option value="" selected disabled>- Pilih Layanan -</option>
                                @foreach($jenis_layanan as $layanan)
                                    <option value="{{ $layanan->id }}" data-kategori="{{ $layanan->kategori }}">
                                        {{ $layanan->nama_layanan }} ({{ $layanan->kategori }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 3. Periode (DINAMIS: Hidden by Default) --}}
                        <div class="mb-3" id="div_cetak_periode_sptjm" style="display: none;">
                            <label class="form-label fw-bold">
                                Periode Kenaikan Pangkat
                                <span class="text-danger fw-normal small ms-1">Khusus Kenaikan Pangkat</span>
                            </label>
                            <select class="form-select" name="periode">
                                <option value="" selected disabled>- Pilih Bulan -</option>
                                <option value="januari">Januari</option>
                                <option value="februari">Februari</option>
                                <option value="maret">Maret</option>
                                <option value="april">April</option>
                                <option value="mei">Mei</option>
                                <option value="juni">Juni</option>
                                <option value="juli">Juli</option>
                                <option value="agustus">Agustus</option>
                                <option value="september">September</option>
                                <option value="oktober">Oktober</option>
                                <option value="november">November</option>
                                <option value="desember">Desember</option>
                            </select>
                        </div>

                        {{-- 4. Status Kepala Kantor --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status Kepala Kantor <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="status_kepala" required>
                                <option value="" selected disabled>- Pilih Status -</option>
                                <option value="definitif">DEFINITIF</option>
                                <option value="plt">PLH</option>
                                <option value="plh">PLT</option>
                            </select>
                        </div>

                        {{-- 5. Metode Surat --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Metode Surat <span class="text-danger">*</span></label>
                            <select class="form-select" name="metode" required>
                                <option value="" selected disabled>- Pilih Metode -</option>
                                <option value="konvensional">Konvensional</option>
                                <option value="tte">TTE</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-primary px-4 py-2">Cetak PDF</button>
                            <button type="button" class="btn btn-danger px-4 py-2" data-bs-dismiss="modal">Kembali
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Update Nama File (Khusus halaman SPTJM) --}}
    {{-- Script untuk Nama File Upload --}}
    <script>
        function updateFileNameSPTJM(input) {
            var fileName = input.files[0].name;
            document.getElementById('fileNameDisplaySPTJM').value = fileName;
        }
    </script>

    {{-- Script untuk Logic Show/Hide Periode --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Fungsi Reusable (Sama persis dengan Pengantar)
            function handlePeriodeVisibility(selectId, divId) {
                const select = document.getElementById(selectId);
                const div = document.getElementById(divId);

                if (!select || !div) return;

                function check() {
                    const selectedOption = select.options[select.selectedIndex];
                    const kategori = selectedOption ? selectedOption.getAttribute('data-kategori') : '';

                    // Jika Kategori mengandung 'Kenaikan Pangkat', tampilkan dropdown periode
                    if (kategori && kategori.toLowerCase().includes('kenaikan pangkat')) {
                        div.style.display = 'block';
                    } else {
                        div.style.display = 'none';
                        // Reset value periode agar aman
                        const periodeSelect = div.querySelector('select');
                        if (periodeSelect) periodeSelect.value = "";
                    }
                }

                select.addEventListener('change', check);
                check(); // Cek saat load
            }

            // Terapkan ke Modal Cetak SPTJM
            handlePeriodeVisibility('cetak_jenis_sptjm', 'div_cetak_periode_sptjm');
            handlePeriodeVisibility('upload_jenis_sptjm', 'div_upload_periode_sptjm');
        });
    </script>
@endsection
