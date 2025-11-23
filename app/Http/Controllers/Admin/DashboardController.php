<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Wajib untuk DB::raw

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Kartu Atas & Sidebar
        $stats = [
            'total_pengajuan' => Pengajuan::count(),
            'menunggu'        => Pengajuan::where('status', 'pending')->count(),
            'disetujui'       => Pengajuan::where('status', 'disetujui')
                ->whereMonth('updated_at', Carbon::now()->month)
                ->count(),
            'ditolak'         => Pengajuan::where('status', 'ditolak')
                ->whereMonth('updated_at', Carbon::now()->month)
                ->count(),
            'berkas_baru'     => Pengajuan::whereDate('created_at', Carbon::today())->count(),
            'user_active'     => User::where('role', 'user')->count(),
        ];

        // 2. Data Grafik Line Chart (Tren Harian - 30 Hari Terakhir)
        $chartData = Pengajuan::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total'),
            DB::raw('SUM(CASE WHEN status = "disetujui" THEN 1 ELSE 0 END) as disetujui')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // 3. Data Grafik Pie Chart (Distribusi Layanan)
        $pieData = Pengajuan::select('jenis_layanans.nama_layanan', DB::raw('count(*) as total'))
            ->join('jenis_layanans', 'pengajuans.jenis_layanan_id', '=', 'jenis_layanans.id')
            ->groupBy('jenis_layanans.nama_layanan')
            ->get();

        // 4. Tabel Aktivitas Terbaru
        $recentActivities = Pengajuan::with(['pegawai', 'jenisLayanan'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('pages.admin.index', compact('stats', 'chartData', 'pieData', 'recentActivities'));
    }
}
