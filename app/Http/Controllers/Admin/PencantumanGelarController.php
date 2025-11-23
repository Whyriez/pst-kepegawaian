<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PencantumanGelarController extends Controller
{
    public function akademik()
    {
        return view('pages.admin.pencantuman_gelar.akademik');
    }

    public function profesi()
    {
        return view('pages.admin.pencantuman_gelar.profesi');
    }
}
