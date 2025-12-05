<?php

namespace Database\Seeders;

use App\Models\JenisLayanan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layanan = [
            // Kenaikan Pangkat
            ['nama' => 'KP Fungsional', 'slug' => 'kp-fungsional', 'kategori' => 'Kenaikan Pangkat'],
            ['nama' => 'KP Struktural', 'slug' => 'kp-struktural', 'kategori' => 'Kenaikan Pangkat'],
            ['nama' => 'KP Reguler', 'slug' => 'kp-reguler', 'kategori' => 'Kenaikan Pangkat'],
            ['nama' => 'KP Penyesuaian Ijazah', 'slug' => 'kp-pi', 'kategori' => 'Kenaikan Pangkat'],

            // Pensiun
            ['nama' => 'Pensiun BUP', 'slug' => 'pensiun-bup', 'kategori' => 'Pensiun'],
            ['nama' => 'Pensiun Janda/Duda/Yatim', 'slug' => 'pensiun-jdy', 'kategori' => 'Pensiun'],
            ['nama' => 'Pensiun APS', 'slug' => 'pensiun-aps', 'kategori' => 'Pensiun'],
            ['nama' => 'Pensiun Meninggal', 'slug' => 'pensiun-meninggal', 'kategori' => 'Pensiun'],
            ['nama' => 'Pensiun Uzur', 'slug' => 'pensiun-uzur', 'kategori' => 'Pensiun'],
            ['nama' => 'Pensiun Hilang', 'slug' => 'pensiun-hilang', 'kategori' => 'Pensiun'],
            ['nama' => 'Pensiun Tanpa Ahli Waris', 'slug' => 'pensiun-taw', 'kategori' => 'Pensiun'],

            // Pindah Instansi
            ['nama' => 'Pindah Masuk', 'slug' => 'pindah-masuk', 'kategori' => 'Pindah Instansi'],
            ['nama' => 'Pindah Keluar', 'slug' => 'pindah-keluar', 'kategori' => 'Pindah Instansi'],

            // Jabatan Fungsional
            ['nama' => 'Pengangkatan JF', 'slug' => 'jf-pengangkatan', 'kategori' => 'Jabatan Fungsional'],
            ['nama' => 'Pemberhentian JF', 'slug' => 'jf-pemberhentian', 'kategori' => 'Jabatan Fungsional'],
            ['nama' => 'Naik Jenjang JF', 'slug' => 'jf-naik-jenjang', 'kategori' => 'Jabatan Fungsional'],

            // Lainnya
            ['nama' => 'Tugas Belajar', 'slug' => 'tugas-belajar', 'kategori' => 'Lainnya'],
            ['nama' => 'Konversi AK Pendidikan', 'slug' => 'konversi-ak-pendidikan', 'kategori' => 'Lainnya'],
            ['nama' => 'Perbaikan Data ASN', 'slug' => 'perbaikan-data', 'kategori' => 'Lainnya'],
            ['nama' => 'Satyalancana', 'slug' => 'satyalancana', 'kategori' => 'Lainnya'],
            ['nama' => 'Gelar Akademik', 'slug' => 'gelar-akademik', 'kategori' => 'Pencantuman Gelar'],
            ['nama' => 'Gelar Profesi', 'slug' => 'gelar-profesi', 'kategori' => 'Pencantuman Gelar'],
        ];

        foreach ($layanan as $item) {
            JenisLayanan::create([
                'nama_layanan' => $item['nama'],
                'slug' => $item['slug'],
                'kategori' => $item['kategori'],
                'is_active' => true
            ]);
        }
    }
}
