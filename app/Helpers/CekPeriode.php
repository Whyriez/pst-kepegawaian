<?php

namespace App\Helpers;

use App\Models\JenisLayanan;
use Carbon\Carbon;

class CekPeriode
{
    /**
     * Cek apakah layanan dengan slug tertentu sedang dibuka.
     * Menggunakan logika yang sudah diperbarui di Model JenisLayanan.
     */
    public static function isBuka($slugLayanan)
    {
        $layanan = JenisLayanan::where('slug', $slugLayanan)->first();

        if (!$layanan) {
            return false;
        }

        // Memanggil method model yang sudah kita revisi di atas
        return $layanan->isCurrentlyOpen();
    }

    /**
     * Ambil jadwal pembukaan berikutnya.
     * Hanya mengambil periode yang MEMILIKI TANGGAL (bukan unlimited) dan di masa depan.
     */
    public static function getNextSchedule($slugLayanan)
    {
        $layanan = JenisLayanan::where('slug', $slugLayanan)->first();
        if(!$layanan) return null;

        return $layanan->periodes()
            ->where('is_active', true)
            ->where('is_unlimited', false) // [PENTING] Abaikan unlimited (karena pasti tanggalnya null)
            ->whereNotNull('tanggal_mulai') // Pastikan tanggal tidak null
            ->whereDate('tanggal_mulai', '>', now()) // Hanya yang belum mulai
            ->orderBy('tanggal_mulai', 'asc')
            ->first();
    }

    /**
     * [OPSIONAL] Helper untuk mengambil pesan status
     * Contoh output: "Buka sampai 20 April" atau "Selalu Buka"
     */
    public static function getStatusLabel($slugLayanan)
    {
        $layanan = JenisLayanan::where('slug', $slugLayanan)->first();
        if (!$layanan) return 'Tutup';

        // Cari periode aktif saat ini
        $periodeAktif = $layanan->periodes()
            ->where('is_active', true)
            ->where(function($q) {
                $q->where('is_unlimited', true)
                    ->orWhere(function($d) {
                        $d->whereDate('tanggal_mulai', '<=', now())
                            ->whereDate('tanggal_selesai', '>=', now());
                    });
            })
            ->first();

        if ($periodeAktif) {
            if ($periodeAktif->is_unlimited) {
                return 'Buka (Tanpa Batas)';
            }
            return 'Buka s.d ' . Carbon::parse($periodeAktif->tanggal_selesai)->translatedFormat('d M Y');
        }

        return 'Tutup';
    }
}
