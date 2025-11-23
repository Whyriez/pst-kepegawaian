<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerbaikanDataController extends Controller
{
    public function index()
    {
        // Folder: perbaikan_data
        return view('pages.admin.perbaikan_data.index');
    }
}
