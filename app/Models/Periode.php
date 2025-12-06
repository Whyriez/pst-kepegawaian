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
        'is_active',
        'is_unlimited'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
        'is_unlimited' => 'boolean'
    ];

    public function isOpen()
    {
        // Jika tidak aktif secara manual, return false
        if (!$this->is_active) {
            return false;
        }

        // Jika Unlimited (Selalu Buka), return true
        if ($this->is_unlimited) {
            return true;
        }

        // Jika pakai tanggal, cek range tanggal
        $now = Carbon::now();

        // Pastikan tanggal tidak null sebelum dicek
        if ($this->tanggal_mulai && $this->tanggal_selesai) {
            return $now->between($this->tanggal_mulai, $this->tanggal_selesai);
        }

        return false;
    }

    public function jenisLayanan()
    {
        return $this->belongsTo(JenisLayanan::class);
    }
}
