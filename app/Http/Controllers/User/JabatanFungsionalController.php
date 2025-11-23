<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JabatanFungsionalController extends Controller
{
    /**
     * Halaman Pengangkatan Jabatan Fungsional
     */
    public function pengangkatan()
    {
        return view('pages.user.jabatan_fungsional.pengangkatan');
    }

    /**
     * Halaman Pemberhentian Jabatan Fungsional
     */
    public function pemberhentian()
    {
        return view('pages.user.jabatan_fungsional.pemberhentian');
    }

    /**
     * Halaman Naik Jenjang Jabatan Fungsional
     */
    public function naikJenjang()
    {
        return view('pages.user.jabatan_fungsional.naik_jenjang');
    }
}
