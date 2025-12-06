<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisLayanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_layanan',
        'slug', // e.g. 'kp-fungsional'
        'kategori', // e.g. 'Kenaikan Pangkat'
        'is_active'
    ];

    // Relasi: Satu layanan punya banyak syarat dokumen
    public function syaratDokumens()
    {
        return $this->hasMany(SyaratDokumen::class);
    }

    // Relasi: Satu layanan punya banyak pengajuan masuk
    public function pengajuans()
    {
        return $this->hasMany(Pengajuan::class);
    }

    public function periodes()
    {
        return $this->hasMany(Periode::class);
    }

    public function isCurrentlyOpen()
    {
        // Cek apakah ada periode yang AKTIF dan memenuhi syarat:
        // 1. Unlimited = TRUE
        // ATAU
        // 2. Tanggal Mulai <= Sekarang <= Tanggal Selesai

        return $this->periodes()
            ->where('is_active', true)
            ->where(function($query) {
                $query->where('is_unlimited', true)
                    ->orWhere(function($dateQuery) {
                        $dateQuery->whereDate('tanggal_mulai', '<=', now())
                            ->whereDate('tanggal_selesai', '>=', now());
                    });
            })
            ->exists();
    }
}
