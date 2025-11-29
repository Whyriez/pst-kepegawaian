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

class PindahInstansiController extends Controller
{
    /**
     * Halaman Pindah Masuk Instansi
     */
    public function masuk()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'pindah-masuk' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pindah-masuk')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        // Lokasi view: resources/views/pages/user/pindah_antar_instansi/masuk.blade.php
        return view('pages.user.pindah_antar_instansi.masuk', compact('pegawai', 'syarat'));
    }

    public function storeMasuk(Request $request)
    {
        $request->validate([
            'nip_display_pindah_masuk' => 'required|exists:pegawais,nip',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_pindah_masuk)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'pindah-masuk')->firstOrFail();

            $tiket = 'PI-MSK-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'           => $request->jabatan_pindah_masuk,
                    'pangkat'           => $request->pangkat_pindah_masuk,
                    'unit_kerja'        => $request->unit_kerja_pindah_masuk,
                    'golongan_ruang'    => $request->golongan_ruang_pindah_masuk,
                    // Data Usulan Baru
                    'usul_jabatan'      => $request->usul_jabatan_pindah_masuk,
                    'usul_unit_kerja'   => $request->usul_unit_kerja_pindah_masuk,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pindah_masuk', $filename, 'public');

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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Pindah Masuk berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman Pindah Keluar Instansi
     */
    public function keluar()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'pindah-keluar' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pindah-keluar')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pindah_antar_instansi.keluar', compact('pegawai', 'syarat'));
    }

    public function storeKeluar(Request $request)
    {
        $request->validate([
            'nip_display_pindah_keluar' => 'required|exists:pegawais,nip',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_pindah_keluar)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'pindah-keluar')->firstOrFail();

            $tiket = 'PI-KLR-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'           => $request->jabatan_pindah_keluar,
                    'pangkat'           => $request->pangkat_pindah_keluar,
                    'unit_kerja'        => $request->unit_kerja_pindah_keluar,
                    'golongan_ruang'    => $request->golongan_ruang_pindah_keluar,
                    // Data Tujuan
                    'instansi_tujuan'   => $request->instansi_tujuan_pindah_keluar,
                    'jabatan_tujuan'    => $request->jabatan_tujuan_pindah_keluar,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pindah_keluar', $filename, 'public');

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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Pindah Keluar berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }
}
