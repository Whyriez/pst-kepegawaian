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

class PerbaikanDataController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'perbaikan-data-asn' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'perbaikan-data-asn')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.perbaikan_data.index', compact('pegawai', 'syarat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip_display_perbaikan_data_asn' => 'required|exists:pegawais,nip',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_perbaikan_data_asn)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'perbaikan-data-asn')->firstOrFail();

            $tiket = 'PBD-ASN-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'        => $request->jabatan_pegawai_perbaikan_data_asn,
                    'pangkat'        => $request->pangkat_pegawai_perbaikan_data_asn,
                    'unit_kerja'     => $request->unit_kerja_pegawai_perbaikan_data_asn,
                    'golongan_ruang' => $request->golongan_ruang_pegawai_perbaikan_data_asn,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/perbaikan_data_asn', $filename, 'public');

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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Perbaikan Data ASN berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }
}
