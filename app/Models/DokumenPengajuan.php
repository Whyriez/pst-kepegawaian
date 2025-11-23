<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenPengajuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajuan_id',
        'syarat_dokumen_id',
        'nama_file_asli',
        'path_file',
        'tipe_file',
        'ukuran_file'
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function syaratDokumen()
    {
        return $this->belongsTo(SyaratDokumen::class);
    }
}
