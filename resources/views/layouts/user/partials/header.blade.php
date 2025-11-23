<!-- includes/header.php -->
<nav class="topbar navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <!-- Logo Kantor -->
        <div class="navbar-brand d-flex align-items-center">
            <img src="assets/logo_kantor.png" alt="Logo Kantor" class="logo-kantor me-2" style="max-height: 40px;">
            <span class="d-none d-md-inline fw-bold">Kementrian Agama Kota Gorontalo</span>
        </div>

        <!-- User Info -->
        <div class="user-info d-flex align-items-center ms-auto">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                    id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=User+Pegawai&background=1a73e8&color=fff" alt="User"
                        class="rounded-circle me-2" width="40" height="40">
                    <span class="d-none d-md-inline">User Pegawai</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('profile') ? 'active' : '' }}"
                            href="{{ route('profile') }}">
                            <i class="fas fa-user me-2"></i>Profil
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item"><i
                                    class="fas fa-sign-out-alt me-2"></i>Keluar</button>
                        </form>

                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
