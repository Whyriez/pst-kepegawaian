<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisLayanan;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PindahInstansiController extends Controller
{
    public function masuk(Request $request)
    {
        // 1. Ambil ID Layanan
        $layanan = JenisLayanan::where('slug', 'pindah-masuk')->first();

        if (!$layanan) {
            return back()->with('error', 'Layanan Pindah Masuk belum dikonfigurasi.');
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
                    ->orWhere('nip', 'like', '%' . $keyword . '%');
            });
        }

        // 6. Pagination
        $pengajuan = $query->latest('tanggal_pengajuan')->paginate(10);

        return view('pages.admin.pindah_antar_instansi.masuk', compact('pengajuan', 'stats'));
    }

    public function keluar(Request $request)
    {
        // 1. Ambil ID Layanan
        $layanan = JenisLayanan::where('slug', 'pindah-keluar')->first();

        if (!$layanan) {
            return back()->with('error', 'Layanan Pindah Keluar belum dikonfigurasi.');
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
                    ->orWhere('nip', 'like', '%' . $keyword . '%');
            });
        }

        // 6. Pagination
        $pengajuan = $query->latest('tanggal_pengajuan')->paginate(10);

        return view('pages.admin.pindah_antar_instansi.keluar', compact('pengajuan', 'stats'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|exists:pengajuans,id']);

        Pengajuan::where('id', $request->id)->update([
            'status' => 'disetujui',
            'verifikator_id' => Auth::id(),
            'tanggal_verifikasi' => now(),
            'catatan_admin' => null
        ]);

        return back()->with('success', 'Pengajuan Pindah Masuk disetujui.');
    }

    // --- POSTPONE ---
    public function postpone(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:pengajuans,id',
            'prioritas' => 'required',
            'tanggal_tindak_lanjut' => 'required|date',
            'alasan' => 'required|string',
        ]);

        Pengajuan::where('id', $request->id)->update([
            'status' => 'ditunda',
            'verifikator_id' => Auth::id(),
            'prioritas' => $request->prioritas,
            'tanggal_tindak_lanjut' => $request->tanggal_tindak_lanjut,
            'catatan_admin' => $request->alasan,
        ]);

        return back()->with('success', 'Pengajuan ditunda.');
    }

    // --- REJECT ---
    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:pengajuans,id',
            'kategori' => 'required',
            'alasan' => 'required|string',
        ]);

        $pengajuan = Pengajuan::findOrFail($request->id);

        $dataTambahan = $pengajuan->data_tambahan ?? [];
        $dataTambahan['kategori_penolakan'] = $request->kategori;

        $pengajuan->update([
            'status' => 'ditolak',
            'verifikator_id' => Auth::id(),
            'tanggal_verifikasi' => now(),
            'catatan_admin' => $request->alasan,
            'data_tambahan' => $dataTambahan,
        ]);

        return back()->with('success', 'Pengajuan ditolak.');
    }
}
