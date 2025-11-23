<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PindahInstansiController extends Controller
{
    public function masuk()
    {
        // Folder: pindah_antar_instansi
        return view('pages.admin.pindah_antar_instansi.masuk');
    }

    public function keluar()
    {
        return view('pages.admin.pindah_antar_instansi.keluar');
    }
}
