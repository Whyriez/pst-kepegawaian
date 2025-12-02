<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DokumenPengajuan;
use App\Models\JenisLayanan;
use App\Models\Pegawai;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JabatanFungsionalController extends Controller
{
    /**
     * Halaman Pengangkatan Jabatan Fungsional
     */
    public function pengangkatan()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil riwayat pengajuan (Slug: jf-pengangkatan)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'jf-pengangkatan');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.jabatan_fungsional.pengangkatan.index', compact('pengajuans'));
    }

    public function createPengangkatan()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'jf-pengangkatan' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'jf-pengangkatan')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.jabatan_fungsional.pengangkatan.create', compact('pegawai', 'syarat'));
    }

    public function storePengangkatan(Request $request)
    {
        $request->validate([
            'nip_display_jf_pengangkatan' => 'required|exists:pegawais,nip',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_jf_pengangkatan)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'jf-pengangkatan')->firstOrFail();

            $tiket = 'JF-AKT-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_jf_pengangkatan,
                    'pangkat' => $request->pangkat_jf_pengangkatan,
                    'unit_kerja' => $request->satuan_kerja_jf_pengangkatan,
                    'golongan_ruang' => $request->golongan_ruang_jf_pengangkatan,
                    // Data Usulan Baru
                    'usul_jabatan' => $request->usul_jabatan_jf_pengangkatan,
                    'usul_unit_kerja' => $request->usul_satuan_kerja_jf_pengangkatan,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/jf_pengangkatan', $filename, 'public');

                    DokumenPengajuan::create([
                        'pengajuan_id' => $pengajuan->id,
                        'syarat_dokumen_id' => $dokumen->id,
                        'nama_file_asli' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'tipe_file' => $file->getClientMimeType(),
                        'ukuran_file' => $file->getSize() / 1024,
                    ]);
                } elseif ($dokumen->is_required) {
                    throw new \Exception("Dokumen wajib: {$dokumen->nama_dokumen} belum diunggah.");
                }
            }

            DB::commit();
            return redirect()->route('jf.pengangkatan')->with('success', 'Pengajuan Pengangkatan JF berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function editPengangkatan(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('jf.pengangkatan')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // Ambil Data & Validasi Kepemilikan (Slug: jf-pengangkatan)
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'jf-pengangkatan');
            })
            ->firstOrFail();

        // Cek Status (Hanya boleh edit jika Pending atau Perbaikan)
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('jf.pengangkatan')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai, tidak dapat diedit.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.jabatan_fungsional.pengangkatan.edit', compact('pengajuan', 'syarat'));
    }

    public function updatePengangkatan(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'jabatan_jf_pengangkatan' => 'required',
            'pangkat_jf_pengangkatan' => 'required',
            'satuan_kerja_jf_pengangkatan' => 'required',
            'golongan_ruang_jf_pengangkatan' => 'required',
            'usul_jabatan_jf_pengangkatan' => 'required',
            'usul_satuan_kerja_jf_pengangkatan' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 2. Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;

            // Mapping input form ke key JSON
            $dataTambahan['jabatan']          = $request->jabatan_jf_pengangkatan;
            $dataTambahan['pangkat']          = $request->pangkat_jf_pengangkatan;
            $dataTambahan['unit_kerja']       = $request->satuan_kerja_jf_pengangkatan;
            $dataTambahan['golongan_ruang']   = $request->golongan_ruang_jf_pengangkatan;
            $dataTambahan['usul_jabatan']     = $request->usul_jabatan_jf_pengangkatan;
            $dataTambahan['usul_unit_kerja']  = $request->usul_satuan_kerja_jf_pengangkatan;

            $pengajuan->update([
                'data_tambahan'     => $dataTambahan,
                'status'            => 'pending', // Reset status agar diverifikasi ulang
                'catatan_admin'     => null,
                'tanggal_pengajuan' => now(),
            ]);

            // 3. Update Dokumen
            $layanan = $pengajuan->jenisLayanan;

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Hapus File Lama
                    $oldDoc = DokumenPengajuan::where('pengajuan_id', $pengajuan->id)
                        ->where('syarat_dokumen_id', $dokumen->id)
                        ->first();

                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->path_file)) {
                        Storage::disk('public')->delete($oldDoc->path_file);
                    }

                    // Upload File Baru
                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/jf_pengangkatan', $filename, 'public');

                    DokumenPengajuan::updateOrCreate(
                        [
                            'pengajuan_id' => $pengajuan->id,
                            'syarat_dokumen_id' => $dokumen->id
                        ],
                        [
                            'nama_file_asli' => $file->getClientOriginalName(),
                            'path_file'      => $path,
                            'tipe_file'      => $file->getClientMimeType(),
                            'ukuran_file'    => $file->getSize() / 1024,
                        ]
                    );
                }
            }

            DB::commit();
            return redirect()->route('jf.pengangkatan')->with('success', 'Perbaikan data Pengangkatan JF berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Pemberhentian Jabatan Fungsional
     */
    public function pemberhentian()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil riwayat pengajuan (Slug: jf-pemberhentian)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'jf-pemberhentian');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.jabatan_fungsional.pemberhentian.index', compact('pengajuans'));
    }

    public function createPemberhentian()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'jf-pemberhentian' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'jf-pemberhentian')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.jabatan_fungsional.pemberhentian.create', compact('pegawai', 'syarat'));
    }

    public function storePemberhentian(Request $request)
    {
        $request->validate([
            'nip_display_jf_pemberhentian' => 'required|exists:pegawais,nip',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_jf_pemberhentian)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'jf-pemberhentian')->firstOrFail();

            $tiket = 'JF-STP-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_jf_pemberhentian,
                    'pangkat' => $request->pangkat_jf_pemberhentian,
                    'unit_kerja' => $request->satuan_kerja_jf_pemberhentian,
                    'golongan_ruang' => $request->golongan_ruang_jf_pemberhentian,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/jf_pemberhentian', $filename, 'public');

                    DokumenPengajuan::create([
                        'pengajuan_id' => $pengajuan->id,
                        'syarat_dokumen_id' => $dokumen->id,
                        'nama_file_asli' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'tipe_file' => $file->getClientMimeType(),
                        'ukuran_file' => $file->getSize() / 1024,
                    ]);
                } elseif ($dokumen->is_required) {
                    throw new \Exception("Dokumen wajib: {$dokumen->nama_dokumen} belum diunggah.");
                }
            }

            DB::commit();
            return redirect()->route('jf.pemberhentian')->with('success', 'Pengajuan Pemberhentian JF berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function editPemberhentian(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('jf.pemberhentian')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // Ambil Data & Validasi
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'jf-pemberhentian');
            })
            ->firstOrFail();

        // Cek Status
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('jf.pemberhentian')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.jabatan_fungsional.pemberhentian.edit', compact('pengajuan', 'syarat'));
    }

    public function updatePemberhentian(Request $request, $id)
    {
        // Validasi Input
        $request->validate([
            'jabatan_jf_pemberhentian' => 'required',
            'satuan_kerja_jf_pemberhentian' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;

            // Map input ke key JSON
            $dataTambahan['jabatan'] = $request->jabatan_jf_pemberhentian;
            $dataTambahan['pangkat'] = $request->pangkat_jf_pemberhentian;
            $dataTambahan['unit_kerja'] = $request->satuan_kerja_jf_pemberhentian;
            $dataTambahan['golongan_ruang'] = $request->golongan_ruang_jf_pemberhentian;

            $pengajuan->update([
                'data_tambahan' => $dataTambahan,
                'status' => 'pending', // Reset status
                'catatan_admin' => null,
                'tanggal_pengajuan' => now(),
            ]);

            // 2. Update Dokumen
            $layanan = $pengajuan->jenisLayanan;

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Hapus File Lama
                    $oldDoc = DokumenPengajuan::where('pengajuan_id', $pengajuan->id)
                        ->where('syarat_dokumen_id', $dokumen->id)
                        ->first();

                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->path_file)) {
                        Storage::disk('public')->delete($oldDoc->path_file);
                    }

                    // Upload File Baru
                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/jf_pemberhentian', $filename, 'public');

                    DokumenPengajuan::updateOrCreate(
                        [
                            'pengajuan_id' => $pengajuan->id,
                            'syarat_dokumen_id' => $dokumen->id
                        ],
                        [
                            'nama_file_asli' => $file->getClientOriginalName(),
                            'path_file' => $path,
                            'tipe_file' => $file->getClientMimeType(),
                            'ukuran_file' => $file->getSize() / 1024,
                        ]
                    );
                }
            }

            DB::commit();
            return redirect()->route('jf.pemberhentian')->with('success', 'Perbaikan data Pemberhentian JF berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Naik Jenjang Jabatan Fungsional
     */
    public function naikJenjang()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil riwayat pengajuan (Slug: jf-naik-jenjang)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'jf-naik-jenjang');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.jabatan_fungsional.naik_jenjang.index', compact('pengajuans'));
    }

    public function createNaikJenjang()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'jf-naik-jenjang' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'jf-naik-jenjang')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.jabatan_fungsional.naik_jenjang.create', compact('pegawai', 'syarat'));
    }

    public function storeNaikJenjang(Request $request)
    {
        $request->validate([
            'nip_display_jf_naik_jenjang' => 'required|exists:pegawais,nip',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_jf_naik_jenjang)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'jf-naik-jenjang')->firstOrFail();

            $tiket = 'JF-JEN-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_jf_naik_jenjang,
                    'pangkat' => $request->pangkat_jf_naik_jenjang,
                    'unit_kerja' => $request->satuan_kerja_jf_naik_jenjang,
                    'golongan_ruang' => $request->golongan_ruang_jf_naik_jenjang,
                    // Data Usulan Baru
                    'usul_jabatan' => $request->usul_jabatan_jf_naik_jenjang,
                    'usul_unit_kerja' => $request->usul_satuan_kerja_jf_naik_jenjang,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/jf_naik_jenjang', $filename, 'public');

                    DokumenPengajuan::create([
                        'pengajuan_id' => $pengajuan->id,
                        'syarat_dokumen_id' => $dokumen->id,
                        'nama_file_asli' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'tipe_file' => $file->getClientMimeType(),
                        'ukuran_file' => $file->getSize() / 1024,
                    ]);
                } elseif ($dokumen->is_required) {
                    throw new \Exception("Dokumen wajib: {$dokumen->nama_dokumen} belum diunggah.");
                }
            }

            DB::commit();
            return redirect()->route('jf.naik_jenjang')->with('success', 'Pengajuan Kenaikan Jenjang JF berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function editNaikJenjang(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('jf.naik_jenjang')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // Ambil Data & Validasi
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'jf-naik-jenjang');
            })
            ->firstOrFail();

        // Cek Status
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('jf.naik_jenjang')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.jabatan_fungsional.naik_jenjang.edit', compact('pengajuan', 'syarat'));
    }

    public function updateNaikJenjang(Request $request, $id)
    {
        // Validasi Input
        $request->validate([
            'jabatan_jf_naik_jenjang' => 'required',
            'usul_jabatan_jf_naik_jenjang' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;

            // Map input ke key JSON
            $dataTambahan['jabatan'] = $request->jabatan_jf_naik_jenjang;
            $dataTambahan['pangkat'] = $request->pangkat_jf_naik_jenjang;
            $dataTambahan['unit_kerja'] = $request->satuan_kerja_jf_naik_jenjang;
            $dataTambahan['golongan_ruang'] = $request->golongan_ruang_jf_naik_jenjang;
            $dataTambahan['usul_jabatan'] = $request->usul_jabatan_jf_naik_jenjang;
            $dataTambahan['usul_unit_kerja'] = $request->usul_satuan_kerja_jf_naik_jenjang;

            $pengajuan->update([
                'data_tambahan' => $dataTambahan,
                'status' => 'pending', // Reset status
                'catatan_admin' => null,
                'tanggal_pengajuan' => now(),
            ]);

            // 2. Update Dokumen
            $layanan = $pengajuan->jenisLayanan;

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Hapus File Lama
                    $oldDoc = DokumenPengajuan::where('pengajuan_id', $pengajuan->id)
                        ->where('syarat_dokumen_id', $dokumen->id)
                        ->first();

                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->path_file)) {
                        Storage::disk('public')->delete($oldDoc->path_file);
                    }

                    // Upload File Baru
                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/jf_naik_jenjang', $filename, 'public');

                    DokumenPengajuan::updateOrCreate(
                        [
                            'pengajuan_id' => $pengajuan->id,
                            'syarat_dokumen_id' => $dokumen->id
                        ],
                        [
                            'nama_file_asli' => $file->getClientOriginalName(),
                            'path_file' => $path,
                            'tipe_file' => $file->getClientMimeType(),
                            'ukuran_file' => $file->getSize() / 1024,
                        ]
                    );
                }
            }

            DB::commit();
            return redirect()->route('jf.naik_jenjang')->with('success', 'Perbaikan data Kenaikan Jenjang JF berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}
