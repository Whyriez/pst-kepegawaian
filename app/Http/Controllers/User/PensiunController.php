<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PensiunController extends Controller
{
    /**
     * Halaman Pensiun Batas Usia Pensiun (BUP)
     */
    public function bup()
    {
        return view('pages.user.pensiun.batas_usia_pensiun');
    }

    /**
     * Halaman Pensiun Janda/Duda/Yatim
     */
    public function jandaDudaYatim()
    {
        return view('pages.user.pensiun.janda_duda_yatim');
    }

    /**
     * Halaman Pensiun Atas Permintaan Sendiri (APS)
     */
    public function aps()
    {
        return view('pages.user.pensiun.atas_permintaan_sendiri');
    }

    /**
     * Halaman Pensiun Meninggal
     */
    public function meninggal()
    {
        return view('pages.user.pensiun.meninggal');
    }

    /**
     * Halaman Pensiun Uzur
     */
    public function uzur()
    {
        return view('pages.user.pensiun.uzur');
    }

    /**
     * Halaman Pensiun Hilang
     */
    public function hilang()
    {
        return view('pages.user.pensiun.hilang');
    }

    /**
     * Halaman Pensiun Tanpa Ahli Waris
     */
    public function tanpaAhliWaris()
    {
        return view('pages.user.pensiun.tanpa_ahli_waris');
    }
}
