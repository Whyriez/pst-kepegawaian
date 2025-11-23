<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_tiket',
        'pegawai_id',
        'jenis_layanan_id',
        'status',
        'prioritas', // <--- JANGAN LUPA TAMBAHKAN INI
        'data_tambahan',
        'catatan_admin',
        'tanggal_tindak_lanjut',
        'tanggal_pengajuan',
        'tanggal_verifikasi',
        'verifikator_id'
    ];

    // Casting data JSON otomatis jadi Array
    protected $casts = [
        'data_tambahan' => 'array',
        'tanggal_pengajuan' => 'date',
        'tanggal_verifikasi' => 'date',
        'tanggal_tindak_lanjut' => 'date',
    ];

    // Relasi (Sama seperti sebelumnya)
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
    public function jenisLayanan()
    {
        return $this->belongsTo(JenisLayanan::class);
    }
    public function dokumenPengajuans()
    {
        return $this->hasMany(DokumenPengajuan::class);
    }
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }

    // --- HELPER UNTUK TAMPILAN (ACCESSOR) ---

    // Warna Badge Status (Bootstrap Class)
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',   // Kuning
            'disetujui' => 'success', // Hijau
            'ditolak' => 'danger',    // Merah
            'ditunda' => 'secondary', // Abu-abu
            'perbaikan' => 'info',    // Biru muda
            default => 'light',
        };
    }

    // Warna Badge Prioritas (Bootstrap Class)
    public function getPrioritasBadgeAttribute()
    {
        return match ($this->prioritas) {
            'tinggi' => 'danger',  // Merah (Urgent)
            'sedang' => 'primary', // Biru (Normal)
            'rendah' => 'success', // Hijau (Santai)
            default => 'secondary',
        };
    }
}
