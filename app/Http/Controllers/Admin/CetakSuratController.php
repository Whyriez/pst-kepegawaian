<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArsipSurat;
use App\Models\JenisLayanan;
use App\Models\Pegawai;
use App\Models\Pengajuan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use NumberFormatter;

class CetakSuratController extends Controller
{
    public function pengantar(Request $request)
    {
        // 1. DATA DINAMIS PEGAWAI (Dengan Pagination)
        $data_pegawai = Pengajuan::with(['pegawai', 'jenisLayanan'])
            ->whereRaw("LOWER(status) = 'pending'")
            ->latest()
            ->paginate(10) // Ganti get() dengan paginate()
            ->through(function ($item) {
                // through() memodifikasi item di dalam pagination tanpa merusak link page
                return (object) [
                    'id'      => $item->id,
                    'nama'    => $item->pegawai->nama_lengkap ?? '-',
                    'nip'     => $item->pegawai->nip ?? '-',
                    'jabatan' => $item->pegawai->jabatan ?? '-',
                    'perihal' => $item->jenisLayanan->nama_layanan ?? '-'
                ];
            });


        $templates = ArsipSurat::with('jenisLayanan')
        ->where('jenis_dokumen', 'PENGANTAR')
            ->latest()
            ->limit(5)
            ->get();

        $jenis_layanan = JenisLayanan::where('is_active', true)->get();

        return view('pages.admin.cetak_surat.pengantar', compact('data_pegawai', 'templates', 'jenis_layanan'));
    }

    public function exportPengantar(Request $request)
    {
        // ==========================================
        // 1. SETUP LOGO & TANGGAL
        // ==========================================
        $path = public_path('assets/logo_kantor.png');
        $logo_base64 = '';

        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $logo_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $tanggal_surat = Carbon::now()->isoFormat('D MMMM Y');
        $tahun_ini = Carbon::now()->year;

        // ==========================================
        // 2. TANGKAP INPUT & DATA LAYANAN
        // ==========================================
        // Pastikan name di modal adalah 'jenis_layanan_id'
        $jenis_layanan_id = $request->input('jenis_layanan_id');
        $input_periode    = strtolower($request->input('periode')); // januari, dst

        // Ambil Data Layanan dari Database (berdasarkan ID yang dipilih)
        $layanan = JenisLayanan::find($jenis_layanan_id);


        $perihal_text = "Surat Pengantar";

        if ($layanan) {
            $kategori = $layanan->kategori;
            $nama_layanan = $layanan->nama_layanan;

            switch ($kategori) {
                case 'Kenaikan Pangkat':
                    $periode_text = $input_periode ? ucfirst($input_periode) : '';
                    $perihal_text = "Usul Kenaikan Pangkat Periode $periode_text $tahun_ini";
                    break;

                case 'Pencantuman Gelar':
                    $perihal_text = "Usul Pencantuman Gelar Akademik/Profesi";
                    break;

                case 'Pensiun':
                    // Hasil: "Usul Pensiun BUP Pegawai" atau "Usul Pensiun Janda/Duda Pegawai"
                    $perihal_text = "Usul " . $nama_layanan . " Pegawai";
                    break;

                case 'Pindah Instansi':
                    // Hasil: "Usul Pindah Masuk" atau "Usul Pindah Keluar"
                    $perihal_text = "Usul " . $nama_layanan . " Tahun " . $tahun_ini;
                    break;

                case 'Jabatan Fungsional':
                    // Hasil: "Usul Pengangkatan JF", "Usul Naik Jenjang JF"
                    $perihal_text = "Usul " . $nama_layanan . " Tahun " . $tahun_ini;
                    break;

                case 'Lainnya':
                    // Hasil: "Usul Tugas Belajar", "Usul Satyalancana", "Usul Perbaikan Data ASN"
                    $perihal_text = "Usul " . $nama_layanan . " Tahun " . $tahun_ini;
                    break;

                default:
                    // Fallback untuk kategori baru yg belum terdaftar
                    $perihal_text = "Usul " . $nama_layanan . " Tahun " . $tahun_ini;
                    break;
            }
        }


        $list_bulan = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
        ];

        // Mulai Query
        $query = Pengajuan::with(['pegawai', 'jenisLayanan'])
            ->whereYear('created_at', $tahun_ini)
            ->whereRaw("LOWER(status) = 'pending'");

