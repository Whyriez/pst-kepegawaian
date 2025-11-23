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
}
