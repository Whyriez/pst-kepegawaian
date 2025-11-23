<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DokumenPengajuan;
use App\Models\JenisLayanan;
use App\Models\Pegawai;
use App\Models\Pengajuan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KenaikanPangkatController extends Controller
{
    /**
     * Halaman KP Fungsional
     * View: pages.user.kenaikan_pangkat.fungsional
     */
    public function fungsional()
    {
        // 1. Ambil data pegawai dari user yang login (untuk autofill)
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // 2. Ambil syarat dokumen dari database (agar dinamis)
        // Pastikan Seeder sudah dijalankan dan slug 'kp-fungsional' ada
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'kp-fungsional')->first();

        // Jika layanan belum ada di DB, kirim collection kosong untuk menghindari error
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.kenaikan_pangkat.fungsional', compact('pegawai', 'syarat'));
    }

    public function storeFungsional(Request $request)
    {
        // 1. Validasi Data Utama
        $request->validate([
            'nip_display_kp_fungsional' => 'required|exists:pegawais,nip',
            'periode_kenaikan_pangkat_kp_fungsional' => 'required',
        ], [
            'nip_display_kp_fungsional.exists' => 'NIP tidak terdaftar dalam database pegawai.',
            'periode_kenaikan_pangkat_kp_fungsional.required' => 'Periode kenaikan pangkat wajib dipilih.'
        ]);

        try {
            DB::beginTransaction();

            // 2. Cari Data Pendukung
            $pegawai = Pegawai::where('nip', $request->nip_display_kp_fungsional)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'kp-fungsional')->firstOrFail();

            // 3. Simpan Header Pengajuan
            // Generate Nomor Tiket: KP-FUN-YYYYMMDD-XXXX
            $tiket = 'KP-FUN-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',   // Default status
                'prioritas'         => 'sedang',    // Default prioritas
                'tanggal_pengajuan' => now(),
                // Simpan data form yang tidak ada kolom khususnya ke dalam JSON
                'data_tambahan'     => [
                    'jabatan_saat_ini' => $request->jabatan_kp_fungsional,
                    'pangkat_saat_ini' => $request->pangkat_kp_fungsional,
                    'unit_kerja'       => $request->unit_kerja_kp_fungsional,
                    'golongan_ruang'   => $request->golongan_ruang_kp_fungsional,
                    'periode'          => $request->periode_kenaikan_pangkat_kp_fungsional,
                ]
            ]);

            // 4. Loop Syarat Dokumen & Simpan File
            // Kita meloop syarat dari database, lalu mencari input file yang sesuai (file_{id})
            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id; // Contoh: file_1, file_2

                // Cek apakah user mengupload file untuk syarat ini
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Format nama file: TIKET_NAMA-DOKUMEN.ext
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();

                    // Simpan fisik file ke storage
                    $path = $file->storeAs('documents/kp_fungsional', $filename, 'public');

                    // Simpan record ke database
                    DokumenPengajuan::create([
                        'pengajuan_id'      => $pengajuan->id,
                        'syarat_dokumen_id' => $dokumen->id,
                        'nama_file_asli'    => $file->getClientOriginalName(),
                        'path_file'         => $path,
                        'tipe_file'         => $file->getClientMimeType(),
                        'ukuran_file'       => $file->getSize() / 1024, // Konversi ke KB
                    ]);
                } else {
                    // Jika dokumen wajib tapi tidak ada file (Backup validation)
                    if ($dokumen->is_required) {
                        throw new \Exception("Dokumen wajib: {$dokumen->nama_dokumen} belum diunggah.");
                    }
                }
            }

            DB::commit();

            return redirect()->route('kp.fungsional')->with('success', 'Pengajuan berhasil dikirim! Nomor Tiket Anda: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman KP Penyesuaian Ijazah
     * View: pages.user.kenaikan_pangkat.penyesuaian_ijazah
     */
    public function penyesuaianIjazah()
    {
        // 1. Ambil data pegawai (Autofill)
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // 2. Ambil syarat dokumen dinamis berdasarkan slug layanan
        // PASTIKAN DI DATABASE TABEL jenis_layanans SUDAH ADA SLUG: 'kp-penyesuaian-ijazah'
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'kp-pi')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.kenaikan_pangkat.penyesuaian_ijazah', compact('pegawai', 'syarat'));
    }
    public function storePenyesuaianIjazah(Request $request)
    {
        // 1. Validasi Dasar
        $request->validate([
            'nip_display_kp_penyesuaian_ijazah' => 'required|exists:pegawais,nip',
            'periode_kenaikan_pangkat_kp_penyesuaian_ijazah' => 'required',
        ]);

        try {
            DB::beginTransaction();

            // 2. Ambil Data Pegawai & Layanan
            $pegawai = Pegawai::where('nip', $request->nip_display_kp_penyesuaian_ijazah)->firstOrFail();
            // Pastikan di tabel 'jenis_layanans' ada slug ini!
            $layanan = JenisLayanan::where('slug', 'kp-pi')->firstOrFail();

            // 3. Buat Header Pengajuan
            $tiket = 'KP-PI-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'        => $request->jabatan_kp_penyesuaian_ijazah,
                    'pangkat'        => $request->pangkat_kp_penyesuaian_ijazah,
                    'unit_kerja'     => $request->unit_kerja_kp_penyesuaian_ijazah,
                    'golongan_ruang' => $request->golongan_ruang_kp_penyesuaian_ijazah,
                    'periode'        => $request->periode_kenaikan_pangkat_kp_penyesuaian_ijazah,
                ]
            ]);

            // 4. Simpan Dokumen (Looping Dinamis)
            // Controller mencari input dengan nama 'file_1', 'file_2', dst sesuai ID dokumen di database
            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/kp_penyesuaian_ijazah', $filename, 'public');

                    DokumenPengajuan::create([
                        'pengajuan_id'      => $pengajuan->id,
                        'syarat_dokumen_id' => $dokumen->id,
                        'nama_file_asli'    => $file->getClientOriginalName(),
                        'path_file'         => $path,
                        'tipe_file'         => $file->getClientMimeType(),
                        'ukuran_file'       => $file->getSize() / 1024,
                    ]);
                } elseif ($dokumen->is_required) {
                    throw new \Exception("Dokumen wajib: {$dokumen->nama_dokumen} belum diunggah.");
                }
            }

            DB::commit();
            return redirect()->route('kp.penyesuaian_ijazah')->with('success', 'Pengajuan Penyesuaian Ijazah berhasil dikirim! No Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman KP Reguler
     * View: pages.user.kenaikan_pangkat.reguler
     */
    public function reguler()
    {
        // 1. Get Logged-in User Data
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // 2. Get Dynamic Documents (Make sure slug 'kp-reguler' exists in 'jenis_layanans' table)
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'kp-reguler')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.kenaikan_pangkat.reguler', compact('pegawai', 'syarat'));
    }

    public function storeReguler(Request $request)
    {
        // 1. Validation
        $request->validate([
            'nip_display_kp_reguler' => 'required|exists:pegawais,nip',
            'periode_kenaikan_pangkat_kp_reguler' => 'required',
        ]);

        try {
            DB::beginTransaction();

            // 2. Find Data
            $pegawai = Pegawai::where('nip', $request->nip_display_kp_reguler)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'kp-reguler')->firstOrFail();

            // 3. Create Ticket & Header
            $tiket = 'KP-REG-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'        => $request->jabatan_kp_reguler,
                    'pangkat'        => $request->pangkat_kp_reguler,
                    'unit_kerja'     => $request->unit_kerja_kp_reguler,
                    'golongan_ruang' => $request->golongan_ruang_kp_reguler,
                    'periode'        => $request->periode_kenaikan_pangkat_kp_reguler,
                ]
            ]);

            // 4. Save Documents Dynamically
            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/kp_reguler', $filename, 'public');

                    DokumenPengajuan::create([
                        'pengajuan_id'      => $pengajuan->id,
                        'syarat_dokumen_id' => $dokumen->id,
                        'nama_file_asli'    => $file->getClientOriginalName(),
                        'path_file'         => $path,
                        'tipe_file'         => $file->getClientMimeType(),
                        'ukuran_file'       => $file->getSize() / 1024,
                    ]);
                } elseif ($dokumen->is_required) {
                    throw new \Exception("Dokumen wajib: {$dokumen->nama_dokumen} belum diunggah.");
                }
            }

            DB::commit();
            return redirect()->route('kp.reguler')->with('success', 'Pengajuan KP Reguler berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman KP Struktural
     * View: pages.user.kenaikan_pangkat.struktural
     */
    public function struktural()
    {
        // 1. Ambil data pegawai login
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // 2. Ambil Dokumen (Pastikan slug 'kp-struktural' ada di tabel jenis_layanans)
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'kp-struktural')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.kenaikan_pangkat.struktural', compact('pegawai', 'syarat'));
    }

    public function storeStruktural(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'nip_display_kp_struktural' => 'required|exists:pegawais,nip',
            'periode_kenaikan_pangkat_kp_struktural' => 'required',
        ]);

        try {
            DB::beginTransaction();

            // 2. Cari Data
            $pegawai = Pegawai::where('nip', $request->nip_display_kp_struktural)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'kp-struktural')->firstOrFail();

            // 3. Buat Header (Tiket: KP-STR-...)
            $tiket = 'KP-STR-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'        => $request->jabatan_kp_struktural,
                    'pangkat'        => $request->pangkat_kp_struktural,
                    'unit_kerja'     => $request->unit_kerja_kp_struktural,
                    'golongan_ruang' => $request->golongan_ruang_kp_struktural,
                    'periode'        => $request->periode_kenaikan_pangkat_kp_struktural,
                ]
            ]);

            // 4. Simpan Dokumen Dinamis
            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/kp_struktural', $filename, 'public');

                    DokumenPengajuan::create([
                        'pengajuan_id'      => $pengajuan->id,
                        'syarat_dokumen_id' => $dokumen->id,
                        'nama_file_asli'    => $file->getClientOriginalName(),
                        'path_file'         => $path,
                        'tipe_file'         => $file->getClientMimeType(),
                        'ukuran_file'       => $file->getSize() / 1024,
                    ]);
                } elseif ($dokumen->is_required) {
                    throw new \Exception("Dokumen wajib: {$dokumen->nama_dokumen} belum diunggah.");
                }
            }

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Pengajuan KP Struktural berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function cekNip($nip): JsonResponse
    {
        // Cari pegawai berdasarkan NIP dan relasinya
        $pegawai = Pegawai::with('satuanKerja')
            ->where('nip', $nip)
            ->first();

        if ($pegawai) {
            return response()->json([
                'success' => true,
                'data' => [
                    'nama' => $pegawai->nama_lengkap,
                    'nip' => $pegawai->nip,
                    'jabatan' => $pegawai->jabatan,
                    'pangkat' => $pegawai->pangkat,
                    'unit_kerja' => $pegawai->satuanKerja->nama_satuan_kerja ?? '-',
                    'golongan_ruang' => $pegawai->golongan_ruang
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data pegawai dengan NIP tersebut tidak ditemukan.'
        ], 404);
    }
}
