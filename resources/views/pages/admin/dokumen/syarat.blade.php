@extends('layouts.admin.app')
@section('title', 'Atur Syarat Dokumen')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.manajemen_dokumen.index') }}" class="text-decoration-none text-muted">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Layanan
    </a>
    <div class="d-flex justify-content-between align-items-center mt-2">
        <div>
            <h2 class="h4 fw-bold text-primary mb-0">{{ $layanan->nama_layanan }}</h2>
            <p class="text-muted small mb-0">Kategori: {{ $layanan->kategori }}</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSyaratModal">
            <i class="fas fa-plus me-1"></i> Tambah Dokumen
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Nama Dokumen</th>
                    <th>Tipe File</th>
                    <th>Max Size</th>
                    <th>Wajib?</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($layanan->syaratDokumens as $item)
                <tr>
                    <td class="ps-4 fw-bold">{{ $item->nama_dokumen }}</td>
                    <td><span class="badge bg-secondary">{{ $item->allowed_types }}</span></td>
                    <td>{{ $item->max_size_kb / 1024 }} MB</td>
                    <td>
                        @if($item->is_required)
                            <span class="badge bg-danger">Wajib</span>
                        @else
                            <span class="badge bg-success">Opsional</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-warning me-1" onclick="editSyarat({{ $item }})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.manajemen_dokumen.syarat.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus syarat ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">Belum ada syarat dokumen yang diatur.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL TAMBAH/EDIT SYARAT --}}
<div class="modal fade" id="addSyaratModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.manajemen_dokumen.syarat.store') }}" method="POST" id="formSyarat">
            @csrf
            <div id="methodSyaratField"></div>
            <input type="hidden" name="jenis_layanan_id" value="{{ $layanan->id }}">
            
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSyaratTitle">Tambah Syarat Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Dokumen</label>
                        <input type="text" name="nama_dokumen" id="nama_dokumen" class="form-control" placeholder="Contoh: SK CPNS" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Tipe File (pisahkan koma)</label>
                            <input type="text" name="allowed_types" id="allowed_types" class="form-control" value="pdf" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Max Size (KB)</label>
                            <input type="number" name="max_size_kb" id="max_size_kb" class="form-control" value="2048" required>
                            <small class="text-muted">2048 KB = 2 MB</small>
                        </div>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_required" id="is_required" checked>
                        <label class="form-check-label" for="is_required">Wajib Diupload?</label>
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

<script>
    function editSyarat(data) {
        document.getElementById('modalSyaratTitle').innerText = 'Edit Syarat Dokumen';
        document.getElementById('formSyarat').action = `/admin/manajemen-dokumen/syarat/update/${data.id}`;
        document.getElementById('methodSyaratField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        document.getElementById('nama_dokumen').value = data.nama_dokumen;
        document.getElementById('allowed_types').value = data.allowed_types;
        document.getElementById('max_size_kb').value = data.max_size_kb;
        document.getElementById('is_required').checked = data.is_required == 1;
        
        new bootstrap.Modal(document.getElementById('addSyaratModal')).show();
    }
</script>
@endsection