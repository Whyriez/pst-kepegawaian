<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisLayanan;
use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    public function index()
    {
        // Tampilkan list periode, dikelompokkan by Layanan biar rapi
        $periodes = Periode::with('jenisLayanan')->latest()->get();
        $layanans = JenisLayanan::where('is_active', true)->get();

        return view('pages.admin.dokumen.periode', compact('periodes', 'layanans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_layanan_id' => 'required|exists:jenis_layanans,id',
            'nama_periode'     => 'required|string',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
        ], [
            // Jenis Layanan
            'jenis_layanan_id.required' => 'Jenis layanan wajib dipilih.',
            'jenis_layanan_id.exists'   => 'Jenis layanan yang dipilih tidak valid.',

            // Nama Periode
            'nama_periode.required' => 'Nama periode wajib diisi.',
            'nama_periode.string'   => 'Nama periode harus berupa teks.',

            // Tanggal Mulai
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date'     => 'Tanggal mulai harus berupa tanggal yang valid.',

            // Tanggal Selesai
            'tanggal_selesai.required'        => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date'            => 'Tanggal selesai harus berupa tanggal yang valid.',
            'tanggal_selesai.after_or_equal'  => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);


        Periode::create($request->all());

        return redirect()->back()->with('success', 'Periode berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        Periode::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Periode dihapus.');
    }
}
