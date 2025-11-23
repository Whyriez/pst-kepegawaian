<?php

use App\Http\Controllers\Admin\CetakSuratController as AdminCetakSuratController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JabatanFungsionalController as AdminJabatanFungsionalController;
use App\Http\Controllers\Admin\KenaikanPangkatController as AdminKenaikanPangkatController;
use App\Http\Controllers\Admin\ManajemenAkunController;
use App\Http\Controllers\Admin\PencantumanGelarController as AdminPencantumanGelarController;
use App\Http\Controllers\Admin\PensiunController as AdminPensiunController;
use App\Http\Controllers\Admin\PenugasanController as AdminPenugasanController;
use App\Http\Controllers\Admin\PindahInstansiController as AdminPindahInstansiController;
use App\Http\Controllers\Admin\ProfilSatkerController;
use App\Http\Controllers\Admin\SatyalancanaController as AdminSatyalancanaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\CetakSuratController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\JabatanFungsionalController;
use App\Http\Controllers\User\KenaikanPangkatController;
use App\Http\Controllers\User\PencantumanGelarController;
use App\Http\Controllers\User\PensiunController;
use App\Http\Controllers\User\PenugasanController;
use App\Http\Controllers\User\PerbaikanDataController;
use App\Http\Controllers\User\PindahInstansiController;
use App\Http\Controllers\User\ProfilController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SatyalancanaController;
use App\Http\Controllers\User\TugasBelajarController;
use Illuminate\Support\Facades\Route;