        if ($jenis_layanan_id) {
            $query->where('jenis_layanan_id', $jenis_layanan_id);
        }

        // Filter Bulan (Jika user memilih bulan di dropdown)
        if (!empty($input_periode) && array_key_exists($input_periode, $list_bulan)) {
            $query->whereMonth('created_at', $list_bulan[$input_periode]);
        }

        // Eksekusi Query
        $data_pengajuan = $query->get();


        $list_pegawai = $data_pengajuan->map(function ($item) {
            return (object) [
                'nama' => $item->pegawai->nama_lengkap ?? 'Nama Tidak Ditemukan',
                'nip'  => $item->pegawai->nip ?? '-',
                'jabatan_pegawai' => $item->pegawai->jabatan ?? '-'
            ];
        });

        $jumlah_berkas = $list_pegawai->count();

        $angka_huruf = [
            0 => 'Nol', 1 => 'Satu', 2 => 'Dua', 3 => 'Tiga', 4 => 'Empat',
            5 => 'Lima', 6 => 'Enam', 7 => 'Tujuh', 8 => 'Delapan', 9 => 'Sembilan',
            10 => 'Sepuluh', 11 => 'Sebelas', 12 => 'Dua Belas'
        ];

        $terbilang = $angka_huruf[$jumlah_berkas] ?? $jumlah_berkas;

        $banyaknya_berkas = "$jumlah_berkas ($terbilang) Berkas";


        $kepala_kantor = Pegawai::where('jabatan', 'LIKE', '%Kepala Kantor%')->first();

        $nama_kepala = $kepala_kantor ? $kepala_kantor->nama_lengkap : 'Null';
        $nip_kepala  = $kepala_kantor ? $kepala_kantor->nip : 'Null';

        $status_kepala = $request->input('status_kepala');
        $jabatan_signer = 'Kepala Kantor';

        if ($status_kepala == 'plt') $jabatan_signer = 'Plt. Kepala Kantor';
        if ($status_kepala == 'plh') $jabatan_signer = 'Plh. Kepala Kantor';

        // ==========================================
        // 7. RENDER PDF
        // ==========================================
        $data = [
            'nama_surat'       => 'Surat Pengantar',
            'nomor_surat'      => $request->nomor_surat,
            'date'             => $tanggal_surat,
            'logo_base64'      => $logo_base64,
            'perihal'          => $perihal_text,
            'banyaknya_berkas' => $banyaknya_berkas,
            'keterangan'       => 'Dikirim dengan hormat, untuk diproses lebih lanjut. Terima Kasih',
            'list_pegawai'     => $list_pegawai,
            'signer'           => (object)[
                'jabatan' => $jabatan_signer,
                'name'    => $nama_kepala,
                'nip'     => $nip_kepala
            ]
        ];

        $pdf = Pdf::loadView('pages.admin.cetak_surat.template_pengantar', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Surat_Pengantar.pdf');
    }

    public function storeArsip(Request $request)
    {
        $messages = [
            'jenis_layanan_id.required' => 'Perihal/Kategori wajib dipilih.',
            'jenis_layanan_id.exists'   => 'Kategori layanan tidak valid.',
            'dokumen.required'          => 'File dokumen wajib diupload.',
            'dokumen.mimes'             => 'Format file harus PDF.',
            'dokumen.max'               => 'Ukuran file maksimal 2MB.',
            'jenis_dokumen.required'    => 'Jenis dokumen tidak terdeteksi.',
        ];

        $validator = Validator::make($request->all(), [
            'jenis_layanan_id' => 'required|exists:jenis_layanans,id',
            'periode'          => 'nullable',
            'dokumen'          => 'required|mimes:pdf|max:2048',
            'jenis_dokumen'    => 'required'
        ], $messages);
;
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal mengupload! Silakan periksa inputan Anda.');
        }

        DB::beginTransaction();
        $path = null;

