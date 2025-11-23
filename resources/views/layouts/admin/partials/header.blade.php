<nav class="topbar navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <!-- Search Bar -->
        <form class="d-flex search-bar">
            <div class="input-group">
                <span class="input-group-text bg-light border-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-0 bg-light" placeholder="Cari pengajuan...">
            </div>
        </form>

        <!-- User Info -->
        <div class="user-info d-flex align-items-center">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                    id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=Admin+PTSP&background=1a73e8&color=fff" alt="User"
                        class="rounded-circle me-2" width="40" height="40">
                    <span class="d-none d-md-inline">Admin PTSP</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil
                            Admin</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Pengaturan</a>
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
