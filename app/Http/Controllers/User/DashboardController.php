<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman Dashboard User
     */
    public function index()
    {
        // Jika nanti Anda butuh data dari database, ambil di sini
        // Contoh: $notifikasi = Notification::all();
        
        return view('pages.user.index');
    }
}
