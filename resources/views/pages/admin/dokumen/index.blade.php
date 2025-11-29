@extends('layouts.admin.app')
@section('title', 'Manajemen Jenis Layanan')

@section('content')
    {{-- HEADER & TOMBOL TAMBAH --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold text-primary mb-1">Manajemen Jenis Layanan</h2>
            <p class="text-muted small mb-0">Atur layanan dan syarat dokumen terkait</p>
        </div>
        <button class="btn btn-primary" onclick="showAddModal()">
            <i class="fas fa-plus me-1"></i> Tambah Layanan
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- TOOLBAR FILTER & SEARCH --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form action="{{ route('admin.manajemen_dokumen.index') }}" method="GET" class="row g-2 align-items-center">
                {{-- 1. Search Nama Layanan --}}
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 bg-light"
                            placeholder="Cari nama layanan..." value="{{ request('search') }}">
                    </div>
                </div>

                {{-- 2. Filter Kategori (DROPDOWN DARI DB) --}}
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-filter text-muted"></i></span>
                        <select name="kategori" class="form-select border-start-0 bg-light" onchange="this.form.submit()">
                            <option value="">-- Semua Kategori --</option>
                            @foreach ($kategori_list as $cat)
                                <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- 3. Tombol Reset --}}
                <div class="col-md-auto">
                    {{-- Tombol submit hidden untuk search enter --}}
                    <button type="submit" class="d-none"></button>
                    
                    @if (request('search') || request('kategori'))
                        <a href="{{ route('admin.manajemen_dokumen.index') }}" class="btn btn-outline-secondary btn-sm"
                            title="Reset Filter">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Nama Layanan</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3">Syarat Dokumen</th>
                            <th class="py-3">Slug (Kode)</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($layanan as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $item->nama_layanan }}</div>
                                </td>
                                <td>
                                    {{-- Badge Warna-warni Statis Sederhana --}}
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                        {{ $item->kategori }}
                                    </span>
                                </td>
                                <td>
                                    @if ($item->syarat_dokumens_count > 0)
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="fas fa-file-alt me-1"></i> {{ $item->syarat_dokumens_count }} Syarat
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger">Belum diatur</span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    <code>{{ $item->slug }}</code>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.manajemen_dokumen.syarat', $item->id) }}"
                                            class="btn btn-sm btn-outline-primary" title="Atur Dokumen">
                                            <i class="fas fa-list-check"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-warning"
                                            onclick='editLayanan(@json($item))' title="Edit Nama">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <form action="{{ route('admin.manajemen_dokumen.layanan.destroy', $item->id) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus layanan ini? Semua syarat dokumen di dalamnya juga akan terhapus.')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                        <p class="mb-0">Data tidak ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-end">
                {{ $layanan->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH/EDIT LAYANAN --}}
    <div class="modal fade" id="addLayananModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.manajemen_dokumen.layanan.store') }}" method="POST" id="formLayanan">
                @csrf
                <div id="methodField"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Jenis Layanan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Layanan</label>
                            <input type="text" name="nama_layanan" id="nama_layanan" class="form-control"
                                placeholder="Contoh: Kenaikan Pangkat Reguler" required>
                        </div>
                        
                        {{-- TETAP INPUT TEXT AGAR BISA INPUT BEBAS --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori</label>
                            {{-- Datalist untuk suggestion kategori yg sudah ada (Optional, UX Improvement) --}}
                            <input type="text" name="kategori" id="kategori" class="form-control" 
                                list="kategoriOptions" placeholder="Ketik kategori baru atau pilih..." required>
                            
                            {{-- Suggestion list dari data yang sudah ada --}}
                            <datalist id="kategoriOptions">
                                @foreach($kategori_list as $cat)
                                    <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                            <div class="form-text text-muted">Ketik kategori baru atau pilih dari saran.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let modalInstance = null;

        function getModal() {
            if (!modalInstance) {
                const modalEl = document.getElementById('addLayananModal');
                modalInstance = new bootstrap.Modal(modalEl);
            }
            return modalInstance;
        }

        function showAddModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Jenis Layanan';
            document.getElementById('formLayanan').action = "{{ route('admin.manajemen_dokumen.layanan.store') }}";
            document.getElementById('formLayanan').reset(); 
            document.getElementById('methodField').innerHTML = ''; 
            
            getModal().show();
        }

        function editLayanan(data) {
            document.getElementById('modalTitle').innerText = 'Edit Jenis Layanan';
            document.getElementById('formLayanan').action = `/admin/manajemen-dokumen/layanan/update/${data.id}`;
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

            // Isi data
            document.getElementById('nama_layanan').value = data.nama_layanan;
            document.getElementById('kategori').value = data.kategori;

            getModal().show();
        }
    </script>
@endpush