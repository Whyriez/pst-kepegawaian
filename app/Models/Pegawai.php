<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'satuan_kerja_id',
        'nip',
        'nama_lengkap',
        'jabatan',
        'pangkat',
        'golongan_ruang',
        'tempat_lahir',
        'tanggal_lahir',
        'pendidikan_terakhir',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relasi ke Akun Login
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Satuan Kerja
    public function satuanKerja()
    {
        return $this->belongsTo(SatuanKerja::class);
    }

    // Relasi: Pegawai bisa memiliki banyak Pengajuan
    public function pengajuans()
    {
        return $this->hasMany(Pengajuan::class);
    }
}
