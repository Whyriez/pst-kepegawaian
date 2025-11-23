<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatuanKerja extends Model
{
    use HasFactory;

    protected $table = 'satuan_kerjas';
    
    protected $fillable = [
        'nama_satuan_kerja',
        'kode_satuan_kerja',
        'alamat_lengkap',
        'telepon',
        'email',
        'website',
        'kepala_satker',
        'nip_kepala_satker',
    ];

    // Relasi: Satu Satker memiliki banyak User (Admin/Pegawai)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relasi: Satu Satker memiliki banyak data Pegawai
    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }
}
