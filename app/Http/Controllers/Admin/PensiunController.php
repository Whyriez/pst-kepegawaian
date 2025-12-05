<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisLayanan;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PensiunController extends Controller
{
    public function aps(Request $request)
    {
        // 1. Ambil ID Jenis Layanan (Sesuaikan slug dengan database)
        $layanan = JenisLayanan::where('slug', 'pensiun-aps')->first();

        if (!$layanan) {
            return back()->with('error', 'Layanan Pensiun APS belum dikonfigurasi.');
        }

        // 2. Query Dasar
        $query = Pengajuan::with(['pegawai', 'dokumenPengajuans.syaratDokumen'])
            ->where('jenis_layanan_id', $layanan->id);

        // 3. Statistik
        $stats = [
            'total' => $query->count(),
            'pending' => $query->clone()->where('status', 'pending')->count(),
            'disetujui' => $query->clone()->where('status', 'disetujui')->count(),
            'ditolak' => $query->clone()->where('status', 'ditolak')->count(),
        ];

        // 4. Filter Status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // 5. Pencarian
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->whereHas('pegawai', function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', '%' . $keyword . '%')
                    ->orWhere('nip', 'like', '%' . $keyword . '%')
                    ->orWhere('nomor_tiket', 'like', '%' . $keyword . '%');
            });
        }

        // 6. Pagination
        $pengajuan = $query->latest('tanggal_pengajuan')->paginate(10);

        // Pastikan nama view sesuai dengan struktur folder Anda
        return view('pages.admin.pensiun.atas_permintaan_sendiri', compact('pengajuan', 'stats'));
    }

    public function bup(Request $request)
    {
        // 1. Ambil ID Jenis Layanan 'pensiun-bup'
        // PENTING: Pastikan slug di database adalah 'pensiun-bup'
        $layanan = JenisLayanan::where('slug', 'pensiun-bup')->first();

        if (!$layanan) {
            return back()->with('error', 'Layanan Pensiun BUP belum dikonfigurasi di database.');
        }

        // 2. Query Dasar (Relation ke Pegawai & Dokumen)
        $query = Pengajuan::with(['pegawai', 'dokumenPengajuans.syaratDokumen'])
            ->where('jenis_layanan_id', $layanan->id);

        // 3. Statistik Real-time (Untuk Card Dashboard)
        $stats = [
            'total' => $query->count(),
            'pending' => $query->clone()->where('status', 'pending')->count(),
            'disetujui' => $query->clone()->where('status', 'disetujui')->count(),
            'ditolak' => $query->clone()->where('status', 'ditolak')->count(),
        ];

        // 4. Filter Status (Dari Dropdown/URL)
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // 5. Fitur Pencarian (Nama Pegawai atau NIP)
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->whereHas('pegawai', function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', '%' . $keyword . '%')
                    ->orWhere('nip', 'like', '%' . $keyword . '%')
                    ->orWhere('nomor_tiket', 'like', '%' . $keyword . '%');
            });
        }

        // 6. Ambil Data (Pagination)
        $pengajuan = $query->latest('tanggal_pengajuan')->paginate(10);

        return view('pages.admin.pensiun.batas_usia_pensiun', compact('pengajuan', 'stats'));
    }

    public function hilang(Request $request)
    {
        // 1. Ambil ID Jenis Layanan (Sesuaikan slug)
        $layanan = JenisLayanan::where('slug', 'pensiun-hilang')->first();

        if (!$layanan) {
            return back()->with('error', 'Layanan Pensiun Hilang belum dikonfigurasi.');
        }

        // 2. Query Dasar
        $query = Pengajuan::with(['pegawai', 'dokumenPengajuans.syaratDokumen'])
            ->where('jenis_layanan_id', $layanan->id);

        // 3. Statistik
        $stats = [
            'total' => $query->count(),
            'pending' => $query->clone()->where('status', 'pending')->count(),
            'disetujui' => $query->clone()->where('status', 'disetujui')->count(),
            'ditolak' => $query->clone()->where('status', 'ditolak')->count(),
        ];

        // 4. Filter Status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // 5. Pencarian
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->whereHas('pegawai', function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', '%' . $keyword . '%')
                    ->orWhere('nip', 'like', '%' . $keyword . '%')
                    ->orWhere('nomor_tiket', 'like', '%' . $keyword . '%');
            });
        }

        // 6. Pagination
        $pengajuan = $query->latest('tanggal_pengajuan')->paginate(10);

        return view('pages.admin.pensiun.hilang', compact('pengajuan', 'stats'));
    }

    public function jandaDudaYatim(Request $request)
    {
        // 1. Ambil ID Jenis Layanan (Sesuaikan slug dengan database Anda)
        $layanan = JenisLayanan::where('slug', 'pensiun-jdy')->first();

        if (!$layanan) {
            return back()->with('error', 'Layanan Pensiun Janda/Duda/Yatim belum dikonfigurasi.');
        }

        // 2. Query Dasar
        $query = Pengajuan::with(['pegawai', 'dokumenPengajuans.syaratDokumen'])
            ->where('jenis_layanan_id', $layanan->id);

        // 3. Statistik (Clone query agar tidak merusak query utama)
        $stats = [
            'total' => $query->count(),
            'pending' => $query->clone()->where('status', 'pending')->count(),
            'disetujui' => $query->clone()->where('status', 'disetujui')->count(),
            'ditolak' => $query->clone()->where('status', 'ditolak')->count(),
        ];

        // 4. Filter Status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // 5. Pencarian
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->whereHas('pegawai', function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', '%' . $keyword . '%')
                    ->orWhere('nip', 'like', '%' . $keyword . '%')
                    ->orWhere('nomor_tiket', 'like', '%' . $keyword . '%');
            });
        }

        // 6. Pagination
        $pengajuan = $query->latest('tanggal_pengajuan')->paginate(10);

        return view('pages.admin.pensiun.janda_duda_yatim', compact('pengajuan', 'stats'));
    }

    public function meninggal(Request $request)
    {
        // 1. Ambil ID Jenis Layanan (Sesuaikan slug)
        $layanan = JenisLayanan::where('slug', 'pensiun-meninggal')->first();

        if (!$layanan) {
            return back()->with('error', 'Layanan Pensiun Meninggal belum dikonfigurasi.');
        }

        // 2. Query Dasar
        $query = Pengajuan::with(['pegawai', 'dokumenPengajuans.syaratDokumen'])
            ->where('jenis_layanan_id', $layanan->id);

        // 3. Statistik
        $stats = [
            'total' => $query->count(),
            'pending' => $query->clone()->where('status', 'pending')->count(),
            'disetujui' => $query->clone()->where('status', 'disetujui')->count(),
            'ditolak' => $query->clone()->where('status', 'ditolak')->count(),
        ];

        // 4. Filter Status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // 5. Pencarian
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->whereHas('pegawai', function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', '%' . $keyword . '%')
                    ->orWhere('nip', 'like', '%' . $keyword . '%')
                    ->orWhere('nomor_tiket', 'like', '%' . $keyword . '%');
            });
        }

        // 6. Pagination
        $pengajuan = $query->latest('tanggal_pengajuan')->paginate(10);

        return view('pages.admin.pensiun.meninggal', compact('pengajuan', 'stats'));
    }

    public function tanpaAhliWaris(Request $request)
    {
        // 1. Ambil ID Jenis Layanan (Sesuaikan slug)
        $layanan = JenisLayanan::where('slug', 'pensiun-taw')->first();

        if (!$layanan) {
            return back()->with('error', 'Layanan Pensiun Tanpa Ahli Waris belum dikonfigurasi.');
        }

        // 2. Query Dasar
        $query = Pengajuan::with(['pegawai', 'dokumenPengajuans.syaratDokumen'])
            ->where('jenis_layanan_id', $layanan->id);

        // 3. Statistik
        $stats = [
            'total' => $query->count(),
            'pending' => $query->clone()->where('status', 'pending')->count(),
            'disetujui' => $query->clone()->where('status', 'disetujui')->count(),
            'ditolak' => $query->clone()->where('status', 'ditolak')->count(),
        ];

        // 4. Filter Status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // 5. Pencarian
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->whereHas('pegawai', function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', '%' . $keyword . '%')
                    ->orWhere('nip', 'like', '%' . $keyword . '%')
                    ->orWhere('nomor_tiket', 'like', '%' . $keyword . '%');
            });
        }

        // 6. Pagination
        $pengajuan = $query->latest('tanggal_pengajuan')->paginate(10);

        return view('pages.admin.pensiun.tanpa_ahli_waris', compact('pengajuan', 'stats'));
    }

    public function uzur(Request $request)
    {
        // 1. Ambil ID Jenis Layanan (Sesuaikan slug)
        $layanan = JenisLayanan::where('slug', 'pensiun-uzur')->first();

        if (!$layanan) {
            return back()->with('error', 'Layanan Pensiun Uzur belum dikonfigurasi.');
        }

        // 2. Query Dasar
        $query = Pengajuan::with(['pegawai', 'dokumenPengajuans.syaratDokumen'])
            ->where('jenis_layanan_id', $layanan->id);

        // 3. Statistik
        $stats = [
            'total' => $query->count(),
            'pending' => $query->clone()->where('status', 'pending')->count(),
            'disetujui' => $query->clone()->where('status', 'disetujui')->count(),
            'ditolak' => $query->clone()->where('status', 'ditolak')->count(),
        ];

        // 4. Filter Status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // 5. Pencarian
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->whereHas('pegawai', function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', '%' . $keyword . '%')
                    ->orWhere('nip', 'like', '%' . $keyword . '%')
                    ->orWhere('nomor_tiket', 'like', '%' . $keyword . '%');
            });
        }

        // 6. Pagination
        $pengajuan = $query->latest('tanggal_pengajuan')->paginate(10);

        return view('pages.admin.pensiun.uzur', compact('pengajuan', 'stats'));
    }


    public function approve(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:pengajuans,id',
        ], [
            'id.required' => 'ID pengajuan wajib diisi.',
            'id.exists' => 'ID pengajuan tidak ditemukan atau tidak valid.',
        ]);


        $pengajuan = Pengajuan::findOrFail($request->id);

        $pengajuan->update([
            'status' => 'disetujui',
            'verifikator_id' => Auth::id(),
            'tanggal_verifikasi' => now(),
            'catatan_admin' => null, // Hapus catatan revisi jika ada
        ]);

        return back()->with('success', 'Pengajuan Pensiun berhasil disetujui.');
    }

    // --- LOGIKA POSTPONE (TUNDA) ---
    public function postpone(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:pengajuans,id',
            'prioritas' => 'required',
            'tanggal_tindak_lanjut' => 'required|date',
            'alasan' => 'required|string',
        ], [
            'id.required' => 'ID pengajuan wajib diisi.',
            'id.exists' => 'ID pengajuan tidak ditemukan atau tidak valid.',

            'prioritas.required' => 'Prioritas wajib dipilih.',

            'tanggal_tindak_lanjut.required' => 'Tanggal tindak lanjut wajib diisi.',
            'tanggal_tindak_lanjut.date' => 'Tanggal tindak lanjut harus berupa format tanggal yang valid.',

            'alasan.required' => 'Alasan wajib diisi.',
            'alasan.string' => 'Alasan harus berupa teks.',
        ]);


        $pengajuan = Pengajuan::findOrFail($request->id);

        $pengajuan->update([
            'status' => 'ditunda',
            'verifikator_id' => Auth::id(),
            'prioritas' => $request->prioritas,
            'tanggal_tindak_lanjut' => $request->tanggal_tindak_lanjut,
            'catatan_admin' => $request->alasan,
        ]);

        return back()->with('success', 'Pengajuan Pensiun berhasil ditunda.');
    }

    // --- LOGIKA REJECT (TOLAK) ---
    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:pengajuans,id',
            'kategori' => 'required',
            'alasan' => 'required|string',
        ], [
            'id.required' => 'ID pengajuan wajib diisi.',
            'id.exists' => 'ID pengajuan tidak ditemukan atau tidak valid.',

            'kategori.required' => 'Kategori wajib dipilih.',

            'alasan.required' => 'Alasan wajib diisi.',
            'alasan.string' => 'Alasan harus berupa teks.',
        ]);


        $pengajuan = Pengajuan::findOrFail($request->id);

        // Simpan kategori penolakan ke JSON data_tambahan
        $dataTambahan = $pengajuan->data_tambahan ?? [];
        $dataTambahan['kategori_penolakan'] = $request->kategori;

        $pengajuan->update([
            'status' => 'ditolak',
            'verifikator_id' => Auth::id(),
            'tanggal_verifikasi' => now(),
            'catatan_admin' => $request->alasan,
            'data_tambahan' => $dataTambahan,
        ]);

        return back()->with('success', 'Pengajuan Pensiun berhasil ditolak.');
    }
}
