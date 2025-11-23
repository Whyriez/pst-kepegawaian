<?php

namespace Database\Seeders;

use App\Models\SatuanKerja;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SatuanKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SatuanKerja::create([
            'nama_satuan_kerja' => 'Kementerian Agama Kota Gorontalo',
            'kode_satuan_kerja' => 'KEMENAG-GTLO-01',
            'alamat_lengkap' => 'Jl. Ahmad Yani No. 10, Kota Gorontalo',
            'telepon' => '0435-821123',
            'email' => 'kotagorontalo@kemenag.go.id',
            'website' => 'gorontalo.kemenag.go.id',
            'kepala_satker' => 'Dr. H. Hamka Arbie, M.HI',
            'nip_kepala_satker' => '197001011995031001',
        ]);

        SatuanKerja::create([
            'nama_satuan_kerja' => 'Dinas Komunikasi dan Informatika',
            'kode_satuan_kerja' => 'DISKOMINFO-01',
            'alamat_lengkap' => 'Jl. Ternate, Kota Gorontalo',
            'kepala_satker' => 'Kepala Dinas Dummy',
            'nip_kepala_satker' => '198001012005011002',
        ]);
    }
}
