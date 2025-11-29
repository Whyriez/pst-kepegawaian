<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CetakSuratController extends Controller
{
    public function pengantar(Request $request)
    {
        // DATA DUMMY (Sesuai Gambar untuk Demo)
        $data_pegawai = [
            (object)[
                'nama' => 'ZULFIAH, S.Ag.',
                'nip' => '196611101997032001',
                'jabatan' => 'Guru Ahli Madya pada MTsN 1 Kota Gorontalo Kota Gorontalo Provinsi Gorontalo',
                'perihal' => 'KP Fungsional'
            ],
            (object)[
                'nama' => 'ANSHAR H HABIE, S.E.',
                'nip' => '197512242003121002',
                'jabatan' => 'Analisis Pengelolaan Keuangan APBN Ahli Pertama pada Sub Bagian Tata Usaha',
                'perihal' => 'KP Fungsional'
            ],
            (object)[
                'nama' => 'SALMA SALAMUN, S.Ag. M.Pd.I',
                'nip' => '197805302003122004',
                'jabatan' => 'Guru Ahli Madya pada MAN 1 Kota Gorontalo Kota Gorontalo Provinsi Gorontalo',
                'perihal' => 'KP Fungsional'
            ],
            (object)[
                'nama' => 'TASNIM H. HARUN, S.Pd., M.Si.',
                'nip' => '197002012005011006',
                'jabatan' => 'Guru Ahli Madya pada MAN 1 Kota Gorontalo Kota Gorontalo Provinsi Gorontalo',
                'perihal' => 'KP Fungsional'
            ],
            (object)[
                'nama' => 'MINARTI DJAMA, S.Pd',
                'nip' => '198111062007102002',
                'jabatan' => 'Guru Ahli Muda pada MTsS dilingkungan Kota Gorontalo Provinsi Gorontalo',
                'perihal' => 'Pencantuman Gelar'
            ],
        ];

        // DATA DUMMY Side Bar Kanan
        $templates = [
            (object)['id' => 1, 'perihal' => 'Pencantuman Gelar'],
            (object)['id' => 2, 'perihal' => 'Kenaikan Pangkat Periode Desember'],
            (object)['id' => 3, 'perihal' => 'Pensiun Batas Usia Pensiun'],
        ];

        return view('pages.admin.cetak_surat.pengantar', compact('data_pegawai', 'templates'));
    }

    public function sptjm(Request $request)
    {
        // DATA DUMMY PEGAWAI (Contoh untuk SPTJM)
        $data_pegawai = [
            (object)[
                'nama' => 'Drs. H. ABDUL MALIK',
                'nip' => '196801011994031005',
                'jabatan' => 'Kepala Kantor Kementerian Agama Kota Gorontalo',
                'perihal' => 'SPTJM Kenaikan Pangkat'
            ],
            (object)[
                'nama' => 'MARWAN T. HUSAIN, S.Ag',
                'nip' => '197505052003121001',
                'jabatan' => 'Kepala Sub Bagian Tata Usaha',
                'perihal' => 'SPTJM Berkala'
            ],
            (object)[
                'nama' => 'SITI RACHMAWATY, S.Pd.I',
                'nip' => '198202152009012003',
                'jabatan' => 'Guru Muda pada MIN 1 Kota Gorontalo',
                'perihal' => 'SPTJM Kenaikan Pangkat'
            ],
        ];

        // DATA DUMMY SIDEBAR (Template Upload)
        $templates = [
            (object)['id' => 1, 'perihal' => 'SPTJM Kenaikan Pangkat Periode April'],
            (object)['id' => 2, 'perihal' => 'SPTJM Kenaikan Pangkat Periode Oktober'],
            (object)['id' => 3, 'perihal' => 'SPTJM Pensiun'],
        ];

        return view('pages.admin.cetak_surat.sptjm', compact('data_pegawai', 'templates'));
    }
}
