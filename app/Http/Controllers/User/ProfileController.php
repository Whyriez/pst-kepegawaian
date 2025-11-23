<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return view('pages.user.profile.index');
    }

    public function update(Request $request)
    {
        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');
    }
}
