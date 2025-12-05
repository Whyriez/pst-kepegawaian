<?php

namespace App\Http\Controllers\User;

use App\Helpers\CekPeriode;
use App\Http\Controllers\Controller;
use App\Models\DokumenPengajuan;
use App\Models\JenisLayanan;
use App\Models\Pegawai;
use App\Models\Pengajuan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class KenaikanPangkatController extends Controller
{
    /**
     * Halaman KP Fungsional
     * View: pages.user.kenaikan_pangkat.fungsional
     */
    public function fungsional()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ganti get() dengan paginate(10)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai') // pastikan load 'pegawai' juga
        ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'kp-fungsional');
            })
            ->latest()
            ->paginate(10); // <--- UBAH DI SINI

        return view('pages.user.kenaikan_pangkat.fungsional.index', compact('pengajuans'));
    }

    public function createFungsional()
    {
        if (!CekPeriode::isBuka('kp-fungsional')) {
            return redirect()->route('kp.fungsional')
                ->with('error', 'Maaf, Periode pengajuan untuk Kenaikan Pangkat Fungsional sedang DITUTUP.');
        }

        $pegawai = Pegawai::with('satuanKerja')->where('user_id', Auth::id())->firstOrFail();

        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'kp-fungsional')->first();

        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.kenaikan_pangkat.fungsional.create', compact('pegawai', 'syarat'));
    }

    public function editFungsional(Request $request)
    {
        // 1. Ambil ID dari query string (?id=...)
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('kp.fungsional')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // 2. Ambil Data Pengajuan beserta relasinya
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id) // Security: Pastikan punya sendiri
            ->firstOrFail();

        // 3. Cek Status (Hanya boleh edit jika Pending atau Perbaikan)
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('kp.fungsional')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai, tidak dapat diedit.');
        }

        // 4. Ambil Syarat Dokumen
        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.kenaikan_pangkat.fungsional.edit', compact('pengajuan', 'syarat'));
    }

    public function updateFungsional(Request $request, $id)
    {
        $request->validate([
            'nip_display_kp_fungsional'                  => 'required',
            'periode_kenaikan_pangkat_kp_fungsional'     => 'required',
        ], [
            'nip_display_kp_fungsional.required'              => 'NIP wajib diisi.',
            'periode_kenaikan_pangkat_kp_fungsional.required' => 'Periode kenaikan pangkat wajib dipilih.',
        ]);


        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data Utama (JSON)
            $dataTambahan = $pengajuan->data_tambahan; // Ambil data lama
            // Timpa dengan data baru form
            $dataTambahan['jabatan_saat_ini'] = $request->jabatan_kp_fungsional;
            $dataTambahan['pangkat_saat_ini'] = $request->pangkat_kp_fungsional;
            $dataTambahan['unit_kerja'] = $request->unit_kerja_kp_fungsional;
            $dataTambahan['golongan_ruang'] = $request->golongan_ruang_kp_fungsional;
            $dataTambahan['periode'] = $request->periode_kenaikan_pangkat_kp_fungsional;

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

                    // Hapus file lama jika ada (Optional, good practice)
                    $oldDoc = DokumenPengajuan::where('pengajuan_id', $pengajuan->id)
                        ->where('syarat_dokumen_id', $dokumen->id)
                        ->first();

                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->path_file)) {
                        Storage::disk('public')->delete($oldDoc->path_file);
                    }

                    // Simpan File Baru
                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/kp_fungsional', $filename, 'public');

                    // Update atau Create Record DB
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
            return redirect()->route('kp.fungsional')->with('success', 'Perbaikan data berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function storeFungsional(Request $request)
    {
        if (!CekPeriode::isBuka('kp-fungsional')) {
            return redirect()->back()->with('error', 'Gagal! Periode pengajuan telah berakhir.');
        }

        // 1. Validasi Data Utama
        $request->validate([
            'nip_display_kp_fungsional'              => 'required|exists:pegawais,nip',
            'periode_kenaikan_pangkat_kp_fungsional' => 'required',
        ], [
            // NIP
            'nip_display_kp_fungsional.required' => 'NIP wajib diisi.',
            'nip_display_kp_fungsional.exists'   => 'NIP tidak terdaftar dalam database pegawai.',

            // Periode
            'periode_kenaikan_pangkat_kp_fungsional.required' => 'Periode kenaikan pangkat wajib dipilih.',
        ]);


        try {
            DB::beginTransaction();

            // 2. Cari Data Pendukung
            $pegawai = Pegawai::where('nip', $request->nip_display_kp_fungsional)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'kp-fungsional')->firstOrFail();

            // 3. Simpan Header Pengajuan
            // Generate Nomor Tiket: KP-FUN-YYYYMMDD-XXXX
            $tiket = 'KP-FUN-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',   // Default status
                'prioritas' => 'sedang',    // Default prioritas
                'tanggal_pengajuan' => now(),
                // Simpan data form yang tidak ada kolom khususnya ke dalam JSON
                'data_tambahan' => [
                    'jabatan_saat_ini' => $request->jabatan_kp_fungsional,
                    'pangkat_saat_ini' => $request->pangkat_kp_fungsional,
                    'unit_kerja' => $request->unit_kerja_kp_fungsional,
                    'golongan_ruang' => $request->golongan_ruang_kp_fungsional,
                    'periode' => $request->periode_kenaikan_pangkat_kp_fungsional,
                ]
            ]);

            // 4. Loop Syarat Dokumen & Simpan File
            // Kita meloop syarat dari database, lalu mencari input file yang sesuai (file_{id})
            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id; // Contoh: file_1, file_2

                // Cek apakah user mengupload file untuk syarat ini
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Format nama file: TIKET_NAMA-DOKUMEN.ext
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();

                    // Simpan fisik file ke storage
                    $path = $file->storeAs('documents/kp_fungsional', $filename, 'public');

                    // Simpan record ke database
                    DokumenPengajuan::create([
                        'pengajuan_id' => $pengajuan->id,
                        'syarat_dokumen_id' => $dokumen->id,
                        'nama_file_asli' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'tipe_file' => $file->getClientMimeType(),
                        'ukuran_file' => $file->getSize() / 1024, // Konversi ke KB
                    ]);
                } else {
                    // Jika dokumen wajib tapi tidak ada file (Backup validation)
                    if ($dokumen->is_required) {
                        throw new \Exception("Dokumen wajib: {$dokumen->nama_dokumen} belum diunggah.");
                    }
                }
            }

            DB::commit();

            return redirect()->route('kp.fungsional')->with('success', 'Pengajuan berhasil dikirim! Nomor Tiket Anda: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman KP Penyesuaian Ijazah
     * View: pages.user.kenaikan_pangkat.penyesuaian_ijazah
     */
    public function penyesuaianIjazah()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil data pengajuan khusus Penyesuaian Ijazah (slug: kp-pi)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'kp-pi'); // <--- Filter Khusus PI
            })
            ->latest()
            ->paginate(10); // Pagination 10 data per halaman

        return view('pages.user.kenaikan_pangkat.penyesuaian_ijazah.index', compact('pengajuans'));
    }

    public function createPenyesuaianIjazah()
    {
        if (!CekPeriode::isBuka('kp-pi')) {
            return redirect()->route('kp.penyesuaian_ijazah')
                ->with('error', 'Maaf, Periode pengajuan Penyesuaian Ijazah sedang DITUTUP.');
        }

        // 1. Ambil data pegawai (Autofill)
        $pegawai = Pegawai::with('satuanKerja')->where('user_id', Auth::id())->first();

        // 2. Ambil syarat dokumen dinamis berdasarkan slug layanan
        // PASTIKAN DI DATABASE TABEL jenis_layanans SUDAH ADA SLUG: 'kp-penyesuaian-ijazah'
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'kp-pi')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.kenaikan_pangkat.penyesuaian_ijazah.create', compact('pegawai', 'syarat'));
    }

    public function storePenyesuaianIjazah(Request $request)
    {
        if (!CekPeriode::isBuka('kp-pi')) {
            return redirect()->back()
                ->with('error', 'Gagal! Periode pengajuan telah berakhir.');
        }

        // 1. Validasi Dasar
        $request->validate([
            'nip_display_kp_penyesuaian_ijazah'              => 'required|exists:pegawais,nip',
            'periode_kenaikan_pangkat_kp_penyesuaian_ijazah' => 'required',
        ], [
            'nip_display_kp_penyesuaian_ijazah.required' => 'NIP wajib diisi.',
            'nip_display_kp_penyesuaian_ijazah.exists'   => 'NIP tidak terdaftar dalam database pegawai.',

            'periode_kenaikan_pangkat_kp_penyesuaian_ijazah.required' => 'Periode kenaikan pangkat wajib dipilih.',
        ]);


        try {
            DB::beginTransaction();

            // 2. Ambil Data Pegawai & Layanan
            $pegawai = Pegawai::where('nip', $request->nip_display_kp_penyesuaian_ijazah)->firstOrFail();
            // Pastikan di tabel 'jenis_layanans' ada slug ini!
            $layanan = JenisLayanan::where('slug', 'kp-pi')->firstOrFail();

            // 3. Buat Header Pengajuan
            $tiket = 'KP-PI-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_kp_penyesuaian_ijazah,
                    'pangkat' => $request->pangkat_kp_penyesuaian_ijazah,
                    'unit_kerja' => $request->unit_kerja_kp_penyesuaian_ijazah,
                    'golongan_ruang' => $request->golongan_ruang_kp_penyesuaian_ijazah,
                    'periode' => $request->periode_kenaikan_pangkat_kp_penyesuaian_ijazah,
                ]
            ]);

            // 4. Simpan Dokumen (Looping Dinamis)
            // Controller mencari input dengan nama 'file_1', 'file_2', dst sesuai ID dokumen di database
            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/kp_penyesuaian_ijazah', $filename, 'public');

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
            return redirect()->route('kp.penyesuaian_ijazah')->with('success', 'Pengajuan Penyesuaian Ijazah berhasil dikirim! No Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function editPenyesuaianIjazah(Request $request)
    {
        // 1. Ambil ID dari query string (?id=...)
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('kp.penyesuaian_ijazah')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // 2. Ambil Pengajuan (Security: Pastikan milik user & Layanan KP-PI)
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'kp-pi');
            })
            ->firstOrFail();

        // 3. Cek Status (Hanya edit jika Pending/Perbaikan)
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('kp.penyesuaian_ijazah')
                ->with('error', 'Pengajuan sedang diproses/selesai, tidak bisa diedit.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.kenaikan_pangkat.penyesuaian_ijazah.edit', compact('pengajuan', 'syarat'));
    }

    public function updatePenyesuaianIjazah(Request $request, $id)
    {
        $request->validate([
            'periode_kenaikan_pangkat_kp_penyesuaian_ijazah' => 'required',
        ], [
            'periode_kenaikan_pangkat_kp_penyesuaian_ijazah.required' => 'Periode kenaikan pangkat wajib dipilih.',
        ]);


        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data JSON (Data Tambahan)
            // Kita ambil data lama, lalu timpa dengan input baru
            $dataTambahan = $pengajuan->data_tambahan;

            $dataTambahan['jabatan'] = $request->jabatan_kp_penyesuaian_ijazah;
            $dataTambahan['pangkat'] = $request->pangkat_kp_penyesuaian_ijazah;
            $dataTambahan['unit_kerja'] = $request->unit_kerja_kp_penyesuaian_ijazah;
            $dataTambahan['golongan_ruang'] = $request->golongan_ruang_kp_penyesuaian_ijazah;
            $dataTambahan['periode'] = $request->periode_kenaikan_pangkat_kp_penyesuaian_ijazah;

            $pengajuan->update([
                'data_tambahan' => $dataTambahan,
                'status' => 'pending', // Reset ke pending agar dicek ulang
                'catatan_admin' => null,      // Hapus catatan revisi sebelumnya
                'tanggal_pengajuan' => now(),     // Update tanggal submit
            ]);

            // 2. Update Dokumen
            $layanan = $pengajuan->jenisLayanan;

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Hapus file lama fisik (Optional)
                    $oldDoc = DokumenPengajuan::where('pengajuan_id', $pengajuan->id)
                        ->where('syarat_dokumen_id', $dokumen->id)
                        ->first();

                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->path_file)) {
                        Storage::disk('public')->delete($oldDoc->path_file);
                    }

                    // Upload Baru
                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/kp_penyesuaian_ijazah', $filename, 'public');

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
            return redirect()->route('kp.penyesuaian_ijazah')->with('success', 'Perbaikan Penyesuaian Ijazah berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Halaman KP Reguler
     * View: pages.user.kenaikan_pangkat.reguler
     */
    public function reguler()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil pengajuan khusus KP Reguler (slug: kp-reguler)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'kp-reguler'); // <--- Filter Wajib
            })
            ->latest()
            ->paginate(10); // Pagination

        return view('pages.user.kenaikan_pangkat.reguler.index', compact('pengajuans'));
    }

    public function createReguler()
    {
        if (!CekPeriode::isBuka('kp-reguler')) {
            return redirect()->route('kp.reguler')
                ->with('error', 'Maaf, Periode pengajuan Kenaikan Pangkat Reguler sedang DITUTUP.');
        }

        $pegawai = Pegawai::with('satuanKerja')->where('user_id', Auth::id())->first();
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'kp-reguler')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.kenaikan_pangkat.reguler.create', compact('pegawai', 'syarat'));
    }

    public function storeReguler(Request $request)
    {
        if (!CekPeriode::isBuka('kp-reguler')) {
            return redirect()->back()
                ->with('error', 'Gagal! Periode pengajuan telah berakhir.');
        }

        // 1. Validation
        $request->validate([
            'nip_display_kp_reguler'              => 'required|exists:pegawais,nip',
            'periode_kenaikan_pangkat_kp_reguler' => 'required',
        ], [
            'nip_display_kp_reguler.required' => 'NIP wajib diisi.',
            'nip_display_kp_reguler.exists'   => 'NIP tidak terdaftar dalam database pegawai.',

            'periode_kenaikan_pangkat_kp_reguler.required' => 'Periode kenaikan pangkat wajib dipilih.',
        ]);


        try {
            DB::beginTransaction();

            // 2. Find Data
            $pegawai = Pegawai::where('nip', $request->nip_display_kp_reguler)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'kp-reguler')->firstOrFail();

            // 3. Create Ticket & Header
            $tiket = 'KP-REG-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_kp_reguler,
                    'pangkat' => $request->pangkat_kp_reguler,
                    'unit_kerja' => $request->unit_kerja_kp_reguler,
                    'golongan_ruang' => $request->golongan_ruang_kp_reguler,
                    'periode' => $request->periode_kenaikan_pangkat_kp_reguler,
                ]
            ]);

            // 4. Save Documents Dynamically
            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/kp_reguler', $filename, 'public');

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
            return redirect()->route('kp.reguler')->with('success', 'Pengajuan KP Reguler berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function editReguler(Request $request)
    {
        // 1. Ambil ID dari URL (?id=...)
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('kp.reguler')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // 2. Ambil Data Pengajuan (Security Check: Milik User & Tipe Reguler)
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'kp-reguler');
            })
            ->firstOrFail();

        // 3. Cek Status (Hanya boleh edit jika Pending/Perbaikan)
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('kp.reguler')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai, tidak dapat diedit.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.kenaikan_pangkat.reguler.edit', compact('pengajuan', 'syarat'));
    }

    public function updateReguler(Request $request, $id)
    {
        // 1. Validasi
        $request->validate([
            'periode_kenaikan_pangkat_kp_reguler' => 'required',
        ], [
            'periode_kenaikan_pangkat_kp_reguler.required' => 'Periode kenaikan pangkat wajib dipilih.',
        ]);


        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 2. Update Data JSON (Data Tambahan)
            $dataTambahan = $pengajuan->data_tambahan;

            // Map input form ke key JSON yang sesuai (sesuai method storeReguler)
            $dataTambahan['jabatan']        = $request->jabatan_kp_reguler;
            $dataTambahan['pangkat']        = $request->pangkat_kp_reguler;
            $dataTambahan['unit_kerja']     = $request->unit_kerja_kp_reguler;
            $dataTambahan['golongan_ruang'] = $request->golongan_ruang_kp_reguler;
            $dataTambahan['periode']        = $request->periode_kenaikan_pangkat_kp_reguler;

            $pengajuan->update([
                'data_tambahan'     => $dataTambahan,
                'status'            => 'pending', // Reset status agar diverifikasi ulang
                'catatan_admin'     => null,      // Hapus catatan revisi
                'tanggal_pengajuan' => now(),     // Update timestamp
            ]);

            // 3. Update Dokumen
            $layanan = $pengajuan->jenisLayanan;

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Hapus file lama (Opsional, untuk hemat storage)
                    $oldDoc = DokumenPengajuan::where('pengajuan_id', $pengajuan->id)
                        ->where('syarat_dokumen_id', $dokumen->id)
                        ->first();

                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->path_file)) {
                        Storage::disk('public')->delete($oldDoc->path_file);
                    }

                    // Upload Baru
                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/kp_reguler', $filename, 'public');

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
            return redirect()->route('kp.reguler')->with('success', 'Perbaikan data KP Reguler berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Halaman KP Struktural
     * View: pages.user.kenaikan_pangkat.struktural
     */
    public function struktural()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil pengajuan khusus KP Struktural (slug: kp-struktural)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'kp-struktural'); // <--- Filter Wajib
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.kenaikan_pangkat.struktural.index', compact('pengajuans'));
    }

    public function createStruktural()
    {
        if (!CekPeriode::isBuka('kp-struktural')) {
            return redirect()->route('kp.struktural')
                ->with('error', 'Maaf, Periode pengajuan Kenaikan Pangkat Struktural sedang DITUTUP.');
        }

        // 1. Ambil data pegawai login
        $pegawai = Pegawai::with('satuanKerja')->where('user_id', Auth::id())->first();

        // 2. Ambil Dokumen (Pastikan slug 'kp-struktural' ada di tabel jenis_layanans)
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'kp-struktural')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.kenaikan_pangkat.struktural.create', compact('pegawai', 'syarat'));
    }

    public function storeStruktural(Request $request)
    {
        if (!CekPeriode::isBuka('kp-struktural')) {
            return redirect()->back()
                ->with('error', 'Gagal! Periode pengajuan telah berakhir.');
        }

        // 1. Validasi
        $request->validate([
            'nip_display_kp_struktural'              => 'required|exists:pegawais,nip',
            'periode_kenaikan_pangkat_kp_struktural' => 'required',
        ], [
            'nip_display_kp_struktural.required' => 'NIP wajib diisi.',
            'nip_display_kp_struktural.exists'   => 'NIP tidak terdaftar dalam database pegawai.',

            'periode_kenaikan_pangkat_kp_struktural.required' => 'Periode kenaikan pangkat wajib dipilih.',
        ]);


        try {
            DB::beginTransaction();

            // 2. Cari Data
            $pegawai = Pegawai::where('nip', $request->nip_display_kp_struktural)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'kp-struktural')->firstOrFail();

            // 3. Buat Header (Tiket: KP-STR-...)
            $tiket = 'KP-STR-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_kp_struktural,
                    'pangkat' => $request->pangkat_kp_struktural,
                    'unit_kerja' => $request->unit_kerja_kp_struktural,
                    'golongan_ruang' => $request->golongan_ruang_kp_struktural,
                    'periode' => $request->periode_kenaikan_pangkat_kp_struktural,
                ]
            ]);

            // 4. Simpan Dokumen Dinamis
            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/kp_struktural', $filename, 'public');

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
            return redirect()->route('kp.struktural')->with('success', 'Pengajuan KP Struktural berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function editStruktural(Request $request)
    {
        // 1. Ambil ID
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('kp.struktural')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // 2. Cari Data (Security: Milik User & Tipe KP-Struktural)
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'kp-struktural');
            })
            ->firstOrFail();

        // 3. Cek Status
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('kp.struktural')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai, tidak dapat diedit.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.kenaikan_pangkat.struktural.edit', compact('pengajuan', 'syarat'));
    }

    public function updateStruktural(Request $request, $id)
    {
        // 1. Validasi
        $request->validate([
            'periode_kenaikan_pangkat_kp_struktural' => 'required',
        ], [
            'periode_kenaikan_pangkat_kp_struktural.required' => 'Periode kenaikan pangkat wajib dipilih.',
        ]);


        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 2. Update Data JSON (Data Tambahan)
            $dataTambahan = $pengajuan->data_tambahan;

            // Map input form (suffix _kp_struktural)
            $dataTambahan['jabatan']        = $request->jabatan_kp_struktural;
            $dataTambahan['pangkat']        = $request->pangkat_kp_struktural;
            $dataTambahan['unit_kerja']     = $request->unit_kerja_kp_struktural;
            $dataTambahan['golongan_ruang'] = $request->golongan_ruang_kp_struktural;
            $dataTambahan['periode']        = $request->periode_kenaikan_pangkat_kp_struktural;

            $pengajuan->update([
                'data_tambahan'     => $dataTambahan,
                'status'            => 'pending', // Reset status
                'catatan_admin'     => null,      // Hapus catatan
                'tanggal_pengajuan' => now(),
            ]);

            // 3. Update Dokumen
            $layanan = $pengajuan->jenisLayanan;

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Hapus file lama
                    $oldDoc = DokumenPengajuan::where('pengajuan_id', $pengajuan->id)
                        ->where('syarat_dokumen_id', $dokumen->id)
                        ->first();

                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->path_file)) {
                        Storage::disk('public')->delete($oldDoc->path_file);
                    }

                    // Upload Baru
                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/kp_struktural', $filename, 'public');

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
            return redirect()->route('kp.struktural')->with('success', 'Perbaikan data KP Struktural berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function cekNip($nip): JsonResponse
    {
        // Cari pegawai berdasarkan NIP dan relasinya
        $pegawai = Pegawai::with('satuanKerja')
            ->where('nip', $nip)
            ->first();

        if ($pegawai) {
            return response()->json([
                'success' => true,
                'data' => [
                    'nama' => $pegawai->nama_lengkap,
                    'nip' => $pegawai->nip,
                    'jabatan' => $pegawai->jabatan,
                    'pangkat' => $pegawai->pangkat,
                    'unit_kerja' => $pegawai->satuanKerja->nama_satuan_kerja ?? '-',
                    'golongan_ruang' => $pegawai->golongan_ruang
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data pegawai dengan NIP tersebut tidak ditemukan.'
        ], 404);
    }
}
