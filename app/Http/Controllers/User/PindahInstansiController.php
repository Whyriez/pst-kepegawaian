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

class PindahInstansiController extends Controller
{
    /**
     * Halaman Pindah Masuk Instansi
     */
    public function masuk()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil riwayat pengajuan (Slug: pindah-masuk)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'pindah-masuk');
            })
            ->latest()
            ->paginate(10);

        // Pastikan path view sesuai struktur folder Anda
        return view('pages.user.pindah_antar_instansi.masuk.index', compact('pengajuans'));
    }

    public function createMasuk()
    {
        if (!CekPeriode::isBuka('pindah-masuk')) {
            return redirect()->route('pindah.masuk')
                ->with('error', 'Maaf, Periode pengajuan Pindah Masuk Instansi sedang DITUTUP.');
        }

        $pegawai = Pegawai::with('satuanKerja')->where('user_id', Auth::id())->first();

        // Pastikan slug 'pindah-masuk' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pindah-masuk')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        // Lokasi view: resources/views/pages/user/pindah_antar_instansi/masuk.blade.php
        return view('pages.user.pindah_antar_instansi.masuk.create', compact('pegawai', 'syarat'));
    }

    public function storeMasuk(Request $request)
    {
        if (!CekPeriode::isBuka('pindah-masuk')) {
            return redirect()->back()->with('error', 'Gagal! Periode pengajuan telah berakhir.');
        }

        $request->validate([
            'nip_display_pindah_masuk' => 'required|exists:pegawais,nip',
        ], [
            'nip_display_pindah_masuk.required' => 'NIP wajib diisi.',
            'nip_display_pindah_masuk.exists' => 'NIP tidak ditemukan dalam data pegawai.',
        ]);


        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_pindah_masuk)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'pindah-masuk')->firstOrFail();

            $tiket = 'PI-MSK-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_pindah_masuk,
                    'pangkat' => $request->pangkat_pindah_masuk,
                    'unit_kerja' => $request->unit_kerja_pindah_masuk,
                    'golongan_ruang' => $request->golongan_ruang_pindah_masuk,
                    // Data Usulan Baru
                    'usul_jabatan' => $request->usul_jabatan_pindah_masuk,
                    'usul_unit_kerja' => $request->usul_unit_kerja_pindah_masuk,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pindah_masuk', $filename, 'public');

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
            return redirect()->route('pindah.masuk')->with('success', 'Pengajuan Pindah Masuk berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function editMasuk(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('pindah.masuk')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // Ambil Data & Validasi Kepemilikan
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'pindah-masuk');
            })
            ->firstOrFail();

        // Cek Status (Hanya Pending/Perbaikan)
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('pindah.masuk')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.pindah_antar_instansi.masuk.edit', compact('pengajuan', 'syarat'));
    }

    /**
     * Proses Update Pindah Masuk
     */
    public function updateMasuk(Request $request, $id)
    {
        // Validasi Input
        $request->validate([
            'jabatan_pindah_masuk' => 'required',
            'unit_kerja_pindah_masuk' => 'required',
            'usul_jabatan_pindah_masuk' => 'required',
            'usul_unit_kerja_pindah_masuk' => 'required',
        ], [
            'jabatan_pindah_masuk.required' => 'Jabatan pindah masuk wajib diisi.',
            'unit_kerja_pindah_masuk.required' => 'Unit kerja pindah masuk wajib diisi.',
            'usul_jabatan_pindah_masuk.required' => 'Usulan jabatan wajib diisi.',
            'usul_unit_kerja_pindah_masuk.required' => 'Usulan unit kerja wajib diisi.',
        ]);


        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;

            // Update Data Asal
            $dataTambahan['jabatan'] = $request->jabatan_pindah_masuk;
            $dataTambahan['pangkat'] = $request->pangkat_pindah_masuk;
            $dataTambahan['unit_kerja'] = $request->unit_kerja_pindah_masuk;
            $dataTambahan['golongan_ruang'] = $request->golongan_ruang_pindah_masuk;

            // Update Data Usulan
            $dataTambahan['usul_jabatan'] = $request->usul_jabatan_pindah_masuk;
            $dataTambahan['usul_unit_kerja'] = $request->usul_unit_kerja_pindah_masuk;

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
                    $path = $file->storeAs('documents/pindah_masuk', $filename, 'public');

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
            return redirect()->route('pindah.masuk')->with('success', 'Perbaikan data Pindah Masuk berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Pindah Keluar Instansi
     */
    public function keluar()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil riwayat pengajuan (Slug: pindah-keluar)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'pindah-keluar');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.pindah_antar_instansi.keluar.index', compact('pengajuans'));
    }

    public function createKeluar()
    {
        if (!CekPeriode::isBuka('pindah-keluar')) {
            return redirect()->route('pindah.keluar')
                ->with('error', 'Maaf, Periode pengajuan Pindah Keluar Instansi sedang DITUTUP.');
        }

        $pegawai = Pegawai::with('satuanKerja')->where('user_id', Auth::id())->first();

        // Pastikan slug 'pindah-keluar' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'pindah-keluar')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.pindah_antar_instansi.keluar.create', compact('pegawai', 'syarat'));
    }

    public function storeKeluar(Request $request)
    {
        if (!CekPeriode::isBuka('pindah-keluar')) {
            return redirect()->back()->with('error', 'Gagal! Periode pengajuan telah berakhir.');
        }

        $request->validate([
            'nip_display_pindah_keluar' => 'required|exists:pegawais,nip',
        ], [
            'nip_display_pindah_keluar.required' => 'NIP wajib diisi.',
            'nip_display_pindah_keluar.exists' => 'NIP tidak ditemukan dalam data pegawai.',
        ]);


        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_pindah_keluar)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'pindah-keluar')->firstOrFail();

            $tiket = 'PI-KLR-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_pindah_keluar,
                    'pangkat' => $request->pangkat_pindah_keluar,
                    'unit_kerja' => $request->unit_kerja_pindah_keluar,
                    'golongan_ruang' => $request->golongan_ruang_pindah_keluar,
                    // Data Tujuan
                    'instansi_tujuan' => $request->instansi_tujuan_pindah_keluar,
                    'jabatan_tujuan' => $request->jabatan_tujuan_pindah_keluar,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/pindah_keluar', $filename, 'public');

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
            return redirect()->route('pindah.keluar')->with('success', 'Pengajuan Pindah Keluar berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function editKeluar(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('pindah.keluar')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // Ambil Data & Validasi
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'pindah-keluar');
            })
            ->firstOrFail();

        // Cek Status
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('pindah.keluar')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.pindah_antar_instansi.keluar.edit', compact('pengajuan', 'syarat'));
    }

    public function updateKeluar(Request $request, $id)
    {
        // Validasi Input
        $request->validate([
            'jabatan_pindah_keluar' => 'required',
            'unit_kerja_pindah_keluar' => 'required',
            'instansi_tujuan_pindah_keluar' => 'required',
            'jabatan_tujuan_pindah_keluar' => 'required',
        ], [
            'jabatan_pindah_keluar.required' => 'Jabatan pindah keluar wajib diisi.',
            'unit_kerja_pindah_keluar.required' => 'Unit kerja pindah keluar wajib diisi.',
            'instansi_tujuan_pindah_keluar.required' => 'Instansi tujuan wajib diisi.',
            'jabatan_tujuan_pindah_keluar.required' => 'Jabatan tujuan wajib diisi.',
        ]);


        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;

            // Update Data Asal
            $dataTambahan['jabatan'] = $request->jabatan_pindah_keluar;
            $dataTambahan['pangkat'] = $request->pangkat_pindah_keluar;
            $dataTambahan['unit_kerja'] = $request->unit_kerja_pindah_keluar;
            $dataTambahan['golongan_ruang'] = $request->golongan_ruang_pindah_keluar;

            // Update Data Tujuan
            $dataTambahan['instansi_tujuan'] = $request->instansi_tujuan_pindah_keluar;
            $dataTambahan['jabatan_tujuan'] = $request->jabatan_tujuan_pindah_keluar;

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
                    $path = $file->storeAs('documents/pindah_keluar', $filename, 'public');

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
            return redirect()->route('pindah.keluar')->with('success', 'Perbaikan data Pindah Keluar berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}
