<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerbaikanDataController extends Controller
{
    public function index()
    {
        return view('pages.user.perbaikan_data.index');
    }
}
