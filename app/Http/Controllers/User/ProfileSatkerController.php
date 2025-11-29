<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SatuanKerja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileSatkerController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Load relasi 'pegawai' dan dari pegawai load 'satuanKerja'
        // Ini disebut Eager Loading agar query database lebih efisien
        $user->load('pegawai.satuanKerja');

        // Ambil satuan kerja DARI PEGAWAI, bukan dari user langsung
        // Gunakan optional chaining (?->) jaga-jaga kalau user belum jadi pegawai
        $satuanKerja = $user->pegawai?->satuanKerja;

        return view('pages.user.profil_satker.index', compact('satuanKerja'));
    }
}
