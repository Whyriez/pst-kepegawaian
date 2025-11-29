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

class PenugasanController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Pastikan slug 'penugasan' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'penugasan')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.penugasan.index', compact('pegawai', 'syarat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip_display_penugasan' => 'required|exists:pegawais,nip',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_penugasan)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'penugasan')->firstOrFail();

            $tiket = 'TGS-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    'jabatan'        => $request->jabatan_penugasan,
                    'pangkat'        => $request->pangkat_penugasan,
                    'unit_kerja'     => $request->satuan_kerja_penugasan,
                    'golongan_ruang' => $request->golongan_ruang_penugasan,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/penugasan', $filename, 'public');

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
            return redirect()->route('dashboard')->with('success', 'Pengajuan Penugasan berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }
}
