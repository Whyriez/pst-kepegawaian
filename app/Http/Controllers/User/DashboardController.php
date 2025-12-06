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
        // Revisi: Hanya ambil yang punya deadline (Bukan Unlimited)
        $detailPeriods = Periode::with('jenisLayanan')
            ->where('is_active', true)
            ->where('is_unlimited', false) // [BARU] Wajib False (Tidak Unlimited)
            ->whereDate('tanggal_selesai', '>=', now()) // Wajib belum kadaluarsa
            ->orderBy('tanggal_selesai', 'asc')
            ->get();

        // Grouping untuk MODAL
        $groupedPeriods = $detailPeriods->groupBy(function ($item) {
            return $item->jenisLayanan->kategori . '|' . $item->nama_periode;
        });

        return view('pages.user.index', compact('detailPeriods', 'groupedPeriods'));
    }
}
