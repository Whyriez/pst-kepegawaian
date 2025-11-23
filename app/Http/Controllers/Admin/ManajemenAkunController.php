<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManajemenAkunController extends Controller
{
    public function index()
    {
        return view('pages.admin.manajemen_akun.index');
    }
}
