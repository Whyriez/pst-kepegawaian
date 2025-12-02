<?php

use App\Http\Controllers\Admin\CetakSuratController as AdminCetakSuratController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JabatanFungsionalController as AdminJabatanFungsionalController;
use App\Http\Controllers\Admin\KenaikanPangkatController as AdminKenaikanPangkatController;
use App\Http\Controllers\Admin\ManajemenAkunController;
use App\Http\Controllers\Admin\ManajemenDokumenController;
use App\Http\Controllers\Admin\PencantumanGelarController as AdminPencantumanGelarController;
use App\Http\Controllers\Admin\PensiunController as AdminPensiunController;
use App\Http\Controllers\Admin\PenugasanController as AdminPenugasanController;
use App\Http\Controllers\Admin\PerbaikanDataController as AdminPerbaikanDataController;
use App\Http\Controllers\Admin\PindahInstansiController as AdminPindahInstansiController;
use App\Http\Controllers\Admin\ProfilSatkerController;
use App\Http\Controllers\Admin\SatyalancanaController as AdminSatyalancanaController;
use App\Http\Controllers\Admin\TugasBelajarController as AdminTugasBelajarController;
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
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ProfileSatkerController;
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

        // Satuan Kerja Profile
        Route::get('/satuan-kerja', [ProfileSatkerController::class, 'index'])->name('satuan_kerja');

        Route::prefix('kenaikan-pangkat')->name('kp.')->group(function () {

            Route::get('/fungsional', [KenaikanPangkatController::class, 'fungsional'])->name('fungsional');
            Route::get('/fungsional/create', [KenaikanPangkatController::class, 'createFungsional'])->name('fungsional.create');
            Route::post('/fungsional/store', [KenaikanPangkatController::class, 'storeFungsional'])->name('fungsional.store');
            Route::get('/fungsional/edit', [KenaikanPangkatController::class, 'editFungsional'])->name('fungsional.edit');
            Route::put('/fungsional/update/{id}', [KenaikanPangkatController::class, 'updateFungsional'])->name('fungsional.update');


            Route::get('/penyesuaian-ijazah', [KenaikanPangkatController::class, 'penyesuaianIjazah'])->name('penyesuaian_ijazah');
            Route::get('/penyesuaian-ijazah/create', [KenaikanPangkatController::class, 'createPenyesuaianIjazah'])->name('penyesuaian_ijazah.create');
            Route::post('/penyesuaian-ijazah/store', [KenaikanPangkatController::class, 'storePenyesuaianIjazah'])->name('penyesuaian_ijazah.store');
            Route::get('/penyesuaian-ijazah/edit', [KenaikanPangkatController::class, 'editPenyesuaianIjazah'])->name('penyesuaian_ijazah.edit');
            Route::put('/penyesuaian-ijazah/update/{id}', [KenaikanPangkatController::class, 'updatePenyesuaianIjazah'])->name('penyesuaian_ijazah.update');

            Route::get('/reguler', [KenaikanPangkatController::class, 'reguler'])->name('reguler');
            Route::get('/reguler/create', [KenaikanPangkatController::class, 'createReguler'])->name('reguler.create');
            Route::post('/reguler/store', [KenaikanPangkatController::class, 'storeReguler'])->name('reguler.store');
            Route::get('/reguler/edit', [KenaikanPangkatController::class, 'editReguler'])->name('reguler.edit');
            Route::put('/reguler/update/{id}', [KenaikanPangkatController::class, 'updateReguler'])->name('reguler.update');

            // URL: /kenaikan-pangkat/struktural
            Route::get('/struktural', [KenaikanPangkatController::class, 'struktural'])->name('struktural');
            Route::get('/struktural/create', [KenaikanPangkatController::class, 'createStruktural'])->name('struktural.create');
            Route::post('/struktural/store', [KenaikanPangkatController::class, 'storeStruktural'])->name('struktural.store');
            Route::get('/struktural/edit', [KenaikanPangkatController::class, 'editStruktural'])->name('struktural.edit');
            Route::put('/struktural/update/{id}', [KenaikanPangkatController::class, 'updateStruktural'])->name('struktural.update');

            Route::get('/ajax/cek-nip/{nip}', [KenaikanPangkatController::class, 'cekNip'])->name('ajax.cek-nip');
        });

        Route::prefix('pensiun')->name('pensiun.')->group(function () {

            // URL: /pensiun/bup
            Route::get('/bup', [PensiunController::class, 'bup'])->name('bup');
            Route::get('/bup/create', [PensiunController::class, 'createBup'])->name('bup.create');
            Route::post('/bup/store', [PensiunController::class, 'storeBup'])->name('bup.store');
            Route::get('/bup/edit', [PensiunController::class, 'editBup'])->name('bup.edit');
            Route::put('/bup/update/{id}', [PensiunController::class, 'updateBup'])->name('bup.update');

            // URL: /pensiun/janda-duda-yatim
            Route::get('/janda-duda-yatim', [PensiunController::class, 'jandaDudaYatim'])->name('janda_duda_yatim');
            Route::get('/janda-duda-yatim/create', [PensiunController::class, 'createJandaDudaYatim'])->name('janda_duda_yatim.create');
            Route::post('/janda-duda-yatim/store', [PensiunController::class, 'storeJandaDudaYatim'])->name('janda_duda_yatim.store');
            Route::get('/janda-duda-yatim/edit', [PensiunController::class, 'editJandaDudaYatim'])->name('janda_duda_yatim.edit');
            Route::put('/janda-duda-yatim/update/{id}', [PensiunController::class, 'updateJandaDudaYatim'])->name('janda_duda_yatim.update');

            // URL: /pensiun/aps
            Route::get('/aps', [PensiunController::class, 'aps'])->name('aps');
            Route::get('/aps/create', [PensiunController::class, 'createAps'])->name('aps.create');
            Route::post('/aps/store', [PensiunController::class, 'storeAps'])->name('aps.store');
            Route::get('/aps/edit', [PensiunController::class, 'editAps'])->name('aps.edit');
            Route::put('/aps/update/{id}', [PensiunController::class, 'updateAps'])->name('aps.update');

            // URL: /pensiun/meninggal
            Route::get('/meninggal', [PensiunController::class, 'meninggal'])->name('meninggal');
            Route::get('/meninggal/create', [PensiunController::class, 'createMeninggal'])->name('meninggal.create');
            Route::post('/meninggal/store', [PensiunController::class, 'storeMeninggal'])->name('meninggal.store');
            Route::get('/meninggal/edit', [PensiunController::class, 'editMeninggal'])->name('meninggal.edit');
            Route::put('/meninggal/update/{id}', [PensiunController::class, 'updateMeninggal'])->name('meninggal.update');

            // URL: /pensiun/uzur
            Route::get('/uzur', [PensiunController::class, 'uzur'])->name('uzur');
            Route::get('/uzur/create', [PensiunController::class, 'createUzur'])->name('uzur.create');
            Route::post('/uzur/store', [PensiunController::class, 'storeUzur'])->name('uzur.store');
            Route::get('/uzur/edit', [PensiunController::class, 'editUzur'])->name('uzur.edit');
            Route::put('/uzur/update/{id}', [PensiunController::class, 'updateUzur'])->name('uzur.update');

            // URL: /pensiun/hilang
            Route::get('/hilang', [PensiunController::class, 'hilang'])->name('hilang');
            Route::get('/hilang/create', [PensiunController::class, 'createHilang'])->name('hilang.create');
            Route::post('/hilang/store', [PensiunController::class, 'storeHilang'])->name('hilang.store');
            Route::get('/hilang/edit', [PensiunController::class, 'editHilang'])->name('hilang.edit');
            Route::put('/hilang/update/{id}', [PensiunController::class, 'updateHilang'])->name('hilang.update');

            // URL: /pensiun/tanpa-ahli-waris
            Route::get('/tanpa-ahli-waris', [PensiunController::class, 'tanpaAhliWaris'])->name('taw');
            Route::get('/tanpa-ahli-waris/create', [PensiunController::class, 'createTanpaAhliWaris'])->name('taw.create');
            Route::post('/tanpa-ahli-waris/store', [PensiunController::class, 'storeTanpaAhliWaris'])->name('taw.store');
            Route::get('/tanpa-ahli-waris/edit', [PensiunController::class, 'editTanpaAhliWaris'])->name('taw.edit');
            Route::put('/tanpa-ahli-waris/update/{id}', [PensiunController::class, 'updateTanpaAhliWaris'])->name('taw.update');
        });

        Route::prefix('pindah-instansi')->name('pindah.')->group(function () {

            // URL: /pindah-instansi/masuk
            Route::get('/masuk', [PindahInstansiController::class, 'masuk'])->name('masuk');
            Route::get('/masuk/create', [PindahInstansiController::class, 'createMasuk'])->name('masuk.create');
            Route::post('/masuk/store', [PindahInstansiController::class, 'storeMasuk'])->name('masuk.store');
            Route::get('/masuk/edit', [PindahInstansiController::class, 'editMasuk'])->name('masuk.edit');
            Route::put('/masuk/update/{id}', [PindahInstansiController::class, 'updateMasuk'])->name('masuk.update');

            // URL: /pindah-instansi/keluar
            Route::get('/keluar', [PindahInstansiController::class, 'keluar'])->name('keluar');
            Route::get('/keluar/create', [PindahInstansiController::class, 'createKeluar'])->name('keluar.create');
            Route::post('/keluar/store', [PindahInstansiController::class, 'storeKeluar'])->name('keluar.store');
            Route::get('/keluar/edit', [PindahInstansiController::class, 'editKeluar'])->name('keluar.edit');
            Route::put('/keluar/update/{id}', [PindahInstansiController::class, 'updateKeluar'])->name('keluar.update');
        });

        Route::prefix('jabatan-fungsional')->name('jf.')->group(function () {

            // URL: /jabatan-fungsional/pengangkatan
            Route::get('/pengangkatan', [JabatanFungsionalController::class, 'pengangkatan'])->name('pengangkatan');
            Route::get('/pengangkatan/create', [JabatanFungsionalController::class, 'createPengangkatan'])->name('pengangkatan.create');
            Route::post('/pengangkatan/store', [JabatanFungsionalController::class, 'storePengangkatan'])->name('pengangkatan.store');
            Route::get('/pengangkatan/edit', [JabatanFungsionalController::class, 'editPengangkatan'])->name('pengangkatan.edit');
            Route::put('/pengangkatan/update/{id}', [JabatanFungsionalController::class, 'updatePengangkatan'])->name('pengangkatan.update');

            // URL: /jabatan-fungsional/pemberhentian
            Route::get('/pemberhentian', [JabatanFungsionalController::class, 'pemberhentian'])->name('pemberhentian');
            Route::get('/pemberhentian/create', [JabatanFungsionalController::class, 'createPemberhentian'])->name('pemberhentian.create');
            Route::post('/pemberhentian/store', [JabatanFungsionalController::class, 'storePemberhentian'])->name('pemberhentian.store');
            Route::get('/pemberhentian/edit', [JabatanFungsionalController::class, 'editPemberhentian'])->name('pemberhentian.edit');
            Route::put('/pemberhentian/update/{id}', [JabatanFungsionalController::class, 'updatePemberhentian'])->name('pemberhentian.update');

            // URL: /jabatan-fungsional/naik-jenjang
            Route::get('/naik-jenjang', [JabatanFungsionalController::class, 'naikJenjang'])->name('naik_jenjang');
            Route::get('/naik-jenjang/create', [JabatanFungsionalController::class, 'createNaikJenjang'])->name('naik_jenjang.create');
            Route::post('/naik-jenjang/store', [JabatanFungsionalController::class, 'storeNaikJenjang'])->name('naik_jenjang.store');
            Route::get('/naik-jenjang/edit', [JabatanFungsionalController::class, 'editNaikJenjang'])->name('naik_jenjang.edit');
            Route::put('/naik-jenjang/update/{id}', [JabatanFungsionalController::class, 'updateNaikJenjang'])->name('naik_jenjang.update');
        });

        Route::prefix('pencantuman-gelar')->name('gelar.')->group(function () {
            // URL: /pencantuman-gelar/akademik
            Route::get('/akademik', [PencantumanGelarController::class, 'akademik'])->name('akademik');
            Route::get('/akademik/create', [PencantumanGelarController::class, 'createAkademik'])->name('akademik.create');
            Route::post('/akademik/store', [PencantumanGelarController::class, 'storeAkademik'])->name('akademik.store');
            Route::get('/akademik/edit', [PencantumanGelarController::class, 'editAkademik'])->name('akademik.edit');
            Route::put('/akademik/update/{id}', [PencantumanGelarController::class, 'updateAkademik'])->name('akademik.update');

            // URL: /pencantuman-gelar/profesi
            Route::get('/profesi', [PencantumanGelarController::class, 'profesi'])->name('profesi');
            Route::get('/profesi/create', [PencantumanGelarController::class, 'createProfesi'])->name('profesi.create');
            Route::post('/profesi/store', [PencantumanGelarController::class, 'storeProfesi'])->name('profesi.store');
            Route::get('/profesi/edit', [PencantumanGelarController::class, 'editProfesi'])->name('profesi.edit');
            Route::put('/profesi/update/{id}', [PencantumanGelarController::class, 'updateProfesi'])->name('profesi.update');
        });

        Route::get('/satyalancana', [SatyalancanaController::class, 'index'])->name('satyalancana');
        Route::get('/satyalancana/create', [SatyalancanaController::class, 'create'])->name('satyalancana.create');
        Route::post('/satyalancana/store', [SatyalancanaController::class, 'store'])->name('satyalancana.store');
        Route::get('/satyalancana/edit', [SatyalancanaController::class, 'edit'])->name('satyalancana.edit');
        Route::put('/satyalancana/update/{id}', [SatyalancanaController::class, 'update'])->name('satyalancana.update');

        // 1. Penugasan
        Route::get('/penugasan', [PenugasanController::class, 'index'])->name('penugasan');
        Route::get('/penugasan/create', [PenugasanController::class, 'create'])->name('penugasan.create');
        Route::post('/penugasan/store', [PenugasanController::class, 'store'])->name('penugasan.store');
        Route::get('/penugasan/edit', [PenugasanController::class, 'edit'])->name('penugasan.edit');
        Route::put('/penugasan/update/{id}', [PenugasanController::class, 'update'])->name('penugasan.update');

        // 2. Perbaikan Data ASN
        Route::get('/perbaikan-data-asn', [PerbaikanDataController::class, 'index'])->name('perbaikan_data');
        Route::get('/perbaikan-data-asn/create', [PerbaikanDataController::class, 'create'])->name('perbaikan_data.create');
        Route::post('/perbaikan-data-asn/store', [PerbaikanDataController::class, 'store'])->name('perbaikan_data.store');
        Route::get('/perbaikan-data-asn/edit', [PerbaikanDataController::class, 'edit'])->name('perbaikan_data.edit');
        Route::put('/perbaikan-data-asn/update/{id}', [PerbaikanDataController::class, 'update'])->name('perbaikan_data.update');

        // 3. Tugas Belajar
        Route::get('/tugas-belajar', [TugasBelajarController::class, 'index'])->name('tugas_belajar');
        Route::get('/tugas-belajar/create', [TugasBelajarController::class, 'create'])->name('tugas_belajar.create');
        Route::post('/tugas-belajar/store', [TugasBelajarController::class, 'store'])->name('tugas_belajar.store');
        Route::get('/tugas-belajar/edit', [TugasBelajarController::class, 'edit'])->name('tugas_belajar.edit');
        Route::put('/tugas-belajar/update/{id}', [TugasBelajarController::class, 'update'])->name('tugas_belajar.update');

        // 3. Konversi AK Pendidikan
        Route::get('/konversi-ak-pendidikan', [\App\Http\Controllers\User\KonversiAKPendidikanController::class, 'index'])->name('konversi_ak_pendidikan');
        Route::get('/konversi-ak-pendidikan/create', [\App\Http\Controllers\User\KonversiAKPendidikanController::class, 'create'])->name('konversi_ak_pendidikan.create');
        Route::post('/konversi-ak-pendidikan/store', [\App\Http\Controllers\User\KonversiAKPendidikanController::class, 'store'])->name('konversi_ak_pendidikan.store');
        Route::get('/konversi-ak-pendidikan/edit', [\App\Http\Controllers\User\KonversiAKPendidikanController::class, 'edit'])->name('konversi_ak_pendidikan.edit');
        Route::put('/konversi-ak-pendidikan/update/{id}', [\App\Http\Controllers\User\KonversiAKPendidikanController::class, 'update'])->name('konversi_ak_pendidikan.update');

        // 4. Cetak Surat
        Route::get('/cetak-surat', [CetakSuratController::class, 'index'])->name('cetak_surat');
    });


    Route::middleware(['auth', 'cekrole:admin'])->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Profil Satuan Kerja
        Route::get('/profil-satker', [ProfilSatkerController::class, 'index'])->name('profil_satker');
        Route::put('/profil-satker/update', [ProfilSatkerController::class, 'update'])->name('profil_satker.update');

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
            Route::post('/bup/approve', [AdminPensiunController::class, 'approve'])->name('bup.approve');
            Route::post('/bup/postpone', [AdminPensiunController::class, 'postpone'])->name('bup.postpone');
            Route::post('/bup/reject', [AdminPensiunController::class, 'reject'])->name('bup.reject');

            Route::get('/janda-duda-yatim', [AdminPensiunController::class, 'jandaDudaYatim'])->name('janda_duda_yatim');
            Route::post('/janda-duda-yatim/approve', [AdminPensiunController::class, 'approve'])->name('janda_duda_yatim.approve');
            Route::post('/janda-duda-yatim/postpone', [AdminPensiunController::class, 'postpone'])->name('janda_duda_yatim.postpone');
            Route::post('/janda-duda-yatim/reject', [AdminPensiunController::class, 'reject'])->name('janda_duda_yatim.reject');

            Route::get('/aps', [AdminPensiunController::class, 'aps'])->name('aps');
            Route::post('/aps/approve', [AdminPensiunController::class, 'approve'])->name('aps.approve');
            Route::post('/aps/postpone', [AdminPensiunController::class, 'postpone'])->name('aps.postpone');
            Route::post('/aps/reject', [AdminPensiunController::class, 'reject'])->name('aps.reject');

            Route::get('/meninggal', [AdminPensiunController::class, 'meninggal'])->name('meninggal');
            Route::post('/meninggal/approve', [AdminPensiunController::class, 'approve'])->name('meninggal.approve');
            Route::post('/meninggal/postpone', [AdminPensiunController::class, 'postpone'])->name('meninggal.postpone');
            Route::post('/meninggal/reject', [AdminPensiunController::class, 'reject'])->name('meninggal.reject');

            Route::get('/uzur', [AdminPensiunController::class, 'uzur'])->name('uzur');
            Route::post('/uzur/approve', [AdminPensiunController::class, 'approve'])->name('uzur.approve');
            Route::post('/uzur/postpone', [AdminPensiunController::class, 'postpone'])->name('uzur.postpone');
            Route::post('/uzur/reject', [AdminPensiunController::class, 'reject'])->name('uzur.reject');

            Route::get('/hilang', [AdminPensiunController::class, 'hilang'])->name('hilang');
            Route::post('/hilang/approve', [AdminPensiunController::class, 'approve'])->name('hilang.approve');
            Route::post('/hilang/postpone', [AdminPensiunController::class, 'postpone'])->name('hilang.postpone');
            Route::post('/hilang/reject', [AdminPensiunController::class, 'reject'])->name('hilang.reject');

            Route::get('/tanpa-ahli-waris', [AdminPensiunController::class, 'tanpaAhliWaris'])->name('taw');
            Route::post('/tanpa-ahli-waris/approve', [AdminPensiunController::class, 'approve'])->name('taw.approve');
            Route::post('/tanpa-ahli-waris/postpone', [AdminPensiunController::class, 'postpone'])->name('taw.postpone');
            Route::post('/tanpa-ahli-waris/reject', [AdminPensiunController::class, 'reject'])->name('taw.reject');
        });

        // Pindah Instansi
        Route::prefix('pindah-instansi')->name('pindah.')->group(function () {
            Route::get('/masuk', [AdminPindahInstansiController::class, 'masuk'])->name('masuk');
            Route::post('/masuk/approve', [AdminPindahInstansiController::class, 'approve'])->name('masuk.approve');
            Route::post('/masuk/postpone', [AdminPindahInstansiController::class, 'postpone'])->name('masuk.postpone');
            Route::post('/masuk/reject', [AdminPindahInstansiController::class, 'reject'])->name('masuk.reject');

            Route::get('/keluar', [AdminPindahInstansiController::class, 'keluar'])->name('keluar');
            Route::post('/keluar/approve', [AdminPindahInstansiController::class, 'approve'])->name('keluar.approve');
            Route::post('/keluar/postpone', [AdminPindahInstansiController::class, 'postpone'])->name('keluar.postpone');
            Route::post('/keluar/reject', [AdminPindahInstansiController::class, 'reject'])->name('keluar.reject');
        });

        // Jabatan Fungsional
        Route::prefix('jabatan-fungsional')->name('jf.')->group(function () {
            Route::get('/pengangkatan', [AdminJabatanFungsionalController::class, 'pengangkatan'])->name('pengangkatan');
            Route::post('/pengangkatan/approve', [AdminJabatanFungsionalController::class, 'approve'])->name('pengangkatan.approve');
            Route::post('/pengangkatan/postpone', [AdminJabatanFungsionalController::class, 'postpone'])->name('pengangkatan.postpone');
            Route::post('/pengangkatan/reject', [AdminJabatanFungsionalController::class, 'reject'])->name('pengangkatan.reject');

            Route::get('/pemberhentian', [AdminJabatanFungsionalController::class, 'pemberhentian'])->name('pemberhentian');
            Route::post('/pemberhentian/approve', [AdminJabatanFungsionalController::class, 'approve'])->name('pemberhentian.approve');
            Route::post('/pemberhentian/postpone', [AdminJabatanFungsionalController::class, 'postpone'])->name('pemberhentian.postpone');
            Route::post('/pemberhentian/reject', [AdminJabatanFungsionalController::class, 'reject'])->name('pemberhentian.reject');

            Route::get('/naik-jenjang', [AdminJabatanFungsionalController::class, 'naikJenjang'])->name('naik_jenjang');
            Route::post('/naik-jenjang/approve', [AdminJabatanFungsionalController::class, 'approve'])->name('naik_jenjang.approve');
            Route::post('/naik-jenjang/postpone', [AdminJabatanFungsionalController::class, 'postpone'])->name('naik_jenjang.postpone');
            Route::post('/naik-jenjang/reject', [AdminJabatanFungsionalController::class, 'reject'])->name('naik_jenjang.reject');
        });

        // Satyalancana
        Route::get('/satyalancana', [AdminSatyalancanaController::class, 'index'])->name('satyalancana');
        Route::post('/satyalancana/approve', [AdminSatyalancanaController::class, 'approve'])->name('satyalancana.approve');
        Route::post('/satyalancana/postpone', [AdminSatyalancanaController::class, 'postpone'])->name('satyalancana.postpone');
        Route::post('/satyalancana/reject', [AdminSatyalancanaController::class, 'reject'])->name('satyalancana.reject');

        // Pencantuman Gelar
        Route::prefix('pencantuman-gelar')->name('gelar.')->group(function () {
            Route::get('/akademik', [AdminPencantumanGelarController::class, 'akademik'])->name('akademik');
            Route::post('/akademik/approve', [AdminPencantumanGelarController::class, 'approve'])->name('akademik.approve');
            Route::post('/akademik/postpone', [AdminPencantumanGelarController::class, 'postpone'])->name('akademik.postpone');
            Route::post('/akademik/reject', [AdminPencantumanGelarController::class, 'reject'])->name('akademik.reject');

            Route::get('/profesi', [AdminPencantumanGelarController::class, 'profesi'])->name('profesi');
            Route::post('/profesi/approve', [AdminPencantumanGelarController::class, 'approve'])->name('profesi.approve');
            Route::post('/profesi/postpone', [AdminPencantumanGelarController::class, 'postpone'])->name('profesi.postpone');
            Route::post('/profesi/reject', [AdminPencantumanGelarController::class, 'reject'])->name('profesi.reject');
        });

        // Menu Lainnya
        Route::get('/konversi-ak-pendidikan', [\App\Http\Controllers\Admin\KonversiAKPendidikanController::class, 'index'])->name('konversi_ak_pendidikan');
        Route::post('/konversi-ak-pendidikan/approve', [\App\Http\Controllers\Admin\KonversiAKPendidikanController::class, 'approve'])->name('konversi_ak_pendidikan.approve');
        Route::post('/konversi-ak-pendidikan/postpone', [\App\Http\Controllers\Admin\KonversiAKPendidikanController::class, 'postpone'])->name('konversi_ak_pendidikan.postpone');
        Route::post('/konversi-ak-pendidikan/reject', [\App\Http\Controllers\Admin\KonversiAKPendidikanController::class, 'reject'])->name('konversi_ak_pendidikan.reject');

        Route::get('/penugasan', [AdminPenugasanController::class, 'index'])->name('penugasan');
        Route::post('/penugasan/approve', [AdminPenugasanController::class, 'approve'])->name('penugasan.approve');
        Route::post('/penugasan/postpone', [AdminPenugasanController::class, 'postpone'])->name('penugasan.postpone');
        Route::post('/penugasan/reject', [AdminPenugasanController::class, 'reject'])->name('penugasan.reject');

        Route::get('/perbaikan-data-asn', [AdminPerbaikanDataController::class, 'index'])->name('perbaikan_data');
        Route::post('/perbaikan-data-asn/approve', [AdminPerbaikanDataController::class, 'approve'])->name('perbaikan_data.approve');
        Route::post('/perbaikan-data-asn/postpone', [AdminPerbaikanDataController::class, 'postpone'])->name('perbaikan_data.postpone');
        Route::post('/perbaikan-data-asn/reject', [AdminPerbaikanDataController::class, 'reject'])->name('perbaikan_data.reject');

        Route::get('/tugas-belajar', [AdminPenugasanController::class, 'index'])->name('tugas_belajar');
        Route::post('/tugas-belajar/approve', [AdminTugasBelajarController::class, 'approve'])->name('tugas_belajar.approve');
        Route::post('/tugas-belajar/postpone', [AdminTugasBelajarController::class, 'postpone'])->name('tugas_belajar.postpone');
        Route::post('/tugas-belajar/reject', [AdminTugasBelajarController::class, 'reject'])->name('tugas_belajar.reject');

        // Manajemen Akun (CRUD User)
        Route::prefix('manajemen-akun')->name('manajemen_akun.')->group(function () {
            Route::get('/', [ManajemenAkunController::class, 'index'])->name('index');
            Route::post('/store', [ManajemenAkunController::class, 'store'])->name('store');
            Route::put('/update/{id}', [ManajemenAkunController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [ManajemenAkunController::class, 'destroy'])->name('destroy');
        });

        // Manajemen Dokumen (CRUD Dokumen)
        Route::prefix('manajemen-dokumen')->name('manajemen_dokumen.')->group(function () {
            // 1. Kelola Jenis Layanan (Induk)
            Route::get('/', [ManajemenDokumenController::class, 'index'])->name('index');
            Route::post('/layanan/store', [ManajemenDokumenController::class, 'storeLayanan'])->name('layanan.store');
            Route::put('/layanan/update/{id}', [ManajemenDokumenController::class, 'updateLayanan'])->name('layanan.update');
            Route::delete('/layanan/destroy/{id}', [ManajemenDokumenController::class, 'destroyLayanan'])->name('layanan.destroy');

            // 2. Kelola Syarat Dokumen (Anak) - Berdasarkan ID Layanan
            Route::get('/{id}/syarat', [ManajemenDokumenController::class, 'showSyarat'])->name('syarat');
            Route::post('/syarat/store', [ManajemenDokumenController::class, 'storeSyarat'])->name('syarat.store');
            Route::put('/syarat/update/{id}', [ManajemenDokumenController::class, 'updateSyarat'])->name('syarat.update');
            Route::delete('/syarat/destroy/{id}', [ManajemenDokumenController::class, 'destroySyarat'])->name('syarat.destroy');
        });

        // Cetak Surat
        Route::prefix('cetak-surat')->name('cetak_surat.')->group(function () {
            Route::get('/pengantar', [AdminCetakSuratController::class, 'pengantar'])->name('pengantar');

            Route::get('/sptjm', [AdminCetakSuratController::class, 'sptjm'])->name('sptjm');
        });
    });

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/admin', function () {
    return view('pages.admin.index');
});
