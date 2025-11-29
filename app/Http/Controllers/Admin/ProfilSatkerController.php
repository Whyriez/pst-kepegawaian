<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilSatkerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $satuanKerja = $user->pegawai?->satuanKerja;

        if (!$satuanKerja) {
            return redirect()->back()->with('error', 'Akun Admin ini tidak terhubung dengan Satuan Kerja manapun.');
        }

        return view('pages.admin.profil_satker.index', compact('satuanKerja'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $satuanKerja = $user->pegawai?->satuanKerja;

        if (!$satuanKerja) {
            return abort(404, 'Satuan Kerja tidak ditemukan');
        }

        // 1. Validasi Input
        $request->validate([
            'nama_satuan_kerja' => 'required|string|max:255',
            'kode_satuan_kerja' => 'required|string|max:50|unique:satuan_kerjas,kode_satuan_kerja,' . $satuanKerja->id,
            'alamat_lengkap'    => 'required|string',
            'telepon'           => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:255',
            'website'           => 'nullable|string|max:255',
            'kepala_satker'     => 'required|string|max:255',
            'nip_kepala_satker' => 'required|string|max:20',
        ]);

        // 2. Update Data
        $satuanKerja->update([
            'nama_satuan_kerja' => $request->nama_satuan_kerja,
            'kode_satuan_kerja' => $request->kode_satuan_kerja,
            'alamat_lengkap'    => $request->alamat_lengkap,
            'telepon'           => $request->telepon,
            'email'             => $request->email,
            'website'           => $request->website,
            'kepala_satker'     => $request->kepala_satker,
            'nip_kepala_satker' => $request->nip_kepala_satker,
        ]);

        return redirect()->route('admin.profil_satker')->with('success', 'Profil Satuan Kerja berhasil diperbarui!');
    }
}
