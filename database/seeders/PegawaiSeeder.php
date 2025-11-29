<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pegawai::create([
            'user_id' => 2, // ID milik 'User Pegawai'
            'satuan_kerja_id' => 1,
            'nip' => '123456789012345678',
            'nama_lengkap' => 'User Pegawai',
            'jabatan' => 'Pranata Komputer Ahli Pertama',
            'pangkat' => 'Penata Muda Tingkat I',
            'golongan_ruang' => 'III/b',
            'tempat_lahir' => 'Gorontalo',
            'tanggal_lahir' => '1990-01-01',
            'pendidikan_terakhir' => 'S1 Teknik Informatika',
        ]);
        
        // Data dummy tambahan tanpa user login (untuk tes cek NIP orang lain)
        Pegawai::create([
            'user_id' => 1,
            'satuan_kerja_id' => 1,
            'nip' => '198765432109876543',
            'nama_lengkap' => 'Siti Aminah, S.E.',
            'jabatan' => 'Analis Kepegawaian',
            'pangkat' => 'Penata',
            'golongan_ruang' => 'III/c',
            'tempat_lahir' => 'Limboto',
            'tanggal_lahir' => '1987-05-12',
            'pendidikan_terakhir' => 'S1 Ekonomi',
        ]);
    }
}
