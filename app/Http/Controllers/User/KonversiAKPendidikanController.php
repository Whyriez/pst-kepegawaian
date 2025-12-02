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

class KonversiAKPendidikanController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Mengambil layanan dengan slug 'konversi-ak-pendidikan'
        // Pastikan di database tabel jenis_layanans sudah ada slug ini
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'konversi-ak-pendidikan')->first();

        // Fallback jika layanan belum disetting admin
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.konversi_ak_pendidikan.index', compact('pegawai', 'syarat'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Utama (Hanya NIP, karena dokumen divalidasi manual di loop)
        $request->validate([
            'nip_display_konversi_ak_pendidikan' => 'required|exists:pegawais,nip',
        ]);

        try {
            DB::beginTransaction();

            // 2. Ambil Data Pegawai & Layanan
            $pegawai = Pegawai::where('nip', $request->nip_display_konversi_ak_pendidikan)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'konversi-ak-pendidikan')->firstOrFail();

            // 3. Generate Nomor Tiket (KAKP = Konversi Angka Kredit Pendidikan)
            $tiket = 'KAKP-' . date('Ymd') . '-' . rand(1000, 9999);

            // 4. Simpan Pengajuan
            $pengajuan = Pengajuan::create([
                'nomor_tiket'       => $tiket,
                'pegawai_id'        => $pegawai->id,
                'jenis_layanan_id'  => $layanan->id,
                'status'            => 'pending',
                'prioritas'         => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan'     => [
                    // Mapping data pegawai snapshot saat pengajuan
                    'nama'           => $request->nama_pegawai_konversi_ak_pendidikan,
                    'jabatan'        => $request->jabatan_konversi_ak_pendidikan,
                    'pangkat'        => $request->pangkat_konversi_ak_pendidikan,
                    'satuan_kerja'   => $request->satuan_kerja_konversi_ak_pendidikan,
                    'golongan_ruang' => $request->golongan_ruang_konversi_ak_pendidikan,
                ]
            ]);

            // 5. Proses Upload Dokumen (Dinamis sesuai Database)
            foreach ($layanan->syaratDokumens as $dokumen) {
                // Input name di view menggunakan format file_{id}
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Format nama file: TIKET_SlugDokumen.ext
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();

                    // Simpan ke folder public/storage/documents/konversi_ak_pendidikan
                    $path = $file->storeAs('documents/konversi_ak_pendidikan', $filename, 'public');

                    DokumenPengajuan::create([
                        'pengajuan_id'      => $pengajuan->id,
                        'syarat_dokumen_id' => $dokumen->id,
                        'nama_file_asli'    => $file->getClientOriginalName(),
                        'path_file'         => $path,
                        'tipe_file'         => $file->getClientMimeType(),
                        'ukuran_file'       => $file->getSize() / 1024, // KB
                    ]);
                } elseif ($dokumen->is_required) {
                    throw new \Exception("Dokumen wajib: {$dokumen->nama_dokumen} belum diunggah.");
                }
            }

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Pengajuan Konversi AK Pendidikan berhasil dikirim! Tiket: ' . $tiket);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengajuan: ' . $e->getMessage())->withInput();
        }
    }
}
