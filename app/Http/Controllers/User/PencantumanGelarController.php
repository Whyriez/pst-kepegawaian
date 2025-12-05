<?php

namespace App\Http\Controllers\User;

use App\Helpers\CekPeriode;
use App\Http\Controllers\Controller;
use App\Models\DokumenPengajuan;
use App\Models\JenisLayanan;
use App\Models\Pegawai;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PencantumanGelarController extends Controller
{
    /**
     * Halaman Pencantuman Gelar Akademik
     */
    public function akademik()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil riwayat pengajuan (Slug: gelar-akademik)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'gelar-akademik');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.pencantuman_gelar.akademik.index', compact('pengajuans'));
    }

    public function createAkademik()
    {
        if (!CekPeriode::isBuka('gelar-akademik')) {
            return redirect()->route('gelar.akademik')
                ->with('error', 'Maaf, Periode pengajuan Gelar Akademik sedang DITUTUP.');
        }

        $pegawai = Pegawai::with('satuanKerja')->where('user_id', Auth::id())->first();

        // Pastikan slug 'gelar-akademik' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'gelar-akademik')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pencantuman_gelar.akademik.create', compact('pegawai', 'syarat'));
    }

    public function storeAkademik(Request $request)
    {
        if (!CekPeriode::isBuka('gelar-akademik')) {
            return redirect()->back()->with('error', 'Gagal! Periode pengajuan telah berakhir.');
        }

        $request->validate([
            'nip_display_gelar_akademik' => 'required|exists:pegawais,nip',
            'jenjang_pendidikan_gelar_akademik' => 'required',
        ],[
            'nip_display_gelar_akademik.required' => 'NIP wajib diisi.',
            'nip_display_gelar_akademik.exists' => 'NIP tidak ditemukan dalam data pegawai.',

            'jenjang_pendidikan_gelar_akademik.required' => 'Jenjang pendidikan wajib dipilih.',
        ]);


        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_gelar_akademik)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'gelar-akademik')->firstOrFail();

            $tiket = 'GEL-AKA-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_gelar_akademik,
                    'pangkat' => $request->pangkat_gelar_akademik,
                    'unit_kerja' => $request->satuan_kerja_gelar_akademik,
                    'golongan_ruang' => $request->golongan_ruang_gelar_akademik,
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
                        'pengajuan_id' => $pengajuan->id,
                        'syarat_dokumen_id' => $dokumen->id,
                        'nama_file_asli' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'tipe_file' => $file->getClientMimeType(),
                        'ukuran_file' => $file->getSize() / 1024,
                    ]);
                } elseif ($dokumen->is_required) {
                    throw new \Exception("Dokumen wajib: {$dokumen->nama_dokumen} belum diunggah.");
                }
            }

            DB::commit();
            return redirect()->route('gelar.akademik')->with('success', 'Pengajuan Gelar Akademik berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function editAkademik(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('gelar.akademik')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'gelar-akademik');
            })
            ->firstOrFail();

        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('gelar.akademik')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.pencantuman_gelar.akademik.edit', compact('pengajuan', 'syarat'));
    }

    public function updateAkademik(Request $request, $id)
    {
        $request->validate([
            'jabatan_gelar_akademik' => 'required',
            'satuan_kerja_gelar_akademik' => 'required',
            'jenjang_pendidikan_gelar_akademik' => 'required',
        ],[
            'jabatan_gelar_akademik.required' => 'Jabatan wajib diisi.',
            'satuan_kerja_gelar_akademik.required' => 'Satuan kerja wajib diisi.',
            'jenjang_pendidikan_gelar_akademik.required' => 'Jenjang pendidikan wajib dipilih.',
        ]);


        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            $dataTambahan = $pengajuan->data_tambahan;
            $dataTambahan['jabatan'] = $request->jabatan_gelar_akademik;
            $dataTambahan['pangkat'] = $request->pangkat_gelar_akademik;
            $dataTambahan['unit_kerja'] = $request->satuan_kerja_gelar_akademik;
            $dataTambahan['golongan_ruang'] = $request->golongan_ruang_gelar_akademik;
            $dataTambahan['jenjang_pendidikan'] = $request->jenjang_pendidikan_gelar_akademik;

            $pengajuan->update([
                'data_tambahan' => $dataTambahan,
                'status' => 'pending',
                'catatan_admin' => null,
                'tanggal_pengajuan' => now(),
            ]);

            $layanan = $pengajuan->jenisLayanan;

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    $oldDoc = DokumenPengajuan::where('pengajuan_id', $pengajuan->id)
                        ->where('syarat_dokumen_id', $dokumen->id)
                        ->first();

                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->path_file)) {
                        Storage::disk('public')->delete($oldDoc->path_file);
                    }

                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/gelar_akademik', $filename, 'public');

                    DokumenPengajuan::updateOrCreate(
                        [
                            'pengajuan_id' => $pengajuan->id,
                            'syarat_dokumen_id' => $dokumen->id
                        ],
                        [
                            'nama_file_asli' => $file->getClientOriginalName(),
                            'path_file' => $path,
                            'tipe_file' => $file->getClientMimeType(),
                            'ukuran_file' => $file->getSize() / 1024,
                        ]
                    );
                }
            }

            DB::commit();
            return redirect()->route('gelar.akademik')->with('success', 'Perbaikan data Gelar Akademik berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Pencantuman Gelar Profesi
     */
    public function profesi()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil riwayat pengajuan (Slug: gelar-profesi)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'gelar-profesi');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.pencantuman_gelar.profesi.index', compact('pengajuans'));
    }

    public function createProfesi()
    {
        if (!CekPeriode::isBuka('gelar-profesi')) {
            return redirect()->route('gelar.profesi')
                ->with('error', 'Maaf, Periode pengajuan Gelar Profesi sedang DITUTUP.');
        }

        $pegawai = Pegawai::with('satuanKerja')->where('user_id', Auth::id())->first();

        // Pastikan slug 'gelar-profesi' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'gelar-profesi')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pencantuman_gelar.profesi.create', compact('pegawai', 'syarat'));
    }

    public function storeProfesi(Request $request)
    {
        if (!CekPeriode::isBuka('gelar-profesi')) {
            return redirect()->back()->with('error', 'Gagal! Periode pengajuan telah berakhir.');
        }

        $request->validate([
            'nip_display_gelar_profesi' => 'required|exists:pegawais,nip',
            'usul_profesi_gelar_profesi' => 'required',
        ],[
            'nip_display_gelar_profesi.required' => 'NIP wajib diisi.',
            'nip_display_gelar_profesi.exists' => 'NIP tidak ditemukan dalam data pegawai.',
            'usul_profesi_gelar_profesi.required' => 'Usulan profesi wajib diisi.',
        ]);


        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_gelar_profesi)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'gelar-profesi')->firstOrFail();

            $tiket = 'GEL-PRO-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_gelar_profesi,
                    'pangkat' => $request->pangkat_gelar_profesi,
                    'unit_kerja' => $request->satuan_kerja_gelar_profesi,
                    'golongan_ruang' => $request->golongan_ruang_gelar_profesi,
                    'jenjang_pendidikan' => $request->jenjang_pendidikan_gelar_profesi,
                    'usul_profesi' => $request->usul_profesi_gelar_profesi, // Field khusus profesi
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/gelar_profesi', $filename, 'public');

                    DokumenPengajuan::create([
                        'pengajuan_id' => $pengajuan->id,
                        'syarat_dokumen_id' => $dokumen->id,
                        'nama_file_asli' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'tipe_file' => $file->getClientMimeType(),
                        'ukuran_file' => $file->getSize() / 1024,
                    ]);
                } elseif ($dokumen->is_required) {
                    throw new \Exception("Dokumen wajib: {$dokumen->nama_dokumen} belum diunggah.");
                }
            }

            DB::commit();
            return redirect()->route('gelar.profesi')->with('success', 'Pengajuan Gelar Profesi berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function editProfesi(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('gelar.profesi')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // Ambil Data & Validasi (Slug: gelar-profesi)
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'gelar-profesi');
            })
            ->firstOrFail();

        // Cek Status
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('gelar.profesi')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.pencantuman_gelar.profesi.edit', compact('pengajuan', 'syarat'));
    }

    public function updateProfesi(Request $request, $id)
    {
        // Validasi
        $request->validate([
            'jabatan_gelar_profesi' => 'required',
            'satuan_kerja_gelar_profesi' => 'required',
            'jenjang_pendidikan_gelar_profesi' => 'required',
            'usul_profesi_gelar_profesi' => 'required',
        ],[
            'jabatan_gelar_profesi.required' => 'Jabatan wajib diisi.',
            'satuan_kerja_gelar_profesi.required' => 'Satuan kerja wajib diisi.',
            'jenjang_pendidikan_gelar_profesi.required' => 'Jenjang pendidikan wajib diisi.',
            'usul_profesi_gelar_profesi.required' => 'Usulan profesi wajib diisi.',
        ]);


        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;

            // Map input ke key JSON
            $dataTambahan['jabatan'] = $request->jabatan_gelar_profesi;
            $dataTambahan['pangkat'] = $request->pangkat_gelar_profesi;
            $dataTambahan['unit_kerja'] = $request->satuan_kerja_gelar_profesi;
            $dataTambahan['golongan_ruang'] = $request->golongan_ruang_gelar_profesi;
            $dataTambahan['jenjang_pendidikan'] = $request->jenjang_pendidikan_gelar_profesi;
            $dataTambahan['usul_profesi'] = $request->usul_profesi_gelar_profesi;

            $pengajuan->update([
                'data_tambahan' => $dataTambahan,
                'status' => 'pending', // Reset status
                'catatan_admin' => null,
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

                    // Upload File Baru
                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/gelar_profesi', $filename, 'public');

                    DokumenPengajuan::updateOrCreate(
                        [
                            'pengajuan_id' => $pengajuan->id,
                            'syarat_dokumen_id' => $dokumen->id
                        ],
                        [
                            'nama_file_asli' => $file->getClientOriginalName(),
                            'path_file' => $path,
                            'tipe_file' => $file->getClientMimeType(),
                            'ukuran_file' => $file->getSize() / 1024,
                        ]
                    );
                }
            }

            DB::commit();
            return redirect()->route('gelar.profesi')->with('success', 'Perbaikan data Gelar Profesi berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}
