<?php

namespace Database\Seeders;

use App\Models\JenisLayanan;
use App\Models\SyaratDokumen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SyaratDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addDocs = function ($slug, $docs) {
            $layanan = JenisLayanan::where('slug', $slug)->first();
            if ($layanan) {
                foreach ($docs as $doc) {
                    SyaratDokumen::create([
                        'jenis_layanan_id' => $layanan->id,
                        'nama_dokumen' => $doc['nama'],
                        'is_required' => $doc['required'] ?? true,
                        'allowed_types' => $doc['types'] ?? 'pdf',
                    ]);
                }
            }
        };

        // 1. KP Fungsional
        $addDocs('kp-fungsional', [
            ['nama' => 'SK CPNS'],
            ['nama' => 'SK PNS'],
            ['nama' => 'SK Kenaikan Pangkat Terakhir'],
            ['nama' => 'SK Jabatan / Mutasi'],
            ['nama' => 'SKP Tahun 2024'],
            ['nama' => 'SKP Tahun 2023'],
            ['nama' => 'SK PAK'],
            ['nama' => 'Sertifikat Uji Kompetensi', 'required' => false],
        ]);
        
        // 2. KP Penyesuaian Ijazah (Contoh dari form sebelumnya)
        $addDocs('kp-pi', [
            ['nama' => 'SK CPNS'],
            ['nama' => 'SK PNS'],
            ['nama' => 'SK Kenaikan Pangkat Terakhir'],
            ['nama' => 'Ijazah'],
            ['nama' => 'Transkrip Nilai'],
            ['nama' => 'Uraian Tugas'],
            ['nama' => 'Surat Tanda Lulus Ujian PI'],
            ['nama' => 'Akreditasi Prodi'],
        ]);

        // 3. Pensiun BUP (Sesuai Form BUP)
        $addDocs('pensiun-bup', [
            ['nama' => 'SK CPNS'],
            ['nama' => 'SK Kenaikan Pangkat'],
            ['nama' => 'SK Jabatan Terakhir'],
            ['nama' => 'Surat Pernyataan Tidak Pernah Dijatuhi Hukuman Disiplin'],
            ['nama' => 'Surat Pernyataan Tidak Sedang Menjalani Pidana'],
            ['nama' => 'Scan KTP'],
            ['nama' => 'Pas Photo', 'types' => 'jpg,jpeg,png'],
            ['nama' => 'SKP Tahun 2024'],
            ['nama' => 'Buku Nikah', 'required' => false],
            ['nama' => 'Akta Kematian/Cerai', 'required' => false],
            ['nama' => 'Akta Lahir Anak'],
            ['nama' => 'Scan Kartu Keluarga'],
            ['nama' => 'Data Perorangan Calon Pensiun'],
        ]);
        
        // 4. Tugas Belajar
        $addDocs('tugas-belajar', [
             ['nama' => 'Surat Perjanjian dengan Pimpinan Satuan Kerja'],
             ['nama' => 'Surat Perjanjian dengan Sponsor', 'required' => false],
        ]);

        // 5. Pindah Keluar
        $addDocs('pindah-keluar', [
            ['nama' => 'Surat Permohonan Pindah dari YBS'],
            ['nama' => 'Surat Persetujuan Mutasi dari Instansi Asal'],
            ['nama' => 'Surat Usul Mutasi dari Instansi Penerima'],
            ['nama' => 'Surat Pernyataan Bebas Hukuman Disiplin'],
            ['nama' => 'Surat Pernyataan Tidak Sedang Tugas Belajar'],
            ['nama' => 'Surat Keterangan Bebas Temuan'],
            ['nama' => 'Surat Analisis Jabatan & ABK'],
            ['nama' => 'SKP 2024'],
            ['nama' => 'SKP 2023'],
            ['nama' => 'SK Pangkat/Jabatan Terakhir'],
            ['nama' => 'Surat Pengumuman Uji Kompetensi (Jika Ada)', 'required' => false],
        ]);

        // ... Tambahkan layanan lain sesuai kebutuhan ...
        // Satyalancana
        $addDocs('satyalancana', [
            ['nama' => 'SK CPNS'],
            ['nama' => 'SK PNS'],
            ['nama' => 'SK Pangkat Terakhir'],
            ['nama' => 'SK Jabatan Terakhir'],
            ['nama' => 'Satyalancana Sebelumnya', 'required' => false],
        ]);
    }
}
