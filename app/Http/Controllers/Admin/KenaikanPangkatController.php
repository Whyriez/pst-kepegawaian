<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisLayanan;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KenaikanPangkatController extends Controller
{
    public function fungsional(Request $request)
    {
        // 1. Ambil ID Jenis Layanan 'kp-fungsional'
        // Pastikan Anda sudah seeder database JenisLayanan dengan slug 'kp-fungsional'
        $layanan = JenisLayanan::where('slug', 'kp-fungsional')->first();

        if (!$layanan) {
            return back()->with('error', 'Layanan Kenaikan Pangkat Fungsional belum dikonfigurasi.');
        }

        // 2. Query Dasar
        $query = Pengajuan::with(['pegawai', 'dokumenPengajuans.syaratDokumen'])
            ->where('jenis_layanan_id', $layanan->id);

        // 3. Hitung Statistik (Sebelum Filter Search & Status diterapkan untuk Card Dashboard)
        // Kita gunakan clone() agar query utama tidak terganggu
        $stats = [
            'total'     => $query->count(),
            'pending'   => $query->clone()->where('status', 'pending')->count(),
            'disetujui' => $query->clone()->where('status', 'disetujui')->count(),
            'ditolak'   => $query->clone()->where('status', 'ditolak')->count(),
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

        return view('pages.admin.kenaikan_pangkat.fungsional', compact('pengajuan', 'stats'));
    }

    public function approve(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:pengajuans,id',
        ], [
            'id.required' => 'ID pengajuan wajib diisi.',
            'id.exists'   => 'ID pengajuan tidak ditemukan atau tidak valid.',
        ]);


        $pengajuan = Pengajuan::findOrFail($request->id);

        $pengajuan->update([
            'status' => 'disetujui',
            'verifikator_id' => Auth::id(), // Siapa admin yang menyetujui
            'tanggal_verifikasi' => now(),
            'catatan_admin' => null, // Bersihkan catatan jika sebelumnya ada revisi
        ]);

        return back()->with('success', 'Pengajuan berhasil disetujui.');
    }

    // 2. LOGIKA TUNDA
    public function postpone(Request $request)
    {
        $request->validate([
            'id'                    => 'required|exists:pengajuans,id',
            'prioritas'             => 'required',
            'tanggal_tindak_lanjut' => 'required|date',
            'alasan'                => 'required|string',
        ], [
            'id.required'    => 'ID pengajuan wajib diisi.',
            'id.exists'      => 'ID pengajuan tidak ditemukan atau tidak valid.',

            'prioritas.required' => 'Prioritas wajib dipilih.',

            'tanggal_tindak_lanjut.required' => 'Tanggal tindak lanjut wajib diisi.',
            'tanggal_tindak_lanjut.date'     => 'Tanggal tindak lanjut harus berupa format tanggal yang valid.',

            'alasan.required' => 'Alasan wajib diisi.',
            'alasan.string'   => 'Alasan harus berupa teks.',
        ]);


        $pengajuan = Pengajuan::findOrFail($request->id);

        $pengajuan->update([
            'status' => 'ditunda',
            'verifikator_id' => Auth::id(),
            'prioritas' => $request->prioritas,
            'tanggal_tindak_lanjut' => $request->tanggal_tindak_lanjut,
            'catatan_admin' => $request->alasan, // Simpan alasan penundaan
        ]);

        return back()->with('success', 'Pengajuan berhasil ditunda.');
    }

    // 3. LOGIKA TOLAK
    public function reject(Request $request)
    {
        $request->validate([
            'id'       => 'required|exists:pengajuans,id',
            'kategori' => 'required',
            'alasan'   => 'required|string',
        ], [
            'id.required'  => 'ID pengajuan wajib diisi.',
            'id.exists'    => 'ID pengajuan tidak ditemukan atau tidak valid.',

            'kategori.required' => 'Kategori wajib dipilih.',

            'alasan.required' => 'Alasan wajib diisi.',
            'alasan.string'   => 'Alasan harus berupa teks.',
        ]);


        $pengajuan = Pengajuan::findOrFail($request->id);

        // Kita simpan kategori penolakan ke dalam JSON data_tambahan agar tidak perlu ubah struktur tabel
        $dataTambahan = $pengajuan->data_tambahan ?? [];
        $dataTambahan['kategori_penolakan'] = $request->kategori;

        $pengajuan->update([
            'status' => 'ditolak',
            'verifikator_id' => Auth::id(),
            'tanggal_verifikasi' => now(),
            'catatan_admin' => $request->alasan,
            'data_tambahan' => $dataTambahan,
        ]);

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }

    public function penyesuaianIjazah(Request $request)
    {
        // 1. Ambil Layanan PI
        $layanan = JenisLayanan::where('slug', 'kp-pi')->first();

        if (!$layanan) {
            return back()->with('error', 'Layanan KP Penyesuaian Ijazah belum diset.');
        }

        // 2. Query Data
        $query = Pengajuan::with(['pegawai', 'dokumenPengajuans.syaratDokumen'])
            ->where('jenis_layanan_id', $layanan->id);

        // 3. Statistik
        $stats = [
            'total'     => $query->count(),
            'pending'   => $query->clone()->where('status', 'pending')->count(),
            'disetujui' => $query->clone()->where('status', 'disetujui')->count(),
            'ditolak'   => $query->clone()->where('status', 'ditolak')->count(),
        ];

        // 4. Filter Status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // 5. Search
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->whereHas('pegawai', function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', '%' . $keyword . '%')
                    ->orWhere('nip', 'like', '%' . $keyword . '%')
                    ->orWhere('nomor_tiket', 'like', '%' . $keyword . '%');
            });
        }

        $pengajuan = $query->latest('tanggal_pengajuan')->paginate(10);

        return view('pages.admin.kenaikan_pangkat.penyesuaian_ijazah', compact('pengajuan', 'stats'));
    }

    public function reguler(Request $request)
    {
        // 1. Ambil Layanan Reguler
        // Pastikan di database tabel jenis_layanans ada slug 'kp-reguler'
        $layanan = JenisLayanan::where('slug', 'kp-reguler')->first();

        if (!$layanan) {
            return back()->with('error', 'Layanan KP Reguler belum dikonfigurasi.');
        }

        // 2. Query Dasar
        $query = Pengajuan::with(['pegawai', 'dokumenPengajuans.syaratDokumen'])
            ->where('jenis_layanan_id', $layanan->id);

        // 3. Statistik
        $stats = [
            'total'     => $query->count(),
            'pending'   => $query->clone()->where('status', 'pending')->count(),
            'disetujui' => $query->clone()->where('status', 'disetujui')->count(),
            'ditolak'   => $query->clone()->where('status', 'ditolak')->count(),
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

        return view('pages.admin.kenaikan_pangkat.reguler', compact('pengajuan', 'stats'));
    }

    public function struktural(Request $request)
    {
        // 1. Ambil ID Layanan Struktural
        // Pastikan di database tabel jenis_layanans ada slug 'kp-struktural'
        $layanan = JenisLayanan::where('slug', 'kp-struktural')->first();

        if (!$layanan) {
            return back()->with('error', 'Layanan KP Struktural belum dikonfigurasi.');
        }

        // 2. Query Dasar
        $query = Pengajuan::with(['pegawai', 'dokumenPengajuans.syaratDokumen'])
            ->where('jenis_layanan_id', $layanan->id);

        // 3. Statistik Real-time
        $stats = [
            'total'     => $query->count(),
            'pending'   => $query->clone()->where('status', 'pending')->count(),
            'disetujui' => $query->clone()->where('status', 'disetujui')->count(),
            'ditolak'   => $query->clone()->where('status', 'ditolak')->count(),
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

        return view('pages.admin.kenaikan_pangkat.struktural', compact('pengajuan', 'stats'));
    }
}
