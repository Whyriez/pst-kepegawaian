<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilSatkerController extends Controller
{
    public function index()
    {
        // Pastikan Anda membuat file: resources/views/pages/admin/profil_satker/index.blade.php
        return view('pages.admin.profil_satker.index');
    }
}
