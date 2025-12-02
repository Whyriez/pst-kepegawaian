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

class KonversiAKPendidikanController extends Controller
{
    public function index()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil riwayat pengajuan (Slug: konversi-ak-pendidikan)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'konversi-ak-pendidikan');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.konversi_ak_pendidikan.index', compact('pengajuans'));
    }

    public function create()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        // Mengambil layanan dengan slug 'konversi-ak-pendidikan'
        // Pastikan di database tabel jenis_layanans sudah ada slug ini
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'konversi-ak-pendidikan')->first();

        // Fallback jika layanan belum disetting admin
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.konversi_ak_pendidikan.create', compact('pegawai', 'syarat'));
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
            return redirect()->route('konversi_ak_pendidikan')->with('success', 'Pengajuan Konversi AK Pendidikan berhasil dikirim! Tiket: ' . $tiket);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengajuan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('konversi_ak_pendidikan')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // Ambil Data & Validasi
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'konversi-ak-pendidikan');
            })
            ->firstOrFail();

        // Cek Status
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('konversi_ak_pendidikan')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.konversi_ak_pendidikan.edit', compact('pengajuan', 'syarat'));
    }

    /**
     * Proses Update Konversi AK
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jabatan_konversi_ak_pendidikan' => 'required',
            'satuan_kerja_konversi_ak_pendidikan' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;
            $dataTambahan['nama']           = $request->nama_pegawai_konversi_ak_pendidikan;
            $dataTambahan['jabatan']        = $request->jabatan_konversi_ak_pendidikan;
            $dataTambahan['pangkat']        = $request->pangkat_konversi_ak_pendidikan;
            $dataTambahan['satuan_kerja']   = $request->satuan_kerja_konversi_ak_pendidikan;
            $dataTambahan['golongan_ruang'] = $request->golongan_ruang_konversi_ak_pendidikan;

            $pengajuan->update([
                'data_tambahan'     => $dataTambahan,
                'status'            => 'pending', // Reset status
                'catatan_admin'     => null,
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
                    $path = $file->storeAs('documents/konversi_ak_pendidikan', $filename, 'public');

                    DokumenPengajuan::updateOrCreate(
                        [
                            'pengajuan_id' => $pengajuan->id,
                            'syarat_dokumen_id' => $dokumen->id
                        ],
                        [
                            'nama_file_asli' => $file->getClientOriginalName(),
                            'path_file'      => $path,
                            'tipe_file'      => $file->getClientMimeType(),
                            'ukuran_file'    => $file->getSize() / 1024,
                        ]
                    );
                }
            }

            DB::commit();
            return redirect()->route('konversi_ak_pendidikan')->with('success', 'Perbaikan data Konversi AK berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}
