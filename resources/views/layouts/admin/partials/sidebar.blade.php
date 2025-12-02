<div class="sidebar" id="sidebar">
    <div class="logo">
        <div class="logo-icon">
            <i class="fas fa-landmark"></i>
        </div>
        <div class="logo-text">
            <h1 class="fs-4 fw-bold text-white">PTSP Gorontalo</h1>
            <small class="text-light">Admin Panel v2.0</small>
        </div>
    </div>

    <div class="sidebar-search px-3 mb-3">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-dark border-0">
                <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control bg-dark border-0 text-white" placeholder="Cari menu..."
                id="sidebarSearch">
        </div>
    </div>

    <ul class="menu" id="mainMenu">
        {{-- Dashboard --}}
        <li class="">
            <a href="{{ route('admin.dashboard') }}"
                class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- Profil Satuan Kerja --}}
        <li class="">
            <a href="{{ route('admin.profil_satker') }}"
                class="menu-item {{ request()->routeIs('admin.profil_satker') ? 'active' : '' }}">
                <i class="fas fa-building"></i>
                <span>Profil Satuan Kerja</span>
            </a>
        </li>

        {{-- ================================================================================== --}}
        {{-- MENU: KENAIKAN PANGKAT --}}
        {{-- ================================================================================== --}}
        <li class="has-submenu {{ request()->routeIs('admin.kp.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-id-card"></i>
                    <span>Kenaikan Pangkat</span>
                </div>
                {{-- BADGE INDUK (Total Kategori) --}}
                @if (isset($badgeKategori['Kenaikan Pangkat']) && $badgeKategori['Kenaikan Pangkat'] > 0)
                    <span
                        class="badge bg-danger rounded-pill ms-auto me-2">{{ $badgeKategori['Kenaikan Pangkat'] }}</span>
                @endif
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu" style="{{ request()->routeIs('admin.kp.*') ? 'display:block;' : 'display:none;' }}">
                <li>
                    <a href="{{ route('admin.kp.fungsional') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.kp.fungsional') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i
                                class="fas fa-user-tie me-2"></i><span>Fungsional</span></div>
                        {{-- BADGE ANAK (Slug: kp-fungsional) --}}
                        @if (isset($badgeSlug['kp-fungsional']) && $badgeSlug['kp-fungsional'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['kp-fungsional'] }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kp.penyesuaian_ijazah') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.kp.penyesuaian_ijazah') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i
                                class="fas fa-graduation-cap me-2"></i><span>Penyesuaian Ijazah</span></div>
                        @if (isset($badgeSlug['kp-pi']) && $badgeSlug['kp-pi'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['kp-pi'] }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kp.struktural') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.kp.struktural') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i
                                class="fas fa-sitemap me-2"></i><span>Struktural</span></div>
                        @if (isset($badgeSlug['kp-struktural']) && $badgeSlug['kp-struktural'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['kp-struktural'] }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kp.reguler') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.kp.reguler') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i
                                class="fas fa-chart-line me-2"></i><span>Reguler</span></div>
                        @if (isset($badgeSlug['kp-reguler']) && $badgeSlug['kp-reguler'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['kp-reguler'] }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </li>

        {{-- ================================================================================== --}}
        {{-- MENU: PENSIUN --}}
        {{-- ================================================================================== --}}
        <li class="has-submenu {{ request()->routeIs('admin.pensiun.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-clock"></i>
                    <span>Pensiun</span>
                </div>
                {{-- BADGE INDUK --}}
                @if (isset($badgeKategori['Pensiun']) && $badgeKategori['Pensiun'] > 0)
                    <span class="badge bg-danger rounded-pill ms-auto me-2">{{ $badgeKategori['Pensiun'] }}</span>
                @endif
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu"
                style="{{ request()->routeIs('admin.pensiun.*') ? 'display:block;' : 'display:none;' }}">
                <li>
                    <a href="{{ route('admin.pensiun.bup') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.pensiun.bup') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i class="fas fa-hourglass-end me-2"></i><span>Batas Usia
                                Pensiun</span></div>
                        {{-- BADGE ANAK --}}
                        @if (isset($badgeSlug['pensiun-bup']) && $badgeSlug['pensiun-bup'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['pensiun-bup'] }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pensiun.janda_duda_yatim') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.pensiun.janda_duda_yatim') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i
                                class="fas fa-users me-2"></i><span>Janda/Duda/Yatim</span></div>
                        @if (isset($badgeSlug['pensiun-janda-duda']) && $badgeSlug['pensiun-janda-duda'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['pensiun-janda-duda'] }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pensiun.aps') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.pensiun.aps') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i class="fas fa-hand-paper me-2"></i><span>Atas
                                Permintaan Sendiri</span></div>
                        @if (isset($badgeSlug['pensiun-aps']) && $badgeSlug['pensiun-aps'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['pensiun-aps'] }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pensiun.meninggal') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.pensiun.meninggal') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i
                                class="fas fa-procedures me-2"></i><span>Meninggal</span></div>
                        @if (isset($badgeSlug['pensiun-meninggal']) && $badgeSlug['pensiun-meninggal'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['pensiun-meninggal'] }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pensiun.uzur') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.pensiun.uzur') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i class="fas fa-wheelchair me-2"></i><span>Uzur</span>
                        </div>
                        @if (isset($badgeSlug['pensiun-uzur']) && $badgeSlug['pensiun-uzur'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['pensiun-uzur'] }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pensiun.hilang') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.pensiun.hilang') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i
                                class="fas fa-search-location me-2"></i><span>Hilang</span></div>
                        @if (isset($badgeSlug['pensiun-hilang']) && $badgeSlug['pensiun-hilang'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['pensiun-hilang'] }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pensiun.taw') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.pensiun.taw') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i class="fas fa-user-slash me-2"></i><span>Tanpa Ahli
                                Waris</span></div>
                        @if (isset($badgeSlug['pensiun-taw']) && $badgeSlug['pensiun-taw'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['pensiun-taw'] }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </li>

        {{-- ================================================================================== --}}
        {{-- MENU: PINDAH INSTANSI --}}
        {{-- ================================================================================== --}}
        <li class="has-submenu {{ request()->routeIs('admin.pindah.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Pindah Instansi</span>
                </div>
                @if (isset($badgeKategori['Pindah Instansi']) && $badgeKategori['Pindah Instansi'] > 0)
                    <span
                        class="badge bg-danger rounded-pill ms-auto me-2">{{ $badgeKategori['Pindah Instansi'] }}</span>
                @endif
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu"
                style="{{ request()->routeIs('admin.pindah.*') ? 'display:block;' : 'display:none;' }}">
                <li>
                    <a href="{{ route('admin.pindah.masuk') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.pindah.masuk') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i class="fas fa-sign-in-alt me-2"></i><span>Masuk
                                Instansi</span></div>
                        @if (isset($badgeSlug['pindah-masuk']) && $badgeSlug['pindah-masuk'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['pindah-masuk'] }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pindah.keluar') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.pindah.keluar') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i class="fas fa-sign-out-alt me-2"></i><span>Keluar
                                Instansi</span></div>
                        @if (isset($badgeSlug['pindah-keluar']) && $badgeSlug['pindah-keluar'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['pindah-keluar'] }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </li>

        {{-- ================================================================================== --}}
        {{-- MENU: JABATAN FUNGSIONAL --}}
        {{-- ================================================================================== --}}
        <li class="has-submenu {{ request()->routeIs('admin.jf.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-briefcase"></i>
                    <span>Jabatan Fungsional</span>
                </div>
                @if (isset($badgeKategori['Jabatan Fungsional']) && $badgeKategori['Jabatan Fungsional'] > 0)
                    <span
                        class="badge bg-danger rounded-pill ms-auto me-2">{{ $badgeKategori['Jabatan Fungsional'] }}</span>
                @endif
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu" style="{{ request()->routeIs('admin.jf.*') ? 'display:block;' : 'display:none;' }}">
                <li>
                    <a href="{{ route('admin.jf.pengangkatan') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.jf.pengangkatan') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i
                                class="fas fa-user-plus me-2"></i><span>Pengangkatan</span></div>
                        @if (isset($badgeSlug['jf-pengangkatan']) && $badgeSlug['jf-pengangkatan'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['jf-pengangkatan'] }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.jf.pemberhentian') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.jf.pemberhentian') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i
                                class="fas fa-user-minus me-2"></i><span>Pemberhentian</span></div>
                        @if (isset($badgeSlug['jf-pemberhentian']) && $badgeSlug['jf-pemberhentian'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['jf-pemberhentian'] }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.jf.naik_jenjang') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.jf.naik_jenjang') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i class="fas fa-level-up-alt me-2"></i><span>Naik
                                Jenjang</span></div>
                        @if (isset($badgeSlug['jf-naik-jenjang']) && $badgeSlug['jf-naik-jenjang'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['jf-naik-jenjang'] }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </li>

        {{-- ================================================================================== --}}
        {{-- MENU: SINGLE LEVEL ITEMS --}}
        {{-- ================================================================================== --}}

        {{-- Satyalancana --}}
        <li class="">
            <a href="{{ route('admin.satyalancana') }}"
                class="menu-item {{ request()->routeIs('admin.satyalancana') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center flex-grow-1">
                    <i class="fas fa-medal"></i>
                    <span>Satyalancana</span>
                </div>
                {{-- Hanya cek Kategori saja --}}
                @if (isset($badgeSlug['satyalancana']) && $$badgeSlug['satyalancana'] > 0)
                    <span class="badge bg-danger rounded-pill flex-shrink-0">{{ $$badgeSlug['satyalancana'] }}</span>
                @endif
            </a>
        </li>

        {{-- Pencantuman Gelar --}}
        <li class="has-submenu {{ request()->routeIs('admin.gelar.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Pencantuman Gelar</span>
                </div>
                @if (isset($badgeKategori['Pencantuman Gelar']) && $badgeKategori['Pencantuman Gelar'] > 0)
                    <span
                        class="badge bg-danger rounded-pill ms-auto me-2">{{ $badgeKategori['Pencantuman Gelar'] }}</span>
                @endif
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu"
                style="{{ request()->routeIs('admin.gelar.*') ? 'display:block;' : 'display:none;' }}">
                <li>
                    <a href="{{ route('admin.gelar.akademik') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.gelar.akademik') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i
                                class="fas fa-university me-2"></i><span>Akademik</span></div>
                        @if (isset($badgeSlug['gelar-akademik']) && $badgeSlug['gelar-akademik'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['gelar-akademik'] }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.gelar.profesi') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.gelar.profesi') ? 'active' : '' }}">
                        <div class="d-flex align-items-center"><i class="fas fa-user-md me-2"></i><span>Profesi</span>
                        </div>
                        @if (isset($badgeSlug['gelar-profesi']) && $badgeSlug['gelar-profesi'] > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7em;">{{ $badgeSlug['gelar-profesi'] }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </li>

        {{-- Konversi AK Pendidikan --}}
        <li class="">
            <a href="{{ route('admin.konversi_ak_pendidikan') }}"
               class="menu-item {{ request()->routeIs('admin.konversi_ak_pendidikan') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-calculator"></i>
                    <span>Konversi AK Pendidikan</span>
                </div>
                @if (isset($badgeSlug['konversi-ak-pendidikan']) && $badgeSlug['konversi-ak-pendidikan'] > 0)
                    <span class="badge bg-danger rounded-pill">{{ $badgeSlug['konversi-ak-pendidikan'] }}</span>
                @endif
            </a>
        </li>

        {{-- Penugasan --}}
        <li class="">
            <a href="{{ route('admin.penugasan') }}"
                class="menu-item {{ request()->routeIs('admin.penugasan') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center flex-grow-1">
                    <i class="fas fa-tasks"></i>
                    <span>Penugasan</span>
                </div>
                @if (isset($badgeSlug['penugasan']) && $badgeSlug['penugasan'] > 0)
                    <span class="badge bg-danger rounded-pill flex-shrink-0">{{ $badgeSlug['penugasan'] }}</span>
                @endif
            </a>
        </li>

        {{-- Perbaikan Data ASN --}}
        <li class="">
            <a href="{{ route('admin.perbaikan_data') }}"
                class="menu-item {{ request()->routeIs('admin.perbaikan_data') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center flex-grow-1">
                    <i class="fas fa-database"></i>
                    <span>Perbaikan Data ASN</span>
                </div>
                @if (isset($badgeSlug['perbaikan-data-asn']) && $badgeSlug['perbaikan-data-asn'] > 0)
                    <span class="badge bg-danger rounded-pill flex-shrink-0">{{ $badgeSlug['perbaikan-data-asn'] }}</span>
                @endif
            </a>
        </li>

        {{-- Tugas Belajar --}}
        <li class="">
            <a href="{{ route('admin.tugas_belajar') }}"
                class="menu-item {{ request()->routeIs('admin.tugas_belajar') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center flex-grow-1">
                    <i class="fas fa-book-reader"></i>
                    <span>Tugas Belajar</span>
                </div>
                @if (isset($badgeSlug['tugas-belajar']) && $badgeSlug['tugas-belajar'] > 0)
                    <span class="badge bg-danger rounded-pill flex-shrink-0">{{ $badgeSlug['tugas-belajar'] }}</span>
                @endif
            </a>
        </li>

        {{-- Manajemen Akun (KHUSUS ADMIN) --}}
        <li class="">
            <a href="{{ route('admin.manajemen_akun.index') }}"
                class="menu-item {{ request()->routeIs('admin.manajemen_akun.index') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i>
                <span>Manajemen Akun</span>
            </a>
        </li>

        {{-- Manajemen Dokumen (KHUSUS ADMIN) --}}
        <li class="">
            <a href="{{ route('admin.manajemen_dokumen.index') }}"
                class="menu-item {{ request()->routeIs('admin.manajemen_dokumen.index') ? 'active' : '' }}">
                <i class="fas fa-file"></i>
                <span>Manajemen Dokumen</span>
            </a>
        </li>


        {{-- Cetak Surat --}}
        <li class="has-submenu {{ request()->routeIs('admin.cetak_surat.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    {{-- Ganti wisuda (graduation-cap) jadi Print --}}
                    <i class="fas fa-print"></i>
                    <span>Cetak Surat</span>
                </div>
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu"
                style="{{ request()->routeIs('admin.cetak_surat.*') ? 'display:block;' : 'display:none;' }}">

                {{-- Surat Pengantar --}}
                <li>
                    <a href="{{ route('admin.cetak_surat.pengantar') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.cetak_surat.pengantar') ? 'active' : '' }}">
                        {{-- Ganti university jadi amplop terbuka (surat) --}}
                        <div class="d-flex align-items-center">
                            <i class="fas fa-envelope-open-text me-2"></i>
                            <span>Surat Pengantar</span>
                        </div>
                    </a>
                </li>

                {{-- SPTJM --}}
                <li>
                    <a href="{{ route('admin.cetak_surat.sptjm') }}"
                        class="submenu-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.cetak_surat.sptjm') ? 'active' : '' }}">
                        {{-- Ganti dokter (user-md) jadi file signature (tanda tangan tanggung jawab) --}}
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-signature me-2"></i>
                            <span>SPTJM</span>
                        </div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Logout Form --}}
        <li>
            <form action="{{ route('logout') }}" method="POST" id="logout-form-admin">
                @csrf
                <a href="#" class="menu-item logout-item"
                    onclick="event.preventDefault(); document.getElementById('logout-form-admin').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar Sistem</span>
                </a>
            </form>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="user-mini">
            {{-- Avatar Admin --}}
            <img src="https://ui-avatars.com/api/?name=Admin+PTSP&background=1a73e8&color=fff" alt="Admin">
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name ?? 'Administrator' }}</div>
                <small class="user-role">Super Administrator</small>
            </div>
            <div class="online-indicator"></div>
        </div>
    </div>
</div>
