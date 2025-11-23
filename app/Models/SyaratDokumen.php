<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyaratDokumen extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_layanan_id',
        'nama_dokumen',
        'is_required',
        'allowed_types', // pdf,jpg
        'max_size_kb'
    ];

    public function jenisLayanan()
    {
        return $this->belongsTo(JenisLayanan::class);
    }
}
