<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Admin\PeriodeController;
use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman Dashboard User
     */
    public function index()
    {
        $activePeriods = Periode::with('jenisLayanan')
            ->where('is_active', true)
            ->whereDate('tanggal_selesai', '>=', now())
            ->orderBy('tanggal_selesai', 'asc') // Urutkan yang mau deadline duluan
            ->get();

        return view('pages.user.index', compact('activePeriods'));
    }
}
