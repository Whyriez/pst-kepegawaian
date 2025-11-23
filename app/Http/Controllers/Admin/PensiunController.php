<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PensiunController extends Controller
{
    public function aps()
    {
        // Sesuai nama file: atas_permintaan_sendiri.blade.php
        return view('pages.admin.pensiun.atas_permintaan_sendiri');
    }

    public function bup()
    {
        // Sesuai nama file: batas_usia_pensiun.blade.php
        return view('pages.admin.pensiun.batas_usia_pensiun');
    }

    public function hilang()
    {
        return view('pages.admin.pensiun.hilang');
    }

    public function jandaDudaYatim()
    {
        return view('pages.admin.pensiun.janda_duda_yatim');
    }

    public function meninggal()
    {
        return view('pages.admin.pensiun.meninggal');
    }

    public function tanpaAhliWaris()
    {
        return view('pages.admin.pensiun.tanpa_ahli_waris');
    }

    public function uzur()
    {
        return view('pages.admin.pensiun.uzur');
    }
}
