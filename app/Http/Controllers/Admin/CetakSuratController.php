<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CetakSuratController extends Controller
{
    public function index()
    {
        return view('pages.admin.cetak_surat.index');
    }
}