        try {
            // Proses Upload File
            if ($request->hasFile('dokumen')) {
                $file = $request->file('dokumen');
                $originalName = $file->getClientOriginalName();
                $filename = time() . '_' . $originalName;

                $path = Storage::disk('public')->putFileAs('arsip', $file, $filename);

            } else {
                throw new \Exception('File tidak ditemukan saat proses upload.');
            }

            ArsipSurat::create([
                'jenis_dokumen'    => $request->jenis_dokumen,
                'jenis_layanan_id' => $request->jenis_layanan_id,
                'periode'          => $request->periode,
                'file_path'        => $path,
                'file_name'        => $originalName
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Dokumen berhasil diarsipkan!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($path && Storage::exists($path)) {
                Storage::delete($path);
            }
            Log::error('Gagal Upload Arsip: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function updateArsip(Request $request, $id)
    {
        // 1. Cari Data Arsip
        $arsip = ArsipSurat::findOrFail($id);

        // 2. Validasi
        $validator = Validator::make($request->all(), [
            'jenis_layanan_id' => 'required|exists:jenis_layanans,id',
            'periode'          => 'nullable',
            'dokumen'          => 'nullable|mimes:pdf|max:2048',
        ], [
            'jenis_layanan_id.required' => 'Jenis layanan wajib dipilih.',
            'jenis_layanan_id.exists'   => 'Jenis layanan yang dipilih tidak valid.',

            'dokumen.mimes' => 'Dokumen harus berupa file PDF.',
            'dokumen.max'   => 'Ukuran dokumen maksimal 2 MB.',
        ]);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'update') // Bagian 'update' agar error muncul di modal edit (opsional)
                ->withInput()
                ->with('error', 'Gagal update! Periksa inputan.');
        }

        DB::beginTransaction();

        try {
            // 3. Cek apakah ada file baru diupload
            if ($request->hasFile('dokumen')) {
                // Hapus file lama fisik
                if ($arsip->file_path && Storage::disk('public')->exists($arsip->file_path)) {
                    Storage::disk('public')->delete($arsip->file_path);
                }

                // Upload file baru
                $file = $request->file('dokumen');
                $originalName = $file->getClientOriginalName();
                $filename = time() . '_' . $originalName;

                // Simpan path baru
                $path = Storage::disk('public')->putFileAs('arsip', $file, $filename);

                // Update nama file & path di object
                $arsip->file_path = $path;
                $arsip->file_name = $originalName;
            }

            // 4. Update Data Lainnya
            $arsip->jenis_layanan_id = $request->jenis_layanan_id;
            $arsip->periode = $request->periode;
            $arsip->save();

            DB::commit();

            return redirect()->back()->with('success', 'Arsip berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal Update Arsip: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function sptjm(Request $request)
    {
        // 1. DATA DINAMIS PEGAWAI (Filter Status Disetujui)
        // Kita gunakan whereIn untuk menangkap berbagai kemungkinan penulisan status di database
        $data_pegawai = Pengajuan::with(['pegawai', 'jenisLayanan'])
            ->whereRaw("LOWER(status) IN ('disetujui', 'approved', 'selesai')") // <--- HANYA DATA DISETUJUI
            ->latest() // Urutkan dari yang terbaru disetujui
            ->paginate(10)
            ->through(function ($item) {
                return (object) [
                    'id'      => $item->id,
                    'nama'    => $item->pegawai->nama_lengkap ?? '-',
                    'nip'     => $item->pegawai->nip ?? '-',
                    'jabatan' => $item->pegawai->jabatan ?? '-',
                    'perihal' => $item->jenisLayanan->nama_layanan ?? '-'
                ];
            });

        // 2. DATA SIDEBAR (Template Arsip SPTJM)
        $templates = ArsipSurat::with('jenisLayanan')
            ->where('jenis_dokumen', 'SPTJM')
            ->latest()
            ->limit(5)
            ->get();

        // 3. DROPDOWN LAYANAN (Untuk Modal)
        $jenis_layanan = JenisLayanan::where('is_active', true)->get();

        return view('pages.admin.cetak_surat.sptjm', compact('data_pegawai', 'templates', 'jenis_layanan'));
    }

    public function exportSptjm(Request $request)
    {
        // 1. SETUP LOGO & TANGGAL
        $path = public_path('assets/logo_kantor.png');
        $logo_base64 = '';
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $logo_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        // Format Tanggal Indo (04 Desember 2025)
        $tanggal_surat = Carbon::now()->isoFormat('D MMMM Y');
        $tahun_ini = Carbon::now()->year;

        // 2. TANGKAP INPUT FORM
        $jenis_layanan_id = $request->input('jenis_layanan_id');
        $input_periode    = strtolower($request->input('periode')); // januari...

        // 3. LOGIKA JUDUL PERIHAL (Sama seperti Pengantar)
        $layanan = JenisLayanan::find($jenis_layanan_id);
        $perihal_text = "Usulan"; // Default

        if ($layanan) {
            $kategori = $layanan->kategori;
            $nama_layanan = $layanan->nama_layanan;

            switch ($kategori) {
                case 'Kenaikan Pangkat':
                    $periode_text = $input_periode ? ucfirst($input_periode) : '';
                    $perihal_text = "Usulan Kenaikan Pangkat Periode $periode_text $tahun_ini";
                    break;
                case 'Pensiun':
                    $perihal_text = "Usulan " . $nama_layanan;
                    break;
                default:
                    $perihal_text = "Usulan " . $nama_layanan;
                    break;
            }
        }

        // 4. QUERY DATA PEGAWAI
        // Filter: Status Pending + ID Layanan + Bulan (Opsional)
        $list_bulan = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
        ];

        $query = Pengajuan::with(['pegawai', 'jenisLayanan'])
            ->whereYear('created_at', $tahun_ini)
            ->whereRaw("LOWER(status) = 'disetujui'");

        if ($jenis_layanan_id) {
            $query->where('jenis_layanan_id', $jenis_layanan_id);
        }

        if (!empty($input_periode) && array_key_exists($input_periode, $list_bulan)) {
            $query->whereMonth('created_at', $list_bulan[$input_periode]);
        }

        $data_pengajuan = $query->get();

        // 5. MAPPING DATA (Termasuk Golongan)
        $list_pegawai = $data_pengajuan->map(function ($item) {
            $pegawai = $item->pegawai;

            // 1. Golongan Awal (Dari Tabel Pegawai)
            // Format: Pangkat (Golongan) -> Contoh: Penata (III/c)
            $pangkat_awal = $pegawai->pangkat ?? '';
            $gol_ruang_awal = $pegawai->golongan_ruang ?? '';
            $gol_awal = $pangkat_awal . ' (' . $gol_ruang_awal . ')';

            $gol_usulan_kode = data_get($item->data_tambahan, 'golongan_ruang', '-');

            $gol_usulan = $gol_usulan_kode;

            return (object) [
                'nama' => $pegawai->nama_lengkap ?? 'Nama Tidak Ditemukan',
                'nip'  => $pegawai->nip ?? '-',
                'jabatan_pegawai' => $pegawai->jabatan ?? '-',

                'golongan_awal'   => $gol_awal,
                'golongan_usulan' => $gol_usulan
            ];
        });

        // 6. DATA PENANDATANGAN
        $kepala_kantor = Pegawai::where('jabatan', 'LIKE', '%Kepala Kantor%')->first();
        $nama_kepala = $kepala_kantor ? $kepala_kantor->nama_lengkap : 'H. NUR ALIM M. SUMA';
        $nip_kepala  = $kepala_kantor ? $kepala_kantor->nip : '19900101 202501 1 001';

        $status_kepala = $request->input('status_kepala');
        $jabatan_signer = 'Kepala Kantor Kementerian Agama Kota Gorontalo'; // Default Panjang sesuai gambar

        if ($status_kepala == 'plt') $jabatan_signer = 'Plt. Kepala Kantor Kementerian Agama Kota Gorontalo';
        if ($status_kepala == 'plh') $jabatan_signer = 'Plh. Kepala Kantor Kementerian Agama Kota Gorontalo';

        // 7. RENDER PDF
        $data = [
            'nama_surat'     => 'SPTJM',
            'nomor_surat'    => $request->nomor_surat,
            'logo_base64'    => $logo_base64,
            'perihal'        => $perihal_text,
            'list_pegawai'   => $list_pegawai,

            // Lokasi & Tanggal di kanan bawah
            'lokasi_tanggal' => 'Kecamatan Kota Tengah, ' . $tanggal_surat,

            'signer'         => (object)[
                'jabatan' => $jabatan_signer,
                'name'    => $nama_kepala,
                'nip'     => $nip_kepala
            ]
        ];

        $pdf = Pdf::loadView('pages.admin.cetak_surat.template_sptjm', $data);
        $pdf->setPaper('A4', 'portrait'); // Bisa ganti 'legal' jika tabelnya panjang

        return $pdf->stream('SPTJM.pdf');
    }
}
