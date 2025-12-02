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

class TugasBelajarController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Mengambil layanan dengan slug 'tugas-belajar'
        // Pastikan di tabel jenis_layanans sudah ada data dengan slug ini
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'tugas-belajar')->first();

        // Fallback jika layanan belum disetting admin, agar tidak error di view
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.tugas_belajar.index', compact('pegawai', 'syarat'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Utama
        $request->validate([
            'nip_display_tugas_belajar' => 'required|exists:pegawais,nip',
            'jenis_tugas_belajar' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // 2. Ambil Data Pegawai & Layanan
            $pegawai = Pegawai::where('nip', $request->nip_display_tugas_belajar)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'tugas-belajar')->firstOrFail();

            // 3. Generate Nomor Tiket (TB = Tugas Belajar)
            $tiket = 'TB-' . date('Ymd') . '-' . rand(1000, 9999);

            // 4. Simpan Pengajuan (Data Tambahan disesuaikan dengan Form)
            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    // Mapping field dari Form View ke Database JSON column
                    'nama' => $request->nama_pegawai_tugas_belajar,
                    'jabatan' => $request->jabatan_tugas_belajar,
                    'pangkat' => $request->pangkat_tugas_belajar,
                    'satuan_kerja' => $request->satuan_kerja_tugas_belajar,
                    'golongan_ruang' => $request->golongan_ruang_tugas_belajar,
                    'jenis_tugas_belajar' => $request->jenis_tugas_belajar,
                ]
            ]);

            // 5. Proses Upload Dokumen
            foreach ($layanan->syaratDokumens as $dokumen) {
                // Sesuai dengan name di view: name="file_{{ $dokumen->id }}"
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Format nama file: TIKET_SlugDokumen.ext
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();

                    // Simpan ke folder public/storage/documents/tugas_belajar
                    $path = $file->storeAs('documents/tugas_belajar', $filename, 'public');

                    DokumenPengajuan::create([
                        'pengajuan_id' => $pengajuan->id,
                        'syarat_dokumen_id' => $dokumen->id,
                        'nama_file_asli' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'tipe_file' => $file->getClientMimeType(),
                        'ukuran_file' => $file->getSize() / 1024, // KB
                    ]);
                } elseif ($dokumen->is_required) {
                    // Lempar error jika dokumen wajib tidak ada
                    throw new \Exception("Dokumen wajib: {$dokumen->nama_dokumen} belum diunggah.");
                }
            }

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Pengajuan Tugas Belajar berhasil dikirim! Tiket: ' . $tiket);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengajuan: ' . $e->getMessage())->withInput();
        }
    }
}
