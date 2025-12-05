<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_layanan_id',
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean'
    ];

    public function isOpen()
    {
        $now = Carbon::now();
        return $this->is_active
            && $now->between($this->tanggal_mulai, $this->tanggal_selesai);
    }

    public function jenisLayanan()
    {
        return $this->belongsTo(JenisLayanan::class);
    }
}
