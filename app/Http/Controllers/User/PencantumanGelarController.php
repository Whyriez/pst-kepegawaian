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

class PencantumanGelarController extends Controller
{
    /**
     * Halaman Pencantuman Gelar Akademik
     */
    public function akademik()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'gelar-akademik' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'gelar-akademik')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pencantuman_gelar.akademik', compact('pegawai', 'syarat'));
    }

    public function storeAkademik(Request $request)
    {
        $request->validate([
            'nip_display_gelar_akademik' => 'required|exists:pegawais,nip',
            'jenjang_pendidikan_gelar_akademik' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_gelar_akademik)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'gelar-akademik')->firstOrFail();

            $tiket = 'GEL-AKA-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'            => $request->jabatan_gelar_akademik,
                    'pangkat'            => $request->pangkat_gelar_akademik,
                    'unit_kerja'         => $request->satuan_kerja_gelar_akademik,
                    'golongan_ruang'     => $request->golongan_ruang_gelar_akademik,
                    'jenjang_pendidikan' => $request->jenjang_pendidikan_gelar_akademik,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/gelar_akademik', $filename, 'public');

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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Gelar Akademik berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman Pencantuman Gelar Profesi
     */
    public function profesi()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'gelar-profesi' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'gelar-profesi')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pencantuman_gelar.profesi', compact('pegawai', 'syarat'));
    }

    public function storeProfesi(Request $request)
    {
        $request->validate([
            'nip_display_gelar_profesi' => 'required|exists:pegawais,nip',
            'usul_profesi_gelar_profesi' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_gelar_profesi)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'gelar-profesi')->firstOrFail();

            $tiket = 'GEL-PRO-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'            => $request->jabatan_gelar_profesi,
                    'pangkat'            => $request->pangkat_gelar_profesi,
                    'unit_kerja'         => $request->satuan_kerja_gelar_profesi,
                    'golongan_ruang'     => $request->golongan_ruang_gelar_profesi,
                    'jenjang_pendidikan' => $request->jenjang_pendidikan_gelar_profesi,
                    'usul_profesi'       => $request->usul_profesi_gelar_profesi, // Field khusus profesi
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/gelar_profesi', $filename, 'public');

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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Gelar Profesi berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }
}
