@extends('layouts.admin.app')
@section('title', 'Cetak Surat Pengantar')

@section('content')
    <div class="row">

        {{-- KOLOM KIRI: DAFTAR PEGAWAI (TABEL UTAMA) --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    {{-- Tombol Cetak Biru di Atas Kanan --}}
                    <div class="d-flex justify-content-end mb-3">
                        {{-- ADDED ATTRIBUTES HERE --}}
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cetakModal">
                            <i class="fas fa-print me-1"></i> Cetak Surat Pengantar
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
                                @foreach ($data_pegawai as $index => $pegawai)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}.</td>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Dummy (Agar mirip tabel asli) --}}
                    <div class="mt-3">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-end pagination-sm mb-0">
                                <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                        </nav>
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
                        {{-- TAMBAHKAN DATA-BS DI SINI --}}
                        <button class="btn btn-info text-white" style="background-color: #17a2b8; border-color: #17a2b8;"
                            data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="fas fa-upload me-1"></i> Upload Surat Pengantar
                        </button>
                    </div>

                    {{-- Tabel Sidebar --}}
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th width="10%">No</th>
                                    <th>Perihal</th>
                                    <th width="35%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Baris Kosong (Row 1 & 2 di gambar kosong) --}}
                                <tr style="height: 40px;">
                                    <td class="text-center">1.</td>
                                    <td></td>
                                    <td class="text-center">
                                        <a href="#" class="text-dark me-2 text-decoration-none"><i
                                                class="fas fa-search"></i> Lihat</a>
                                        <a href="#" class="btn btn-xs btn-info text-white px-2 py-0"
                                            style="font-size: 0.7rem;"><i class="fas fa-edit"></i> Edit</a>
                                    </td>
                                </tr>
                                <tr style="height: 40px;">
                                    <td class="text-center">2.</td>
                                    <td></td>
                                    <td class="text-center">
                                        <a href="#" class="text-dark me-2 text-decoration-none"><i
                                                class="fas fa-search"></i> Lihat</a>
                                        <a href="#" class="btn btn-xs btn-info text-white px-2 py-0"
                                            style="font-size: 0.7rem;"><i class="fas fa-edit"></i> Edit</a>
                                    </td>
                                </tr>

                                {{-- Baris Isi (Row 3 dst) --}}
                                @foreach ($templates as $index => $tmpl)
                                    <tr>
                                        <td class="text-center">{{ $index + 3 }}.</td>
                                        <td>{{ $tmpl->perihal }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                <a href="#" class="text-dark text-decoration-none small">
                                                    <i class="fas fa-search"></i> Lihat
                                                </a>
                                                <a href="#" class="btn btn-sm btn-info text-white p-1"
                                                    style="font-size: 0.7rem; line-height: 1;">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>


    {{-- MODAL UPLOAD SURAT PENGANTAR --}}
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                {{-- Header: Judul Besar --}}
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-normal fs-4" id="uploadModalLabel">Upload Surat Pengantar</h4>
                    {{-- Tombol X dihapus agar mirip gambar, user pakai tombol Kembali --}}
                </div>

                <div class="modal-body pt-4">
                    <form action="#" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- 1. Perihal Surat --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Perihal Surat <span class="text-danger">*</span></label>
                            <select class="form-select" name="perihal" required>
                                <option value="" selected disabled>- Pilih -</option>
                                <option value="kp">Kenaikan Pangkat</option>
                                <option value="gelar">Pencantuman Gelar</option>
                                <option value="pensiun">Pensiun</option>
                            </select>
                        </div>

                        {{-- 2. Periode Kenaikan Pangkat --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Periode Kenaikan Pangkat
                                <span class="text-danger fw-normal small ms-1">Kosongkan jika bukan Kenaikan Pangkat</span>
                            </label>
                            <select class="form-select" name="periode">
                                <option value="" selected>- Pilih -</option>
                                <option value="april">April</option>
                                <option value="oktober">Oktober</option>
                            </select>
                        </div>

                        {{-- 3. Dokumen Surat Pengantar (Custom File Input) --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Dokumen Surat Pengantar <span
                                    class="text-danger">*</span></label>

                            {{-- Input Group untuk meniru tampilan: [ Text Box ] [ Browse Button ] --}}
                            <div class="input-group">
                                <input type="text" class="form-control bg-white" id="fileNameDisplay"
                                    placeholder="Upload Berkas" readonly style="cursor: pointer;"
                                    onclick="document.getElementById('fileInput').click()">
                                <button class="btn btn-light border" type="button"
                                    onclick="document.getElementById('fileInput').click()"
                                    style="background-color: #e9ecef;">Browse</button>
                            </div>

                            {{-- Input File Asli (Disembunyikan) --}}
                            <input type="file" name="dokumen" id="fileInput" class="d-none" accept=".pdf"
                                onchange="updateFileName(this)">

                            <div class="form-text text-muted mt-1">Type File: PDF, Max size : 2MB</div>
                        </div>

                        {{-- Footer Tombol (Simpan & Kembali) --}}
                        <div class="d-flex justify-content-between mt-4">
                            {{-- Tombol Simpan (Biru) --}}
                            <button type="submit" class="btn btn-primary px-4 py-2">Simpan</button>

                            {{-- Tombol Kembali (Merah) --}}
                            <button type="button" class="btn btn-danger px-4 py-2"
                                data-bs-dismiss="modal">Kembali</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- MODAL CETAK SURAT PENGANTAR --}}
    <div class="modal fade" id="cetakModal" tabindex="-1" aria-labelledby="cetakModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                {{-- Header: Simple Title --}}
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-normal fs-4" id="cetakModalLabel">Cetak Surat Pengantar</h4>
                </div>

                <div class="modal-body pt-4">
                    <form action="#" method="POST" target="_blank"> {{-- target_blank for printing usually --}}
                        @csrf

                        {{-- 1. Nomor Surat --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Surat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nomor_surat" required>
                        </div>

                        {{-- 2. Status Kepala Kantor --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status Kepala Kantor <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="status_kepala" required>
                                <option value="" selected disabled>- Pilih Status -</option>
                                <option value="definitif">Definitif</option>
                                <option value="plt">Plt (Pelaksana Tugas)</option>
                                <option value="plh">Plh (Pelaksana Harian)</option>
                            </select>
                        </div>

                        {{-- 3. Perihal Surat --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Perihal Surat <span class="text-danger">*</span></label>
                            <select class="form-select" name="perihal" required>
                                <option value="" selected disabled>- Pilih -</option>
                                <option value="kp">Kenaikan Pangkat</option>
                                <option value="gelar">Pencantuman Gelar</option>
                                <option value="pensiun">Pensiun</option>
                            </select>
                        </div>

                        {{-- 4. Periode Kenaikan Pangkat --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Periode Kenaikan Pangkat
                                <span class="text-danger fw-normal small ms-1">Kosongkan jika bukan Kenaikan Pangkat</span>
                            </label>
                            <select class="form-select" name="periode">
                                <option value="" selected>- Pilih -</option>
                                <option value="april">April</option>
                                <option value="oktober">Oktober</option>
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

                        {{-- Footer Buttons --}}
                        <div class="d-flex justify-content-between mt-4">
                            {{-- Tombol Cetak (Biru) --}}
                            <button type="submit" class="btn btn-primary px-4 py-2">Cetak</button>

                            {{-- Tombol Kembali (Merah) --}}
                            <button type="button" class="btn btn-danger px-4 py-2"
                                data-bs-dismiss="modal">Kembali</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT SEDERHANA UNTUK UPDATE NAMA FILE --}}
    <script>
        function updateFileName(input) {
            var fileName = input.files[0].name;
            document.getElementById('fileNameDisplay').value = fileName;
        }
    </script>
@endsection
