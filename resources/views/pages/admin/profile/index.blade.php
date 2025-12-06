@extends('layouts.admin.app') {{-- Pastikan extend layout admin --}}
@section('title', 'Profil Admin')

@section('content')
<div class="container-fluid p-0">
    <div class="page-header mb-4">
        <h2 class="h3 fw-bold text-dark mb-1">Profil Admin</h2>
        <p class="text-muted mb-0">Lengkapi data diri dan Satuan Kerja Anda</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Foto Profil --}}
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm text-center p-4">
                    <div class="mb-3 position-relative d-inline-block">
                        @php
                            $avatar = $user->avatar ? asset('storage/'.$user->avatar) : "https://ui-avatars.com/api/?name=".urlencode($user->name)."&background=0d6efd&color=fff";
                        @endphp
                        <img src="{{ $avatar }}" class="rounded-circle img-thumbnail shadow-sm" width="128" height="128" id="preview-img">
                        <button type="button" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle" onclick="document.getElementById('avatar').click()">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <h5 class="fw-bold">{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                    <input type="file" name="avatar" id="avatar" class="d-none" onchange="previewImage(event)">
                </div>
            </div>

            {{-- Form Data --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold text-primary mb-0"><i class="fas fa-user-shield me-2"></i>Data Pegawai (Admin)</h5>
                    </div>
                    <div class="card-body p-4">

                        {{-- PEMILIHAN SATUAN KERJA (KHUSUS ADMIN) --}}
                        <div class="alert alert-warning border-0 d-flex align-items-center mb-4">
                            <i class="fas fa-building me-3 fa-2x"></i>
                            <div>
                                <strong>Penting:</strong> Pilih Satuan Kerja tempat Anda bertugas.
                                <br>Data ini akan digunakan sebagai default saat Anda menambah pegawai baru.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Satuan Kerja</label>
                            <select name="satuan_kerja_id" class="form-select @error('satuan_kerja_id') is-invalid @enderror">
                                <option value="">-- Pilih Satuan Kerja --</option>
                                @foreach($satuanKerjas as $satker)
                                    <option value="{{ $satker->id }}"
                                        {{ (old('satuan_kerja_id', $user->pegawai?->satuan_kerja_id) == $satker->id) ? 'selected' : '' }}>
                                        {{ $satker->kode_satuan_kerja }} - {{ $satker->nama_satuan_kerja }}
                                    </option>
                                @endforeach
                            </select>
                            @error('satuan_kerja_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->pegawai?->nama_lengkap ?? $user->name) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">NIP</label>
                                <input type="number" name="nip" class="form-control" value="{{ old('nip', $user->pegawai?->nip) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Email Login</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Nomor WhatsApp (Admin)</label>
                                <input type="text" name="nomor_telepon" class="form-control"
                                       placeholder="08xxxxxxxxxx"
                                       value="{{ old('nomor_telepon', $user->nomor_telepon) }}">
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem">*Nomor ini akan digunakan pada link 'Hubungi Admin' di halaman Login.</small>
                            </div>

                        </div>


                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $user->pegawai?->jabatan) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Pangkat</label>
                                <input type="text" name="pangkat" class="form-control" placeholder="Contoh: Pembina" value="{{ old('pangkat', $user->pegawai?->pangkat) }}">
                            </div>
                            <div class=" mb-3">
                                <label class="form-label fw-bold small">Golongan Ruang</label>
                                <select name="golongan_ruang" class="form-select">
                                    <option value="">Pilih Golongan</option>
                                    @foreach(['II/a','II/b','II/c','II/d','III/a','III/b','III/c','III/d','IV/a','IV/b','IV/c','IV/d','IV/e'] as $gol)
                                        <option value="{{ $gol }}" {{ (old('golongan_ruang', $user->pegawai?->golongan_ruang) == $gol) ? 'selected' : '' }}>{{ $gol }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $user->pegawai?->tempat_lahir) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $user->pegawai?->tanggal_lahir ? \Carbon\Carbon::parse($user->pegawai->tanggal_lahir)->format('Y-m-d') : '') }}">
                            </div>
                        </div>

                         <div class="mb-3">
                            <label class="form-label fw-bold small">Pendidikan Terakhir</label>
                            <select name="pendidikan_terakhir" class="form-select">
                                @foreach(['SMA','D3','D4','S1','S2','S3'] as $p)
                                    <option value="{{ $p }}" {{ (old('pendidikan_terakhir', $user->pegawai?->pendidikan_terakhir) == $p) ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>Simpan Profil Admin</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview-img');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
