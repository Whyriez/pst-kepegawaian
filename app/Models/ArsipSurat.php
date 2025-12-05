<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArsipSurat extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_dokumen',    // PENGANTAR / SPTJM
        'jenis_layanan_id', // <--- Ganti kategori_layanan jadi ID
        'periode',
        'file_path',
        'file_name'
    ];

    // DEFINISI RELASI KE JENIS LAYANAN
    public function jenisLayanan()
    {
        return $this->belongsTo(JenisLayanan::class, 'jenis_layanan_id');
    }
}
