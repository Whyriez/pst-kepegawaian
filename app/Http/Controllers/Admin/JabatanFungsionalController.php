<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JabatanFungsionalController extends Controller
{
    public function naikJenjang()
    {
        return view('pages.admin.jabatan_fungsional.naik_jenjang');
    }

    public function pemberhentian()
    {
        return view('pages.admin.jabatan_fungsional.pemberhentian');
    }

    public function pengangkatan()
    {
        return view('pages.admin.jabatan_fungsional.pengangkatan');
    }
}
