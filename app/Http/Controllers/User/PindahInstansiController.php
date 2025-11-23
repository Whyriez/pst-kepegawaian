<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PindahInstansiController extends Controller
{
    /**
     * Halaman Pindah Masuk Instansi
     */
    public function masuk()
    {
        // Lokasi view: resources/views/pages/user/pindah_instansi/masuk.blade.php
        return view('pages.user.pindah_antar_instansi.masuk');
    }

    /**
     * Halaman Pindah Keluar Instansi
     */
    public function keluar()
    {
        // Lokasi view: resources/views/pages/user/pindah_instansi/keluar.blade.php
        return view('pages.user.pindah_antar_instansi.keluar');
    }
}
