<?php

namespace App\Providers;

use App\Models\Pengajuan;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
//        URL::forceScheme('https');

        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            // Ambil data mentah group by Kategori DAN Slug
            $rawCounts = Pengajuan::where('status', 'pending')
                ->join('jenis_layanans', 'pengajuans.jenis_layanan_id', '=', 'jenis_layanans.id')
                ->select('jenis_layanans.kategori', 'jenis_layanans.slug', DB::raw('count(*) as total'))
                ->groupBy('jenis_layanans.kategori', 'jenis_layanans.slug')
                ->get();

            // 1. Data untuk Badge Menu Utama (Sum total per kategori)
            // Contoh: ['Kenaikan Pangkat' => 5, 'Pensiun' => 3]
            $badgeKategori = $rawCounts->groupBy('kategori')->map(function ($row) {
                return $row->sum('total');
            })->toArray();

            // 2. Data untuk Badge Sub-Menu (Total per slug spesifik)
            // Contoh: ['kp-fungsional' => 2, 'kp-struktural' => 3]
            // PENTING: Key array ini adalah 'slug' yang ada di database table jenis_layanans
            $badgeSlug = $rawCounts->pluck('total', 'slug')->toArray();

            $notifikasi_tindak_lanjut = Pengajuan::with(['pegawai', 'jenisLayanan'])
                ->where('status', 'ditunda')
                ->whereDate('tanggal_tindak_lanjut', '<=', now())
                ->orderBy('tanggal_tindak_lanjut', 'asc')
                ->get();

            $view->with([
                'badgeKategori' => $badgeKategori,
                'badgeSlug' => $badgeSlug,
                'notifikasi_tindak_lanjut' => $notifikasi_tindak_lanjut
            ]);
        });
    }
}
