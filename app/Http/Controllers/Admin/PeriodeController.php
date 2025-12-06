<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisLayanan;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodeController extends Controller
{
    public function index()
    {
        // Ambil data, eager load jenisLayanan
        $rawPeriodes = Periode::with('jenisLayanan')->latest()->get();

        // Grouping berdasarkan string unik: "Kategori - Nama Periode"
        // Contoh key: "Kenaikan Pangkat - Periode April 2025"
        $periodes = $rawPeriodes->groupBy(function ($item) {
            return $item->jenisLayanan->kategori . '|' . $item->nama_periode;
        });

        $kategoris = JenisLayanan::select('kategori')->distinct()->pluck('kategori');

        return view('pages.admin.dokumen.periode', compact('periodes', 'kategoris'));
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $rules = [
            'kategori'     => 'required|string', // Inputnya sekarang Kategori
            'nama_periode' => 'required|string',
        ];

        // Jika TIDAK unlimited, maka tanggal wajib diisi
        if (!$request->has('is_unlimited')) {
            $rules['tanggal_mulai']   = 'required|date';
            $rules['tanggal_selesai'] = 'required|date|after_or_equal:tanggal_mulai';
        }

        $request->validate($rules);


        DB::beginTransaction();
        try {
            // 2. Ambil semua layanan berdasarkan kategori yang dipilih
            // Contoh: User pilih 'Kenaikan Pangkat', sistem ambil [KP Reguler, KP Fungsional, dll]
            $layananTarget = JenisLayanan::where('kategori', $request->kategori)
                ->where('is_active', true)
                ->get();

            if ($layananTarget->isEmpty()) {
                return back()->with('error', 'Tidak ada jenis layanan ditemukan untuk kategori ini.');
            }

            // 3. Looping untuk membuat periode bagi setiap layanan anak
            foreach ($layananTarget as $layanan) {
                Periode::create([
                    'jenis_layanan_id' => $layanan->id,
                    'nama_periode'     => $request->nama_periode,
                    'is_unlimited'     => $request->has('is_unlimited'),
                    // Jika unlimited, tanggal null. Jika tidak, ambil input
                    'tanggal_mulai'    => $request->has('is_unlimited') ? null : $request->tanggal_mulai,
                    'tanggal_selesai'  => $request->has('is_unlimited') ? null : $request->tanggal_selesai,
                    'is_active'        => true
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Periode berhasil dibuka untuk kategori ' . $request->kategori);

        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        // 1. Ambil data representatif (salah satu item dari grup ini)
        $periode = Periode::with('jenisLayanan')->findOrFail($id);

        // Simpan kunci pencarian lama sebelum di-update
        $oldNamaPeriode = $periode->nama_periode;
        $kategoriTarget = $periode->jenisLayanan->kategori;

        // 2. Validasi Input
        $rules = [
            'nama_periode' => 'required|string',
        ];

        if (!$request->has('is_unlimited')) {
            $rules['tanggal_mulai']   = 'required|date';
            $rules['tanggal_selesai'] = 'required|date|after_or_equal:tanggal_mulai';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            // 3. Cari SEMUA periode yang satu grup (Nama sama & Kategori sama)
            $siblings = Periode::where('nama_periode', $oldNamaPeriode)
                ->whereHas('jenisLayanan', function($q) use ($kategoriTarget) {
                    $q->where('kategori', $kategoriTarget);
                })
                ->get();

            // 4. Update Semuanya
            foreach ($siblings as $p) {
                $p->update([
                    'nama_periode'    => $request->nama_periode, // Nama baru
                    'is_unlimited'    => $request->has('is_unlimited'),
                    'tanggal_mulai'   => $request->has('is_unlimited') ? null : $request->tanggal_mulai,
                    'tanggal_selesai' => $request->has('is_unlimited') ? null : $request->tanggal_selesai,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Periode untuk kategori ' . $kategoriTarget . ' berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        // 1. Cari data pemicu
        $periode = Periode::with('jenisLayanan')->findOrFail($id);

        // 2. Ambil parameter kuncinya
        $kategoriTarget = $periode->jenisLayanan->kategori;
        $namaPeriodeTarget = $periode->nama_periode;

        // 3. Hapus SEMUA periode yang punya Nama sama DAN Kategori sama
        // (Misal: Hapus semua 'Periode April' milik 'Kenaikan Pangkat')
        $idsToDelete = Periode::where('nama_periode', $namaPeriodeTarget)
            ->whereHas('jenisLayanan', function($q) use ($kategoriTarget) {
                $q->where('kategori', $kategoriTarget);
            })
            ->pluck('id');

        Periode::destroy($idsToDelete);

        return redirect()->back()->with('success', 'Periode untuk kategori ' . $kategoriTarget . ' berhasil dihapus.');
    }
}
