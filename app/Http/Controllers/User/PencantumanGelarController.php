<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PencantumanGelarController extends Controller
{
    /**
     * Halaman Pencantuman Gelar Akademik
     */
    public function akademik()
    {
        // Lokasi view: resources/views/pages/user/pencantuman_gelar/akademik.blade.php
        return view('pages.user.pencantuman_gelar.akademik');
    }

    /**
     * Halaman Pencantuman Gelar Profesi
     */
    public function profesi()
    {
        // Lokasi view: resources/views/pages/user/pencantuman_gelar/profesi.blade.php
        return view('pages.user.pencantuman_gelar.profesi');
    }
}
