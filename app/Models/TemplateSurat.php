<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateSurat extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_layanan_id',
        'nama_surat',
        'konten_template',
        'is_active'
    ];

    public function jenisLayanan()
    {
        return $this->belongsTo(JenisLayanan::class);
    }
}
