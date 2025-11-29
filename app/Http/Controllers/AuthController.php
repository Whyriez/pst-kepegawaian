<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        // 1. Ubah validasi dari email ke nip
        $request->validate([
            'nip' => 'required|numeric|digits:18',
            'password' => 'required',
        ]);

        // 2. Cari Pegawai berdasarkan NIP
        $pegawai = Pegawai::where('nip', $request->nip)->first();

        // 3. Cek apakah Pegawai ada DAN memiliki akun user aktif
        if ($pegawai && $pegawai->user) {

            // 4. Lakukan Auth::attempt menggunakan email dari relasi user tersebut
            // Trik: Kita login pakai email di belakang layar, tapi user taunya pakai NIP
            $credentials = [
                'email' => $pegawai->user->email,
                'password' => $request->password
            ];

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                $role = Auth::user()->role;

                if ($role === 'admin') {
                    return redirect()->intended('admin/dashboard');
                } else {
                    return redirect()->intended('dashboard');
                }
            }
        }

        // Jika Pegawai tidak ditemukan atau Password salah
        return back()->withErrors([
            'nip' => 'NIP atau password salah.',
        ])->onlyInput('nip');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