Route::get('/', [AuthController::class, 'index'])->name('login'); // Halaman awal langsung login
Route::get('/login', [AuthController::class, 'index'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth'])->group(function () {
    Route::middleware(['cekrole:user'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('kenaikan-pangkat')->name('kp.')->group(function () {

            Route::get('/fungsional', [KenaikanPangkatController::class, 'fungsional'])->name('fungsional');
            Route::post('/fungsional/store', [KenaikanPangkatController::class, 'storeFungsional'])->name('fungsional.store');


            Route::get('/penyesuaian-ijazah', [KenaikanPangkatController::class, 'penyesuaianIjazah'])->name('penyesuaian_ijazah');
            Route::post('/penyesuaian-ijazah/store', [KenaikanPangkatController::class, 'storePenyesuaianIjazah'])->name('penyesuaian_ijazah.store');

            Route::get('/reguler', [KenaikanPangkatController::class, 'reguler'])->name('reguler');
            Route::post('/reguler/store', [KenaikanPangkatController::class, 'storeReguler'])->name('reguler.store');

            // URL: /kenaikan-pangkat/struktural
            Route::get('/struktural', [KenaikanPangkatController::class, 'struktural'])->name('struktural');
            Route::post('/struktural/store', [KenaikanPangkatController::class, 'storeStruktural'])->name('struktural.store');

            Route::get('/ajax/cek-nip/{nip}', [KenaikanPangkatController::class, 'cekNip'])->name('ajax.cek-nip');
        });

        Route::prefix('pensiun')->name('pensiun.')->group(function () {

            // URL: /pensiun/bup
            Route::get('/bup', [PensiunController::class, 'bup'])->name('bup');

            // URL: /pensiun/janda-duda-yatim
            Route::get('/janda-duda-yatim', [PensiunController::class, 'jandaDudaYatim'])->name('janda_duda_yatim');

            // URL: /pensiun/aps
            Route::get('/aps', [PensiunController::class, 'aps'])->name('aps');

            // URL: /pensiun/meninggal
            Route::get('/meninggal', [PensiunController::class, 'meninggal'])->name('meninggal');

            // URL: /pensiun/uzur
            Route::get('/uzur', [PensiunController::class, 'uzur'])->name('uzur');

            // URL: /pensiun/hilang
            Route::get('/hilang', [PensiunController::class, 'hilang'])->name('hilang');

            // URL: /pensiun/tanpa-ahli-waris
            Route::get('/tanpa-ahli-waris', [PensiunController::class, 'tanpaAhliWaris'])->name('taw'); // Saya singkat jadi taw agar tidak kepanjangan
        });

        Route::prefix('pindah-instansi')->name('pindah.')->group(function () {

            // URL: /pindah-instansi/masuk
            Route::get('/masuk', [PindahInstansiController::class, 'masuk'])->name('masuk');

            // URL: /pindah-instansi/keluar
            Route::get('/keluar', [PindahInstansiController::class, 'keluar'])->name('keluar');
        });

        Route::prefix('jabatan-fungsional')->name('jf.')->group(function () {

            // URL: /jabatan-fungsional/pengangkatan
            Route::get('/pengangkatan', [JabatanFungsionalController::class, 'pengangkatan'])->name('pengangkatan');

            // URL: /jabatan-fungsional/pemberhentian
            Route::get('/pemberhentian', [JabatanFungsionalController::class, 'pemberhentian'])->name('pemberhentian');

            // URL: /jabatan-fungsional/naik-jenjang
            Route::get('/naik-jenjang', [JabatanFungsionalController::class, 'naikJenjang'])->name('naik_jenjang');
        });

        Route::prefix('pencantuman-gelar')->name('gelar.')->group(function () {
            // URL: /pencantuman-gelar/akademik
            Route::get('/akademik', [PencantumanGelarController::class, 'akademik'])->name('akademik');

            // URL: /pencantuman-gelar/profesi
            Route::get('/profesi', [PencantumanGelarController::class, 'profesi'])->name('profesi');
        });

        Route::get('/satyalancana', [SatyalancanaController::class, 'index'])->name('satyalancana');


        // 1. Penugasan
        Route::get('/penugasan', [PenugasanController::class, 'index'])->name('penugasan');

        // 2. Perbaikan Data ASN
        Route::get('/perbaikan-data-asn', [PerbaikanDataController::class, 'index'])->name('perbaikan_data');

        // 3. Tugas Belajar
        Route::get('/tugas-belajar', [TugasBelajarController::class, 'index'])->name('tugas_belajar');

        // 4. Cetak Surat
        Route::get('/cetak-surat', [CetakSuratController::class, 'index'])->name('cetak_surat');
    });


    Route::middleware(['auth', 'cekrole:admin'])->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Profil Satuan Kerja
        Route::get('/profil-satker', [ProfilSatkerController::class, 'index'])->name('profil_satker');

        // Kenaikan Pangkat (List Pengajuan Masuk)
        Route::prefix('kenaikan-pangkat')->name('kp.')->group(function () {
            Route::get('/fungsional', [AdminKenaikanPangkatController::class, 'fungsional'])->name('fungsional');
            Route::post('/fungsional/approve', [AdminKenaikanPangkatController::class, 'approve'])->name('fungsional.approve');
            Route::post('/fungsional/postpone', [AdminKenaikanPangkatController::class, 'postpone'])->name('fungsional.postpone');
            Route::post('/fungsional/reject', [AdminKenaikanPangkatController::class, 'reject'])->name('fungsional.reject');

            Route::get('/penyesuaian-ijazah', [AdminKenaikanPangkatController::class, 'penyesuaianIjazah'])->name('penyesuaian_ijazah');
            Route::post('/penyesuaian-ijazah/approve', [AdminKenaikanPangkatController::class, 'approve'])->name('penyesuaian_ijazah.approve');
            Route::post('/penyesuaian-ijazah/postpone', [AdminKenaikanPangkatController::class, 'postpone'])->name('penyesuaian_ijazah.postpone');
            Route::post('/penyesuaian-ijazah/reject', [AdminKenaikanPangkatController::class, 'reject'])->name('penyesuaian_ijazah.reject');

            Route::get('/struktural', [AdminKenaikanPangkatController::class, 'struktural'])->name('struktural');
            Route::post('/struktural/approve', [AdminKenaikanPangkatController::class, 'approve'])->name('struktural.approve');
            Route::post('/struktural/postpone', [AdminKenaikanPangkatController::class, 'postpone'])->name('struktural.postpone');
            Route::post('/struktural/reject', [AdminKenaikanPangkatController::class, 'reject'])->name('struktural.reject');

            Route::get('/reguler', [AdminKenaikanPangkatController::class, 'reguler'])->name('reguler');
            Route::post('/reguler/approve', [AdminKenaikanPangkatController::class, 'approve'])->name('reguler.approve');
            Route::post('/reguler/postpone', [AdminKenaikanPangkatController::class, 'postpone'])->name('reguler.postpone');
            Route::post('/reguler/reject', [AdminKenaikanPangkatController::class, 'reject'])->name('reguler.reject');
        });

        // Pensiun
        Route::prefix('pensiun')->name('pensiun.')->group(function () {
            Route::get('/bup', [AdminPensiunController::class, 'bup'])->name('bup');
            Route::get('/janda-duda-yatim', [AdminPensiunController::class, 'jandaDudaYatim'])->name('janda_duda_yatim');
            Route::get('/aps', [AdminPensiunController::class, 'aps'])->name('aps');
            Route::get('/meninggal', [AdminPensiunController::class, 'meninggal'])->name('meninggal');
            Route::get('/uzur', [AdminPensiunController::class, 'uzur'])->name('uzur');
            Route::get('/hilang', [AdminPensiunController::class, 'hilang'])->name('hilang');
            Route::get('/tanpa-ahli-waris', [AdminPensiunController::class, 'tanpaAhliWaris'])->name('taw');
        });

        // Pindah Instansi
        Route::prefix('pindah-instansi')->name('pindah.')->group(function () {
            Route::get('/masuk', [AdminPindahInstansiController::class, 'masuk'])->name('masuk');
            Route::get('/keluar', [AdminPindahInstansiController::class, 'keluar'])->name('keluar');
        });

        // Jabatan Fungsional
        Route::prefix('jabatan-fungsional')->name('jf.')->group(function () {
            Route::get('/pengangkatan', [AdminJabatanFungsionalController::class, 'pengangkatan'])->name('pengangkatan');
            Route::get('/pemberhentian', [AdminJabatanFungsionalController::class, 'pemberhentian'])->name('pemberhentian');
            Route::get('/naik-jenjang', [AdminJabatanFungsionalController::class, 'naikJenjang'])->name('naik_jenjang');
        });

        // Satyalancana
        Route::get('/satyalancana', [AdminSatyalancanaController::class, 'index'])->name('satyalancana');

        // Pencantuman Gelar
        Route::prefix('pencantuman-gelar')->name('gelar.')->group(function () {
            Route::get('/akademik', [AdminPencantumanGelarController::class, 'akademik'])->name('akademik');
            Route::get('/profesi', [AdminPencantumanGelarController::class, 'profesi'])->name('profesi');
        });

        // Menu Lainnya
        Route::get('/penugasan', [AdminPenugasanController::class, 'index'])->name('penugasan');
        Route::get('/perbaikan-data-asn', [AdminPenugasanController::class, 'index'])->name('perbaikan_data');
        Route::get('/tugas-belajar', [AdminPenugasanController::class, 'index'])->name('tugas_belajar');

        // Manajemen Akun (CRUD User)
        Route::get('/manajemen-akun', [ManajemenAkunController::class, 'index'])->name('manajemen_akun');

        // Cetak Surat
        Route::get('/cetak-surat', [AdminCetakSuratController::class, 'index'])->name('cetak_surat');
    });

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/admin', function () {
    return view('pages.admin.index');
});
