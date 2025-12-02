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

class PensiunController extends Controller
{
    /**
     * Halaman Pensiun Batas Usia Pensiun (BUP)
     */
    public function bup()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil data khusus layanan Pensiun BUP
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'pensiun-bup');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.pensiun.batas_usia_pensiun.index', compact('pengajuans'));
    }

    public function createBup()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // 2. Ambil Syarat Dokumen (Pastikan slug 'pensiun-bup' ada di tabel jenis_layanans)
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pensiun-bup')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pensiun.batas_usia_pensiun.create', compact('pegawai', 'syarat'));
    }

    public function storeBup(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'nip_display_pensiun_bup' => 'required|exists:pegawais,nip',
            'tmt_pensiun_bup' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // 2. Cari Data
            $pegawai = Pegawai::where('nip', $request->nip_display_pensiun_bup)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'pensiun-bup')->firstOrFail();

            // 3. Buat Tiket
            $tiket = 'PEN-BUP-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_pensiun_bup,
                    'pangkat' => $request->pangkat_pensiun_bup,
                    'satuan_kerja' => $request->satuan_kerja_pensiun_bup,
                    'golongan' => $request->golongan_ruang_pensiun_bup,
                    'tmt_pensiun' => $request->tmt_pensiun_bup,
                ]
            ]);

            // 4. Simpan Dokumen Dinamis
            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_bup', $filename, 'public');

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
            return redirect()->route('pensiun.bup')->with('success', 'Pengajuan Pensiun BUP berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function editBup(Request $request)
    {
        // 1. Ambil ID dari query param (?id=...)
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('bup')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // 2. Ambil Data Pengajuan
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id) // Security check
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'pensiun-bup');
            })
            ->firstOrFail();

        // 3. Cek Status (Hanya boleh edit jika pending/perbaikan)
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('bup')
                ->with('error', 'Pengajuan sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.pensiun.batas_usia_pensiun.edit', compact('pengajuan', 'syarat'));
    }

    public function updateBup(Request $request, $id)
    {
        $request->validate([
            'tmt_pensiun_bup' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;

            // Map input form ke key JSON
            $dataTambahan['jabatan'] = $request->jabatan_pensiun_bup;
            $dataTambahan['pangkat'] = $request->pangkat_pensiun_bup;
            $dataTambahan['satuan_kerja'] = $request->satuan_kerja_pensiun_bup;
            $dataTambahan['golongan'] = $request->golongan_ruang_pensiun_bup;
            $dataTambahan['tmt_pensiun'] = $request->tmt_pensiun_bup;

            $pengajuan->update([
                'data_tambahan' => $dataTambahan,
                'status' => 'pending', // Reset ke pending
                'catatan_admin' => null,
                'tanggal_pengajuan' => now(),
            ]);

            // 2. Update Dokumen
            $layanan = $pengajuan->jenisLayanan;

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Hapus file lama (Opsional)
                    $oldDoc = DokumenPengajuan::where('pengajuan_id', $pengajuan->id)
                        ->where('syarat_dokumen_id', $dokumen->id)
                        ->first();

                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->path_file)) {
                        Storage::disk('public')->delete($oldDoc->path_file);
                    }

                    // Upload Baru
                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_bup', $filename, 'public');

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
            return redirect()->route('pensiun.bup')->with('success', 'Perbaikan data Pensiun BUP berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Pensiun Janda/Duda/Yatim
     */
    public function jandaDudaYatim()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil data pengajuan khusus Pensiun JDY
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'pensiun-jdy');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.pensiun.janda_duda_yatim.index', compact('pengajuans'));
    }

    public function createJandaDudaYatim()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'pensiun-jdy' ada di database
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pensiun-jdy')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pensiun.janda_duda_yatim.create', compact('pegawai', 'syarat'));
    }

    public function storeJandaDudaYatim(Request $request)
    {
        $request->validate([
            'nip_display_jdy' => 'required|exists:pegawais,nip',
            'tmt_pensiun_jdy' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_jdy)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'pensiun-jdy')->firstOrFail();

            $tiket = 'PEN-JDY-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_jdy,
                    'pangkat' => $request->pangkat_jdy,
                    'satuan_kerja' => $request->satuan_kerja_jdy,
                    'golongan' => $request->golongan_jdy,
                    'tmt_pensiun' => $request->tmt_pensiun_jdy,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_jdy', $filename, 'public');

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
            return redirect()->route('pensiun.janda_duda_yatim')->with('success', 'Pengajuan Pensiun JDY berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function editJandaDudaYatim(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('pensiun.janda_duda_yatim')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // Ambil Data Pengajuan
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'pensiun-jdy');
            })
            ->firstOrFail();

        // Validasi Status
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('pensiun.janda_duda_yatim')
                ->with('error', 'Pengajuan sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.pensiun.janda_duda_yatim.edit', compact('pengajuan', 'syarat'));
    }

    public function updateJandaDudaYatim(Request $request, $id)
    {
        $request->validate([
            'tmt_pensiun_jdy' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;
            $dataTambahan['jabatan'] = $request->jabatan_jdy;
            $dataTambahan['pangkat'] = $request->pangkat_jdy;
            $dataTambahan['satuan_kerja'] = $request->satuan_kerja_jdy;
            $dataTambahan['golongan'] = $request->golongan_jdy;
            $dataTambahan['tmt_pensiun'] = $request->tmt_pensiun_jdy;

            $pengajuan->update([
                'data_tambahan' => $dataTambahan,
                'status' => 'pending',
                'catatan_admin' => null,
                'tanggal_pengajuan' => now(),
            ]);

            // Update Dokumen
            $layanan = $pengajuan->jenisLayanan;

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Hapus file lama (Opsional)
                    $oldDoc = DokumenPengajuan::where('pengajuan_id', $pengajuan->id)
                        ->where('syarat_dokumen_id', $dokumen->id)
                        ->first();

                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->path_file)) {
                        Storage::disk('public')->delete($oldDoc->path_file);
                    }

                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_jdy', $filename, 'public');

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
            return redirect()->route('pensiun.janda_duda_yatim')->with('success', 'Perbaikan data Pensiun JDY berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Pensiun Atas Permintaan Sendiri (APS)
     */
    public function aps()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // FIXED: Ambil data pengajuan (History), bukan ambil syarat dokumen
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'pensiun-aps'); // Filter khusus APS
            })
            ->latest()
            ->paginate(10);

        // Arahkan ke file index.blade.php yang baru kita buat
        return view('pages.user.pensiun.atas_permintaan_sendiri.index', compact('pengajuans'));
    }

    public function createAps()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'pensiun-aps' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pensiun-aps')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pensiun.atas_permintaan_sendiri.create', compact('pegawai', 'syarat'));
    }

    public function storeAps(Request $request)
    {
        $request->validate([
            'nip_display_aps' => 'required|exists:pegawais,nip',
            'tmt_pensiun_aps' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_aps)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'pensiun-aps')->firstOrFail();

            $tiket = 'PEN-APS-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_aps,
                    'pangkat' => $request->pangkat_aps,
                    'satuan_kerja' => $request->satuan_kerja_aps,
                    'golongan' => $request->golongan_aps,
                    'tmt_pensiun' => $request->tmt_pensiun_aps,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_aps', $filename, 'public');

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
            return redirect()->route('pensiun.aps')->with('success', 'Pengajuan Pensiun APS berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function editAps(Request $request)
    {
        // 1. Ambil ID dari Query String
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('pensiun.aps')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // 2. Ambil Data Pengajuan (Security: Milik User & Tipe APS)
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'pensiun-aps'); // Filter Slug Layanan
            })
            ->firstOrFail();

        // 3. Cek Status (Hanya boleh edit jika Pending atau Perbaikan)
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('pensiun.aps')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai, tidak dapat diedit.');
        }

        // 4. Ambil Syarat Dokumen untuk ditampilkan di view
        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.pensiun.atas_permintaan_sendiri.edit', compact('pengajuan', 'syarat'));
    }

    public function updateAps(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'tmt_pensiun_aps' => 'required|date',
            'jabatan_aps' => 'required',
            'pangkat_aps' => 'required',
            'satuan_kerja_aps' => 'required',
            'golongan_aps' => 'required',
        ]);

        try {
            DB::beginTransaction();

            // 2. Cari Pengajuan
            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 3. Update Data JSON (Data Tambahan)
            $dataTambahan = $pengajuan->data_tambahan;

            // Map input form (suffix _aps) ke key JSON
            $dataTambahan['jabatan'] = $request->jabatan_aps;
            $dataTambahan['pangkat'] = $request->pangkat_aps;
            $dataTambahan['satuan_kerja'] = $request->satuan_kerja_aps;
            $dataTambahan['golongan'] = $request->golongan_aps;
            $dataTambahan['tmt_pensiun'] = $request->tmt_pensiun_aps;

            $pengajuan->update([
                'data_tambahan' => $dataTambahan,
                'status' => 'pending', // Reset status agar diverifikasi ulang admin
                'catatan_admin' => null,      // Hapus catatan revisi sebelumnya
                'tanggal_pengajuan' => now(),     // Update waktu pengajuan
            ]);

            // 4. Update Dokumen (Smart Replacement)
            $layanan = $pengajuan->jenisLayanan;

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Hapus file lama fisik (Optional, agar hemat storage)
                    $oldDoc = DokumenPengajuan::where('pengajuan_id', $pengajuan->id)
                        ->where('syarat_dokumen_id', $dokumen->id)
                        ->first();

                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->path_file)) {
                        Storage::disk('public')->delete($oldDoc->path_file);
                    }

                    // Simpan File Baru
                    // Format: TIKET_NAMA-DOKUMEN_TIMESTAMP.ext
                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_aps', $filename, 'public');

                    // Update atau Create record di database
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
            return redirect()->route('pensiun.aps')->with('success', 'Perbaikan data Pensiun APS berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Pensiun Meninggal
     */
    public function meninggal()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil riwayat pengajuan (Slug: pensiun-meninggal)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'pensiun-meninggal');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.pensiun.meninggal.index', compact('pengajuans'));
    }

    public function createMeninggal()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'pensiun-meninggal' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pensiun-meninggal')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pensiun.meninggal.create', compact('pegawai', 'syarat'));
    }

    public function storeMeninggal(Request $request)
    {
        $request->validate([
            'nip_display_meninggal' => 'required|exists:pegawais,nip',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_meninggal)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'pensiun-meninggal')->firstOrFail();

            $tiket = 'PEN-MNG-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'tinggi', // Biasanya pensiun meninggal prioritas tinggi
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_meninggal,
                    'pangkat' => $request->pangkat_meninggal,
                    'satuan_kerja' => $request->satuan_kerja_meninggal,
                    'golongan' => $request->golongan_meninggal,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_meninggal', $filename, 'public');

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
            return redirect()->route('pensiun.meninggal')->with('success', 'Pengajuan Pensiun Meninggal berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function editMeninggal(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('pensiun.meninggal')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // Ambil Data & Cek Validasi Kepemilikan
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'pensiun-meninggal');
            })
            ->firstOrFail();

        // Cek Status
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('pensiun.meninggal')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.pensiun.meninggal.edit', compact('pengajuan', 'syarat'));
    }

    /**
     * Proses Update Pensiun Meninggal
     */
    public function updateMeninggal(Request $request, $id)
    {
        // Validasi input form edit
        $request->validate([
            'jabatan_meninggal' => 'required',
            'pangkat_meninggal' => 'required',
            'satuan_kerja_meninggal' => 'required',
            'golongan_meninggal' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;

            // Map input ke key JSON
            $dataTambahan['jabatan'] = $request->jabatan_meninggal;
            $dataTambahan['pangkat'] = $request->pangkat_meninggal;
            $dataTambahan['satuan_kerja'] = $request->satuan_kerja_meninggal;
            $dataTambahan['golongan'] = $request->golongan_meninggal;

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
                    $path = $file->storeAs('documents/pensiun_meninggal', $filename, 'public');

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
            return redirect()->route('pensiun.meninggal')->with('success', 'Perbaikan data Pensiun Meninggal berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Pensiun Uzur
     */
    public function uzur()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil riwayat pengajuan (Slug: pensiun-uzur)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'pensiun-uzur');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.pensiun.uzur.index', compact('pengajuans'));
    }

    public function createUzur()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'pensiun-uzur' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pensiun-uzur')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pensiun.uzur.create', compact('pegawai', 'syarat'));
    }

    public function storeUzur(Request $request)
    {
        $request->validate([
            'nip_display_uzur' => 'required|exists:pegawais,nip',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_uzur)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'pensiun-uzur')->firstOrFail();

            $tiket = 'PEN-UZR-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'tinggi', // Pensiun karena sakit biasanya prioritas tinggi
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_uzur,
                    'pangkat' => $request->pangkat_uzur,
                    'satuan_kerja' => $request->satuan_kerja_uzur,
                    'golongan' => $request->golongan_uzur,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_uzur', $filename, 'public');

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
            return redirect()->route('pensiun.uzur')->with('success', 'Pengajuan Pensiun Uzur berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function editUzur(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('pensiun.uzur')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // Ambil Data & Validasi
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'pensiun-uzur');
            })
            ->firstOrFail();

        // Cek Status (Pending/Perbaikan only)
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('pensiun.uzur')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.pensiun.uzur.edit', compact('pengajuan', 'syarat'));
    }

    public function updateUzur(Request $request, $id)
    {
        // Validasi input form edit
        $request->validate([
            'jabatan_uzur' => 'required',
            'pangkat_uzur' => 'required',
            'satuan_kerja_uzur' => 'required',
            'golongan_uzur' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;

            // Map input ke key JSON
            $dataTambahan['jabatan'] = $request->jabatan_uzur;
            $dataTambahan['pangkat'] = $request->pangkat_uzur;
            $dataTambahan['satuan_kerja'] = $request->satuan_kerja_uzur;
            $dataTambahan['golongan'] = $request->golongan_uzur;

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
                    $path = $file->storeAs('documents/pensiun_uzur', $filename, 'public');

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
            return redirect()->route('pensiun.uzur')->with('success', 'Perbaikan data Pensiun Uzur berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Pensiun Hilang
     */
    public function hilang()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil riwayat pengajuan (Slug: pensiun-hilang)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'pensiun-hilang');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.pensiun.hilang.index', compact('pengajuans'));
    }

    public function createHilang()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'pensiun-hilang' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pensiun-hilang')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pensiun.hilang.create', compact('pegawai', 'syarat'));
    }

    public function storeHilang(Request $request)
    {
        $request->validate([
            'nip_display_hilang' => 'required|exists:pegawais,nip',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_hilang)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'pensiun-hilang')->firstOrFail();

            $tiket = 'PEN-HIL-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_hilang,
                    'pangkat' => $request->pangkat_hilang,
                    'satuan_kerja' => $request->satuan_kerja_hilang,
                    'golongan' => $request->golongan_hilang,
                    // Pensiun hilang biasanya tidak ada TMT spesifik di awal, atau bisa dikosongkan
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_hilang', $filename, 'public');

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
            return redirect()->route('pensiun.hilang')->with('success', 'Pengajuan Pensiun Hilang berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function editHilang(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('pensiun.hilang')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'pensiun-hilang');
            })
            ->firstOrFail();

        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('pensiun.hilang')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.pensiun.hilang.edit', compact('pengajuan', 'syarat'));
    }

    /**
     * Proses Update Pensiun Hilang
     */
    public function updateHilang(Request $request, $id)
    {
        $request->validate([
            'jabatan_hilang' => 'required',
            'pangkat_hilang' => 'required',
            'satuan_kerja_hilang' => 'required',
            'golongan_hilang' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;
            $dataTambahan['jabatan'] = $request->jabatan_hilang;
            $dataTambahan['pangkat'] = $request->pangkat_hilang;
            $dataTambahan['satuan_kerja'] = $request->satuan_kerja_hilang;
            $dataTambahan['golongan'] = $request->golongan_hilang;

            $pengajuan->update([
                'data_tambahan' => $dataTambahan,
                'status' => 'pending',
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

                    // Upload Baru
                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_hilang', $filename, 'public');

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
            return redirect()->route('pensiun.hilang')->with('success', 'Perbaikan data Pensiun Hilang berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Pensiun Tanpa Ahli Waris
     */
    public function tanpaAhliWaris()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil riwayat pengajuan (Slug: pensiun-taw)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'pensiun-taw');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.pensiun.tanpa_ahli_waris.index', compact('pengajuans'));
    }

    public function createTanpaAhliWaris()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'pensiun-taw' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pensiun-taw')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pensiun.tanpa_ahli_waris.create', compact('pegawai', 'syarat'));
    }

    public function storeTanpaAhliWaris(Request $request)
    {
        $request->validate([
            'nip_display_taw' => 'required|exists:pegawais,nip',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_taw)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'pensiun-taw')->firstOrFail();

            $tiket = 'PEN-TAW-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_taw,
                    'pangkat' => $request->pangkat_taw,
                    'satuan_kerja' => $request->satuan_kerja_taw,
                    'golongan' => $request->golongan_taw,
                    // TAW biasanya tidak ada TMT spesifik di awal, bisa dikosongkan
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_taw', $filename, 'public');

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
            return redirect()->route('pensiun.taw')->with('success', 'Pengajuan Pensiun Tanpa Ahli Waris berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function editTanpaAhliWaris(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('pensiun.taw')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'pensiun-taw');
            })
            ->firstOrFail();

        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('pensiun.taw')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.pensiun.tanpa_ahli_waris.edit', compact('pengajuan', 'syarat'));
    }

    public function updateTanpaAhliWaris(Request $request, $id)
    {
        $request->validate([
            'jabatan_taw' => 'required',
            'pangkat_taw' => 'required',
            'satuan_kerja_taw' => 'required',
            'golongan_taw' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;
            $dataTambahan['jabatan'] = $request->jabatan_taw;
            $dataTambahan['pangkat'] = $request->pangkat_taw;
            $dataTambahan['satuan_kerja'] = $request->satuan_kerja_taw;
            $dataTambahan['golongan'] = $request->golongan_taw;

            $pengajuan->update([
                'data_tambahan' => $dataTambahan,
                'status' => 'pending',
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

                    // Upload Baru
                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_taw', $filename, 'public');

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
            return redirect()->route('pensiun.taw')->with('success', 'Perbaikan data Pensiun Tanpa Ahli Waris berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}
