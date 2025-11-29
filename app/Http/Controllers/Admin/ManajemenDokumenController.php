<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisLayanan;
use App\Models\SyaratDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ManajemenDokumenController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil List Kategori Unik untuk Filter Dropdown
        $kategori_list = JenisLayanan::select('kategori')
            ->distinct()
            ->orderBy('kategori', 'asc')
            ->pluck('kategori');

        // 2. Mulai Query Utama
        $query = JenisLayanan::withCount('syaratDokumens')->latest();

        // 3. Logika Search (Nama Layanan)
        if ($request->filled('search')) {
            $query->where('nama_layanan', 'like', '%' . $request->search . '%');
        }

        // 4. Logika Filter Kategori (Sekarang Exact Match karena pakai Dropdown)
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // 5. Pagination
        $layanan = $query->paginate(10)->withQueryString();

        return view('pages.admin.dokumen.index', compact('layanan', 'kategori_list'));
    }

    public function storeLayanan(Request $request)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'kategori'     => 'required|string|max:100', // Validasi string biasa
        ]);

        JenisLayanan::create([
            'nama_layanan' => $request->nama_layanan,
            'slug'         => Str::slug($request->nama_layanan),
            'kategori'     => $request->kategori, // Simpan input teks langsung
            'is_active'    => true,
        ]);

        return back()->with('success', 'Jenis Layanan berhasil ditambahkan!');
    }

    public function updateLayanan(Request $request, $id)
    {
        $layanan = JenisLayanan::findOrFail($id);

        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'kategori'     => 'required|string|max:100',
        ]);

        $layanan->update([
            'nama_layanan' => $request->nama_layanan,
            'slug'         => Str::slug($request->nama_layanan),
            'kategori'     => $request->kategori,
        ]);

        return back()->with('success', 'Jenis Layanan berhasil diperbarui!');
    }

    public function destroyLayanan($id)
    {
        JenisLayanan::findOrFail($id)->delete();
        return back()->with('success', 'Jenis Layanan berhasil dihapus!');
    }


    // ==========================
    // BAGIAN 2: SYARAT DOKUMEN
    // ==========================

    public function showSyarat($id)
    {
        // Ambil layanan spesifik dan syarat dokumennya
        $layanan = JenisLayanan::with('syaratDokumens')->findOrFail($id);
        return view('pages.admin.dokumen.syarat', compact('layanan'));
    }

    public function storeSyarat(Request $request)
    {
        $request->validate([
            'jenis_layanan_id' => 'required|exists:jenis_layanans,id',
            'nama_dokumen'     => 'required|string',
            'allowed_types'    => 'required|string', // ex: pdf,jpg
            'max_size_kb'      => 'required|integer|min:100',
        ]);

        SyaratDokumen::create([
            'jenis_layanan_id' => $request->jenis_layanan_id,
            'nama_dokumen'     => $request->nama_dokumen,
            'is_required'      => $request->has('is_required'), // checkbox
            'allowed_types'    => $request->allowed_types,
            'max_size_kb'      => $request->max_size_kb,
        ]);

        return back()->with('success', 'Syarat Dokumen berhasil ditambahkan!');
    }

    public function updateSyarat(Request $request, $id)
    {
        $syarat = SyaratDokumen::findOrFail($id);

        $request->validate([
            'nama_dokumen'  => 'required|string',
            'allowed_types' => 'required|string',
            'max_size_kb'   => 'required|integer',
        ]);

        $syarat->update([
            'nama_dokumen'  => $request->nama_dokumen,
            'is_required'   => $request->has('is_required'),
            'allowed_types' => $request->allowed_types,
            'max_size_kb'   => $request->max_size_kb,
        ]);

        return back()->with('success', 'Syarat Dokumen berhasil diperbarui!');
    }

    public function destroySyarat($id)
    {
        SyaratDokumen::findOrFail($id)->delete();
        return back()->with('success', 'Syarat Dokumen berhasil dihapus!');
    }
}
