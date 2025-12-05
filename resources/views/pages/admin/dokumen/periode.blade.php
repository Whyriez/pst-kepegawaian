@extends('layouts.admin.app')
@section('content')
    <div class="row">
        {{-- Form Tambah --}}
        {{-- Menggunakan col-12 agar full width di HP, dan col-md-4 agar menyamping di layar besar --}}
        {{-- mb-4 ditambahkan agar ada jarak dengan tabel di bawahnya saat mode mobile --}}
        <div class="col-12 col-md-4 mb-4">
            <div class="card h-100"> {{-- h-100 agar tinggi kartu sama jika bersebelahan --}}
                <div class="card-header fw-bold">
                    <i class="bi bi-plus-circle me-1"></i> Buka Periode Baru
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.manajemen_dokumen.periode.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Layanan</label>
                            <select name="jenis_layanan_id" class="form-select" required>
                                <option value="">- Pilih Layanan -</option>
                                @foreach($layanans as $layanan)
                                    <option value="{{ $layanan->id }}">{{ $layanan->nama_layanan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Periode</label>
                            <input type="text" name="nama_periode" class="form-control" placeholder="Cth: Periode April 2025" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Buka</label>
                            <input type="date" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Tutup</label>
                            <input type="date" name="tanggal_selesai" class="form-control" required>
                        </div>
                        <button class="btn btn-primary w-100">Simpan & Buka</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tabel List --}}
        <div class="col-12 col-md-8">
            <div class="card h-100">
                <div class="card-header fw-bold">
                    <i class="bi bi-list-ul me-1"></i> Daftar Periode Aktif & Riwayat
                </div>
                <div class="card-body p-0"> {{-- p-0 agar tabel rapi di dalam card --}}
                    {{-- Wrapper table-responsive wajib ada agar tabel bisa di-scroll di HP --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0 align-middle">
                            <thead class="table-light text-nowrap">
                            <tr>
                                <th>Layanan</th>
                                <th>Periode</th>
                                <th>Jadwal</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($periodes as $p)
                                <tr>
                                    <td class="fw-semibold">{{ $p->jenisLayanan->nama_layanan }}</td>
                                    <td>{{ $p->nama_periode }}</td>
                                    {{-- text-nowrap mencegah tanggal turun ke bawah dan membuat baris jadi tinggi --}}
                                    <td class="text-nowrap">
                                        <small class="d-block text-muted">Mulai:</small>
                                        {{ $p->tanggal_mulai->format('d M Y') }}
                                        <small class="d-block text-muted mt-1">Selesai:</small>
                                        {{ $p->tanggal_selesai->format('d M Y') }}
                                    </td>
                                    <td>
                                        @if($p->isOpen())
                                            <span class="badge bg-success rounded-pill">SEDANG BUKA</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill">TUTUP</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.manajemen_dokumen.periode.destroy', $p->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus periode ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">Belum ada data periode.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
