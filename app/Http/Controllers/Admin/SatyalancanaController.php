<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SatyalancanaController extends Controller
{
    public function index()
    {
        return view('pages.admin.satyalancana.index');
    }
}
