<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TugasBelajarController extends Controller
{
    public function index()
    {
        return view('pages.admin.tugas_belajar.index');
    }
}
