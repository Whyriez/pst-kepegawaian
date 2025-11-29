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
use Illuminate\Support\Str;

class PensiunController extends Controller
{
    /**
     * Halaman Pensiun Batas Usia Pensiun (BUP)
     */
    public function bup()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // 2. Ambil Syarat Dokumen (Pastikan slug 'pensiun-bup' ada di tabel jenis_layanans)
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pensiun-bup')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pensiun.batas_usia_pensiun', compact('pegawai', 'syarat'));
    }
    public function storeBup(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'nip_display' => 'required|exists:pegawais,nip',
            'tmt_pensiun' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // 2. Cari Data
            $pegawai = Pegawai::where('nip', $request->nip_display)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'pensiun-bup')->firstOrFail();

            // 3. Buat Tiket
            $tiket = 'PEN-BUP-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'      => $request->jabatan,
                    'pangkat'      => $request->pangkat,
                    'satuan_kerja' => $request->satuan_kerja,
                    'golongan'     => $request->golongan,
                    'tmt_pensiun'  => $request->tmt_pensiun,
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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Pensiun BUP berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman Pensiun Janda/Duda/Yatim
     */
    public function jandaDudaYatim()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'pensiun-jdy' ada di database
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pensiun-jdy')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pensiun.janda_duda_yatim', compact('pegawai', 'syarat'));
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
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'      => $request->jabatan_jdy,
                    'pangkat'      => $request->pangkat_jdy,
                    'satuan_kerja' => $request->satuan_kerja_jdy,
                    'golongan'     => $request->golongan_jdy,
                    'tmt_pensiun'  => $request->tmt_pensiun_jdy,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_jdy', $filename, 'public');

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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Pensiun JDY berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman Pensiun Atas Permintaan Sendiri (APS)
     */
    public function aps()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'pensiun-aps' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pensiun-aps')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pensiun.atas_permintaan_sendiri', compact('pegawai', 'syarat'));
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
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'      => $request->jabatan_aps,
                    'pangkat'      => $request->pangkat_aps,
                    'satuan_kerja' => $request->satuan_kerja_aps,
                    'golongan'     => $request->golongan_aps,
                    'tmt_pensiun'  => $request->tmt_pensiun_aps,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_aps', $filename, 'public');

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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Pensiun APS berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman Pensiun Meninggal
     */
    public function meninggal()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'pensiun-meninggal' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pensiun-meninggal')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pensiun.meninggal', compact('pegawai', 'syarat'));
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
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'tinggi', // Biasanya pensiun meninggal prioritas tinggi
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'      => $request->jabatan_meninggal,
                    'pangkat'      => $request->pangkat_meninggal,
                    'satuan_kerja' => $request->satuan_kerja_meninggal,
                    'golongan'     => $request->golongan_meninggal,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_meninggal', $filename, 'public');

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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Pensiun Meninggal berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman Pensiun Uzur
     */
    public function uzur()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'pensiun-uzur' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pensiun-uzur')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pensiun.uzur', compact('pegawai', 'syarat'));
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
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'tinggi', // Pensiun karena sakit biasanya prioritas tinggi
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'      => $request->jabatan_uzur,
                    'pangkat'      => $request->pangkat_uzur,
                    'satuan_kerja' => $request->satuan_kerja_uzur,
                    'golongan'     => $request->golongan_uzur,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pensiun_uzur', $filename, 'public');

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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Pensiun Uzur berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman Pensiun Hilang
     */
    public function hilang()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'pensiun-hilang' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pensiun-hilang')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pensiun.hilang', compact('pegawai', 'syarat'));
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
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'      => $request->jabatan_hilang,
                    'pangkat'      => $request->pangkat_hilang,
                    'satuan_kerja' => $request->satuan_kerja_hilang,
                    'golongan'     => $request->golongan_hilang,
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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Pensiun Hilang berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman Pensiun Tanpa Ahli Waris
     */
    public function tanpaAhliWaris()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'pensiun-taw' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pensiun-taw')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pensiun.tanpa_ahli_waris', compact('pegawai', 'syarat'));
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
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'      => $request->jabatan_taw,
                    'pangkat'      => $request->pangkat_taw,
                    'satuan_kerja' => $request->satuan_kerja_taw,
                    'golongan'     => $request->golongan_taw,
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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Pensiun Tanpa Ahli Waris berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }
}
