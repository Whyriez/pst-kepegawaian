<?php

namespace App\Helpers;

use App\Models\JenisLayanan;
use Carbon\Carbon;

class CekPeriode
{
    /**
     * Cek apakah layanan dengan slug tertentu sedang dibuka.
     * Jika tutup, return FALSE. Jika buka, return TRUE.
     */
    public static function isBuka($slugLayanan)
    {
        $layanan = JenisLayanan::where('slug', $slugLayanan)->first();

        if (!$layanan) {
            return false; // Layanan tidak ditemukan, anggap tutup
        }

        return $layanan->isCurrentlyOpen();
    }

    /**
     * Ambil pesan kapan dibuka lagi (Opsional)
     */
    public static function getNextSchedule($slugLayanan)
    {
        $layanan = JenisLayanan::where('slug', $slugLayanan)->first();
        if(!$layanan) return null;

        return $layanan->periodes()
            ->where('is_active', true)
            ->whereDate('tanggal_mulai', '>', now())
            ->orderBy('tanggal_mulai', 'asc')
            ->first();
    }
}
