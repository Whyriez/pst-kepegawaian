@extends('layouts.admin.app')
@section('content')
    <div class="row">
        {{-- Form Tambah (SAMA SEPERTI SEBELUMNYA) --}}
        <div class="col-12 col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="bi bi-plus-circle me-1 text-primary"></i> Buka Periode Baru
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.manajemen_dokumen.periode.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Kategori Layanan</label>
                            <select name="kategori" class="form-select" required>
                                <option value="">- Pilih Kategori -</option>
                                @foreach($kategoris as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted small">
                                Periode akan diterapkan ke semua layanan dalam kategori ini.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nama Periode</label>
                            <input type="text" name="nama_periode" class="form-control"
                                   placeholder="Cth: Periode April 2025" required>
                        </div>

                        <div class="mb-3 form-check form-switch bg-light p-2 rounded ps-5">
                            <input class="form-check-input" type="checkbox" id="is_unlimited" name="is_unlimited"
                                   value="1" onchange="toggleDates()">
                            <label class="form-check-label fw-semibold" for="is_unlimited">Selalu Buka
                                (Unlimited)</label>
                        </div>

                        <div id="date-inputs">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Tanggal Buka</label>
                                <input type="date" name="tanggal_mulai" id="tgl_mulai" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Tanggal Tutup</label>
                                <input type="date" name="tanggal_selesai" id="tgl_selesai" class="form-control">
                            </div>
                        </div>

                        <button class="btn btn-primary w-100 py-2">
                            <i class="bi bi-save me-1"></i> Simpan & Buka
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tabel List --}}
        <div class="col-12 col-md-8">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="bi bi-list-ul me-1 text-primary"></i> Riwayat Periode
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light text-nowrap">
                            <tr>
                                <th>Kategori Layanan</th> {{-- Judul kolom berubah --}}
                                <th>Nama Periode</th>
                                <th>Jadwal</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{-- Loop Grouping --}}
                            @forelse($periodes as $key => $group)
                                @php
                                    // Ambil 1 sampel data dari grup untuk ditampilkan
                                    $p = $group->first();
                                    $jumlahLayanan = $group->count();
                                @endphp
                                <tr>
                                    <td>
                                        {{-- Tampilkan Kategori Utama Saja --}}
                                        <div class="fw-bold text-primary">{{ $p->jenisLayanan->kategori }}</div>
                                        <small class="text-muted">Mencakup {{ $jumlahLayanan }} jenis layanan</small>
                                    </td>
                                    <td>{{ $p->nama_periode }}</td>
                                    <td class="text-nowrap">
                                        @if($p->is_unlimited)
                                            <span class="badge bg-info text-dark">
                                                <i class="bi bi-infinity me-1"></i> Tanpa Batas Waktu
                                            </span>
                                        @else
                                            @if($p->tanggal_mulai && $p->tanggal_selesai)
                                                <small
                                                    class="d-block text-muted">Mulai: {{ $p->tanggal_mulai->format('d M Y') }}</small>
                                                <small
                                                    class="d-block text-muted">Tutup: {{ $p->tanggal_selesai->format('d M Y') }}</small>
                                            @else
                                                <span class="text-danger small">Error Tanggal</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($p->isOpen())
                                            <span class="badge bg-success rounded-pill">BUKA</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill">TUTUP</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            {{-- TOMBOL EDIT --}}
                                            <button type="button" class="btn btn-warning btn-sm text-white"
                                                    onclick="editPeriode(
                '{{ $p->id }}',
                '{{ $p->nama_periode }}',
                '{{ $p->is_unlimited }}',
                '{{ $p->tanggal_mulai ? $p->tanggal_mulai->format('Y-m-d') : '' }}',
                '{{ $p->tanggal_selesai ? $p->tanggal_selesai->format('Y-m-d') : '' }}'
            )">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>

                                            {{-- TOMBOL HAPUS --}}
                                            <form
                                                action="{{ route('admin.manajemen_dokumen.periode.destroy', $p->id) }}"
                                                method="POST">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-danger btn-sm border-0"
                                                        onclick="return confirm('Yakin hapus periode {{ $p->nama_periode }}? Semua layanan terkait akan ditutup.')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada periode yang dibuka.
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

    <div class="modal fade" id="modalEditPeriode" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Periode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditPeriode" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">

                        <div class="alert alert-info small py-2">
                            <i class="bi bi-info-circle me-1"></i> Perubahan akan diterapkan ke semua layanan dalam
                            kategori ini.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nama Periode</label>
                            <input type="text" name="nama_periode" id="edit_nama_periode" class="form-control" required>
                        </div>

                        {{-- Switch Unlimited Edit --}}
                        <div class="mb-3 form-check form-switch bg-light p-2 rounded ps-5">
                            <input class="form-check-input" type="checkbox" id="edit_is_unlimited" name="is_unlimited"
                                   value="1" onchange="toggleDatesEdit()">
                            <label class="form-check-label fw-semibold" for="edit_is_unlimited">Selalu Buka
                                (Unlimited)</label>
                        </div>

                        {{-- Input Tanggal Edit --}}
                        <div id="edit-date-inputs">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Tanggal Buka</label>
                                <input type="date" name="tanggal_mulai" id="edit_tgl_mulai" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Tanggal Tutup</label>
                                <input type="date" name="tanggal_selesai" id="edit_tgl_selesai" class="form-control">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning text-white">Update Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleDates() {
            var isUnlimited = document.getElementById('is_unlimited').checked;
            var dateDiv = document.getElementById('date-inputs');
            var tglMulai = document.getElementById('tgl_mulai');
            var tglSelesai = document.getElementById('tgl_selesai');

            if (isUnlimited) {
                dateDiv.style.display = 'none';
                tglMulai.required = false;
                tglSelesai.required = false;
                tglMulai.value = '';
                tglSelesai.value = '';
            } else {
                dateDiv.style.display = 'block';
                tglMulai.required = true;
                tglSelesai.required = true;
            }
        }

        function editPeriode(id, nama, isUnlimited, tglMulai, tglSelesai) {
            // 1. Set URL Form Action
            // Ganti '999' placeholder dengan ID asli
            let url = "{{ route('admin.manajemen_dokumen.periode.update', ':id') }}";
            url = url.replace(':id', id);
            document.getElementById('formEditPeriode').action = url;

            // 2. Isi Value Input
            document.getElementById('edit_nama_periode').value = nama;

            // 3. Set Checkbox Unlimited
            let checkUnlimited = document.getElementById('edit_is_unlimited');
            // Perhatikan: isUnlimited dari parameter string "1" atau "" (kosong)
            checkUnlimited.checked = (isUnlimited == "1");

            // 4. Set Tanggal
            document.getElementById('edit_tgl_mulai').value = tglMulai;
            document.getElementById('edit_tgl_selesai').value = tglSelesai;

            // 5. Jalankan logika hide/show tanggal berdasarkan nilai checkbox
            toggleDatesEdit();

            // 6. Tampilkan Modal
            var myModal = new bootstrap.Modal(document.getElementById('modalEditPeriode'));
            myModal.show();
        }

        function toggleDatesEdit() {
            var isUnlimited = document.getElementById('edit_is_unlimited').checked;
            var dateDiv = document.getElementById('edit-date-inputs');
            var tglMulai = document.getElementById('edit_tgl_mulai');
            var tglSelesai = document.getElementById('edit_tgl_selesai');

            if (isUnlimited) {
                dateDiv.style.display = 'none';
                tglMulai.required = false;
                tglSelesai.required = false;
            } else {
                dateDiv.style.display = 'block';
                tglMulai.required = true;
                tglSelesai.required = true;
            }
        }
    </script>
@endsection
