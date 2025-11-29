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

class JabatanFungsionalController extends Controller
{
    /**
     * Halaman Pengangkatan Jabatan Fungsional
     */
    public function pengangkatan()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'jf-pengangkatan' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'jf-pengangkatan')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.jabatan_fungsional.pengangkatan', compact('pegawai', 'syarat'));
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
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'          => $request->jabatan_jf_pengangkatan,
                    'pangkat'          => $request->pangkat_jf_pengangkatan,
                    'unit_kerja'       => $request->satuan_kerja_jf_pengangkatan,
                    'golongan_ruang'   => $request->golongan_ruang_jf_pengangkatan,
                    // Data Usulan Baru
                    'usul_jabatan'     => $request->usul_jabatan_jf_pengangkatan,
                    'usul_unit_kerja'  => $request->usul_satuan_kerja_jf_pengangkatan,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/jf_pengangkatan', $filename, 'public');

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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Pengangkatan JF berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman Pemberhentian Jabatan Fungsional
     */
    public function pemberhentian()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'jf-pemberhentian' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'jf-pemberhentian')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.jabatan_fungsional.pemberhentian', compact('pegawai', 'syarat'));
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
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'        => $request->jabatan_jf_pemberhentian,
                    'pangkat'        => $request->pangkat_jf_pemberhentian,
                    'unit_kerja'     => $request->satuan_kerja_jf_pemberhentian,
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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Pemberhentian JF berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman Naik Jenjang Jabatan Fungsional
     */
    public function naikJenjang()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'jf-naik-jenjang' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'jf-naik-jenjang')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.jabatan_fungsional.naik_jenjang', compact('pegawai', 'syarat'));
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
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'           => $request->jabatan_jf_naik_jenjang,
                    'pangkat'           => $request->pangkat_jf_naik_jenjang,
                    'unit_kerja'        => $request->satuan_kerja_jf_naik_jenjang,
                    'golongan_ruang'    => $request->golongan_ruang_jf_naik_jenjang,
                    // Data Usulan Baru
                    'usul_jabatan'      => $request->usul_jabatan_jf_naik_jenjang,
                    'usul_unit_kerja'   => $request->usul_satuan_kerja_jf_naik_jenjang,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/jf_naik_jenjang', $filename, 'public');

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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Kenaikan Jenjang JF berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }
}
