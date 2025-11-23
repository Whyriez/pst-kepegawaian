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
            <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- Profil Satuan Kerja --}}
        <li class="">
            <a href="{{ route('admin.profil_satker') }}" class="menu-item {{ request()->routeIs('admin.profil_satker') ? 'active' : '' }}">
                <i class="fas fa-building"></i>
                <span>Profil Satuan Kerja</span>
            </a>
        </li>

        {{-- Kenaikan Pangkat --}}
        <li class="has-submenu {{ request()->routeIs('admin.kp.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-item">
                <i class="fas fa-id-card"></i>
                <span>Kenaikan Pangkat</span>
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu" style="{{ request()->routeIs('admin.kp.*') ? 'display:block;' : 'display:none;' }}">
                <li>
                    <a href="{{ route('admin.kp.fungsional') }}" 
                       class="submenu-item {{ request()->routeIs('admin.kp.fungsional') ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i>
                        <span>Pengajuan Fungsional</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kp.penyesuaian_ijazah') }}" 
                       class="submenu-item {{ request()->routeIs('admin.kp.penyesuaian_ijazah') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Penyesuaian Ijazah</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kp.struktural') }}" 
                       class="submenu-item {{ request()->routeIs('admin.kp.struktural') ? 'active' : '' }}">
                        <i class="fas fa-sitemap"></i>
                        <span>Struktural</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kp.reguler') }}" 
                       class="submenu-item {{ request()->routeIs('admin.kp.reguler') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Reguler</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Pensiun --}}
        <li class="has-submenu {{ request()->routeIs('admin.pensiun.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-item">
                <i class="fas fa-user-clock"></i>
                <span>Pensiun</span>
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu" style="{{ request()->routeIs('admin.pensiun.*') ? 'display:block;' : 'display:none;' }}">
                <li>
                    <a href="{{ route('admin.pensiun.bup') }}" 
                       class="submenu-item {{ request()->routeIs('admin.pensiun.bup') ? 'active' : '' }}">
                        <i class="fas fa-hourglass-end"></i>
                        <span>Batas Usia Pensiun</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pensiun.janda_duda_yatim') }}" 
                       class="submenu-item {{ request()->routeIs('admin.pensiun.janda_duda_yatim') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Janda/Duda/Yatim</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pensiun.aps') }}" 
                       class="submenu-item {{ request()->routeIs('admin.pensiun.aps') ? 'active' : '' }}">
                        <i class="fas fa-hand-paper"></i>
                        <span>Atas Permintaan Sendiri</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pensiun.meninggal') }}" 
                       class="submenu-item {{ request()->routeIs('admin.pensiun.meninggal') ? 'active' : '' }}">
                        <i class="fas fa-procedures"></i>
                        <span>Meninggal</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pensiun.uzur') }}" 
                       class="submenu-item {{ request()->routeIs('admin.pensiun.uzur') ? 'active' : '' }}">
                        <i class="fas fa-wheelchair"></i>
                        <span>Uzur</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pensiun.hilang') }}" 
                       class="submenu-item {{ request()->routeIs('admin.pensiun.hilang') ? 'active' : '' }}">
                        <i class="fas fa-search-location"></i>
                        <span>Hilang</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pensiun.taw') }}" 
                       class="submenu-item {{ request()->routeIs('admin.pensiun.taw') ? 'active' : '' }}">
                        <i class="fas fa-user-slash"></i>
                        <span>Tanpa Ahli Waris</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Pindah Instansi --}}
        <li class="has-submenu {{ request()->routeIs('admin.pindah.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-item">
                <i class="fas fa-exchange-alt"></i>
                <span>Pindah Instansi</span>
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu" style="{{ request()->routeIs('admin.pindah.*') ? 'display:block;' : 'display:none;' }}">
                <li>
                    <a href="{{ route('admin.pindah.masuk') }}" 
                       class="submenu-item {{ request()->routeIs('admin.pindah.masuk') ? 'active' : '' }}">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Masuk Instansi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pindah.keluar') }}" 
                       class="submenu-item {{ request()->routeIs('admin.pindah.keluar') ? 'active' : '' }}">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar Instansi</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Jabatan Fungsional --}}
        <li class="has-submenu {{ request()->routeIs('admin.jf.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-item">
                <i class="fas fa-briefcase"></i>
                <span>Jabatan Fungsional</span>
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu" style="{{ request()->routeIs('admin.jf.*') ? 'display:block;' : 'display:none;' }}">
                <li>
                    <a href="{{ route('admin.jf.pengangkatan') }}" 
                       class="submenu-item {{ request()->routeIs('admin.jf.pengangkatan') ? 'active' : '' }}">
                        <i class="fas fa-user-plus"></i>
                        <span>Pengangkatan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.jf.pemberhentian') }}" 
                       class="submenu-item {{ request()->routeIs('admin.jf.pemberhentian') ? 'active' : '' }}">
                        <i class="fas fa-user-minus"></i>
                        <span>Pemberhentian</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.jf.naik_jenjang') }}" 
                       class="submenu-item {{ request()->routeIs('admin.jf.naik_jenjang') ? 'active' : '' }}">
                        <i class="fas fa-level-up-alt"></i>
                        <span>Naik Jenjang</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Satyalancana --}}
        <li class="{{ request()->routeIs('admin.satyalancana') ? 'active' : '' }}">
            <a href="{{ route('admin.satyalancana') }}" class="menu-item">
                <i class="fas fa-medal"></i>
                <span>Satyalancana</span>
            </a>
        </li>

        {{-- Pencantuman Gelar --}}
        <li class="has-submenu {{ request()->routeIs('admin.gelar.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-item">
                <i class="fas fa-graduation-cap"></i>
                <span>Pencantuman Gelar</span>
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu" style="{{ request()->routeIs('admin.gelar.*') ? 'display:block;' : 'display:none;' }}">
                <li>
                    <a href="{{ route('admin.gelar.akademik') }}" 
                       class="submenu-item {{ request()->routeIs('admin.gelar.akademik') ? 'active' : '' }}">
                        <i class="fas fa-university"></i>
                        <span>Akademik</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.gelar.profesi') }}" 
                       class="submenu-item {{ request()->routeIs('admin.gelar.profesi') ? 'active' : '' }}">
                        <i class="fas fa-user-md"></i>
                        <span>Profesi</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Penugasan --}}
        <li class="{{ request()->routeIs('admin.penugasan') ? 'active' : '' }}">
            <a href="{{ route('admin.penugasan') }}" class="menu-item">
                <i class="fas fa-tasks"></i>
                <span>Penugasan</span>
            </a>
        </li>

        {{-- Perbaikan Data ASN --}}
        <li class="{{ request()->routeIs('admin.perbaikan_data') ? 'active' : '' }}">
            <a href="{{ route('admin.perbaikan_data') }}" class="menu-item">
                <i class="fas fa-database"></i>
                <span>Perbaikan Data ASN</span>
            </a>
        </li>

        {{-- Tugas Belajar --}}
        <li class="{{ request()->routeIs('admin.tugas_belajar') ? 'active' : '' }}">
            <a href="{{ route('admin.tugas_belajar') }}" class="menu-item">
                <i class="fas fa-book-reader"></i>
                <span>Tugas Belajar</span>
            </a>
        </li>

        {{-- Manajemen Akun (KHUSUS ADMIN) --}}
        <li class="{{ request()->routeIs('admin.manajemen_akun') ? 'active' : '' }}">
            <a href="{{ route('admin.manajemen_akun') }}" class="menu-item">
                <i class="fas fa-users-cog"></i>
                <span>Manajemen Akun</span>
            </a>
        </li>

        {{-- Cetak Surat --}}
        <li class="{{ request()->routeIs('admin.cetak_surat') ? 'active' : '' }}">
            <a href="{{ route('admin.cetak_surat') }}" class="menu-item">
                <i class="fas fa-print"></i>
                <span>Cetak Surat</span>
            </a>
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