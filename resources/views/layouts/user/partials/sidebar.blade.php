<div class="sidebar">
    <div class="logo-desktop">
        <h1 class="fs-5 fw-bold text-white text-center">PTSP Kepegawaian</h1>
    </div>

    <div class="logo-mobile">
        <h2>PTSP Kepegawaian</h2>
    </div>

    <ul class="menu">
        <li>
            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="{{ route('satuan_kerja') }}"
                class="menu-item {{ request()->routeIs('satuan_kerja') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>Profil Satuan Kerja</span>
            </a>
        </li>

        <li class="{{ request()->routeIs('kp.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-item has-submenu {{ request()->routeIs('kp.*') ? 'active' : '' }}">
                <i class="fas fa-id-card"></i>
                <span>Kenaikan Pangkat</span>
            </a>
            <ul class="submenu" style="{{ request()->routeIs('kp.*') ? 'display:block;' : '' }}">
                <li>
                    <a href="{{ route('kp.fungsional') }}"
                        class="submenu-item {{ request()->routeIs('kp.fungsional') ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i>
                        <span>Fungsional</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('kp.penyesuaian_ijazah') }}"
                        class="submenu-item {{ request()->routeIs('kp.penyesuaian_ijazah') ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i>
                        <span>Penyesuaian Ijazah</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('kp.struktural') }}"
                        class="submenu-item {{ request()->routeIs('kp.struktural') ? 'active' : '' }}">
                        <i class="fas fa-sitemap"></i>
                        <span>Struktural</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('kp.reguler') }}"
                        class="submenu-item {{ request()->routeIs('kp.reguler') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Reguler</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="{{ request()->routeIs('pensiun.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)"
                class="menu-item has-submenu {{ request()->routeIs('pensiun.*') ? 'active' : '' }}">
                <i class="fas fa-user-clock"></i>
                <span>Pensiun</span>
            </a>
            <ul class="submenu" style="{{ request()->routeIs('pensiun.*') ? 'display:block;' : '' }}">
                <li>
                    <a href="{{ route('pensiun.bup') }}"
                        class="submenu-item {{ request()->routeIs('pensiun.bup') ? 'active' : '' }}">
                        <i class="fas fa-user-clock"></i>
                        <span>Batas Usia Pensiun</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pensiun.janda_duda_yatim') }}"
                        class="submenu-item {{ request()->routeIs('pensiun.janda_duda_yatim') ? 'active' : '' }}">
                        <i class="fas fa-user-friends"></i>
                        <span>Janda/Duda/Yatim</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pensiun.aps') }}"
                        class="submenu-item {{ request()->routeIs('pensiun.aps') ? 'active' : '' }}">
                        <i class="fas fa-user-minus"></i>
                        <span>Atas Permintaan Sendiri</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pensiun.meninggal') }}"
                        class="submenu-item {{ request()->routeIs('pensiun.meninggal') ? 'active' : '' }}">
                        <i class="fas fa-skull-crossbones"></i>
                        <span>Meninggal</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pensiun.uzur') }}"
                        class="submenu-item {{ request()->routeIs('pensiun.uzur') ? 'active' : '' }}">
                        <i class="fas fa-wheelchair"></i>
                        <span>Uzur</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pensiun.hilang') }}"
                        class="submenu-item {{ request()->routeIs('pensiun.hilang') ? 'active' : '' }}">
                        <i class="fas fa-user-slash"></i>
                        <span>Hilang</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pensiun.taw') }}"
                        class="submenu-item {{ request()->routeIs('pensiun.taw') ? 'active' : '' }}">
                        <i class="fas fa-user-times"></i>
                        <span>Tanpa ahli waris</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="{{ request()->routeIs('pindah.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)"
                class="menu-item has-submenu {{ request()->routeIs('pindah.*') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt"></i>
                <span>Pindah Antar Instansi</span>
            </a>
            <ul class="submenu" style="{{ request()->routeIs('pindah.*') ? 'display:block;' : '' }}">
                <li>
                    <a href="{{ route('pindah.masuk') }}"
                        class="submenu-item {{ request()->routeIs('pindah.masuk') ? 'active' : '' }}">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Masuk Instansi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pindah.keluar') }}"
                        class="submenu-item {{ request()->routeIs('pindah.keluar') ? 'active' : '' }}">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar Instansi</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="{{ request()->routeIs('jf.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)"
                class="menu-item has-submenu {{ request()->routeIs('jf.*') ? 'active' : '' }}">
                <i class="fas fa-briefcase"></i>
                <span>Jabatan Fungsional</span>
            </a>
            <ul class="submenu" style="{{ request()->routeIs('jf.*') ? 'display:block;' : '' }}">
                <li>
                    <a href="{{ route('jf.pengangkatan') }}"
                        class="submenu-item {{ request()->routeIs('jf.pengangkatan') ? 'active' : '' }}">
                        <i class="fas fa-user-plus"></i>
                        <span>Pengangkatan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('jf.pemberhentian') }}"
                        class="submenu-item {{ request()->routeIs('jf.pemberhentian') ? 'active' : '' }}">
                        <i class="fas fa-user-times"></i>
                        <span>Pemberhentian</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('jf.naik_jenjang') }}"
                        class="submenu-item {{ request()->routeIs('jf.naik_jenjang') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Naik Jenjang</span>
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="{{ route('satyalancana') }}"
                class="menu-item {{ request()->routeIs('satyalancana') ? 'active' : '' }}">
                <i class="fas fa-medal"></i>
                <span>Satyalancana</span>
            </a>
        </li>

        <li class="{{ request()->routeIs('gelar.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)"
                class="menu-item has-submenu {{ request()->routeIs('gelar.*') ? 'active' : '' }}">
                <i class="fas fa-graduation-cap"></i>
                <span>Pencantuman Gelar</span>
            </a>
            <ul class="submenu" style="{{ request()->routeIs('gelar.*') ? 'display:block;' : '' }}">
                <li>
                    <a href="{{ route('gelar.akademik') }}"
                        class="submenu-item {{ request()->routeIs('gelar.akademik') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Akademik</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('gelar.profesi') }}"
                        class="submenu-item {{ request()->routeIs('gelar.profesi') ? 'active' : '' }}">
                        <i class="fas fa-user-md"></i>
                        <span>Profesi</span>
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="{{ route('konversi_ak_pendidikan') }}"
               class="menu-item {{ request()->routeIs('konversi_ak_pendidikan') ? 'active' : '' }}">
                <i class="fas fa-calculator"></i>
                <span>Konversi AK Pendidikan</span>
            </a>
        </li>

        <li>
            <a href="{{ route('penugasan') }}"
                class="menu-item {{ request()->routeIs('penugasan') ? 'active' : '' }}">
                <i class="fas fa-briefcase"></i>
                <span>Penugasan</span>
            </a>
        </li>

        <li>
            <a href="{{ route('perbaikan_data') }}"
                class="menu-item {{ request()->routeIs('perbaikan_data') ? 'active' : '' }}">
                <i class="fas fa-database"></i>
                <span>Perbaikan Data ASN</span>
            </a>
        </li>

        <li>
            <a href="{{ route('tugas_belajar') }}"
                class="menu-item {{ request()->routeIs('tugas_belajar') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i>
                <span>Tugas Belajar</span>
            </a>
        </li>

        <li>
            <a href="{{ route('cetak_surat') }}"
                class="menu-item {{ request()->routeIs('cetak_surat') ? 'active' : '' }}">
                <i class="fas fa-print"></i>
                <span>Cetak Surat</span>
            </a>
        </li>


        {{-- <li>
            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                @csrf
                <a href="#" class="menu-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </a>
            </form>
        </li> --}}
    </ul>
</div>
