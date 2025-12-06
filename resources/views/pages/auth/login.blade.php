<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SI Kepegawaian Kemenag</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            overflow: hidden;
        }

        .login-container {
            height: 100vh;
        }

        /* Bagian Kiri (Branding) */
        .login-left {
            background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -10%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .login-left::after {
            content: '';
            position: absolute;
            bottom: -10%;
            left: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .brand-logo {
            width: 80px;
            margin-bottom: 1.5rem;
            background: white;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Bagian Kanan (Form) */
        .login-right {
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 400px;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .form-control:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 0.25rem rgba(26, 115, 232, 0.25);
        }

        .btn-login {
            background-color: #1a73e8;
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background-color: #1557b0;
            transform: translateY(-1px);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-right: none;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .form-control {
            border-left: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-left {
                display: none;
                /* Sembunyikan bagian kiri di mobile */
            }

            body {
                background: white;
                overflow-y: auto;
            }

            .login-container {
                height: auto;
                min-height: 100vh;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid login-container">
        <div class="row h-100">

            <div class="col-md-6 login-left">
                <div class="z-1 position-relative">
                    <img src="{{ asset('assets/logo_kantor.png') }}" alt="Logo" class="brand-logo">
                    <h1 class="fw-bold display-6 mb-3">Sistem Informasi Kepegawaian</h1>
                    <h4 class="fw-light mb-4">Kementrian Agama Kota Gorontalo</h4>
                    <p class="lead opacity-75" style="font-size: 1rem; line-height: 1.6;">
                        Selamat datang di portal layanan kepegawaian terpadu.
                        Kelola kenaikan pangkat, pensiun, mutasi, dan administrasi lainnya dengan lebih mudah dan
                        transparan.
                    </p>
                    <div class="mt-5">
                        <small class="d-block opacity-50">&copy; 2025 Kemenag Kota Gorontalo.</small>
                        <small class="d-block opacity-50">Dikembangkan oleh Mahasiswa UNG.</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 login-right">
                <div class="login-form-wrapper">
                    <div class="text-center mb-4 d-md-none">
                        <img src="{{ asset('assets/logo_kantor.png') }}" alt="Logo" width="60" class="mb-3">
                        <h4 class="fw-bold">SI Kepegawaian</h4>
                    </div>

                    <div class="mb-4">
                        <h3 class="fw-bold text-dark">Silakan Login</h3>
                        <p class="text-muted">Masukkan nip dan password Anda untuk melanjutkan.</p>
                    </div>

                    {{-- Alert Error --}}
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <ul class="mb-0 small ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="nip" class="form-label fw-bold small text-muted">NOMOR INDUK PEGAWAI
                                (NIP)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-card text-muted"></i></span>
                                <input type="text" class="form-control @error('nip') is-invalid @enderror"
                                       id="nip" name="nip" placeholder="Masukkan 18 digit NIP"
                                       value="{{ old('nip') }}" maxlength="18" minlength="18"
                                       inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                       required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="password" class="form-label fw-bold small text-muted">PASSWORD</label>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Masukkan password" required>
                                <button class="btn btn-outline-secondary border-start-0 border-top-0 border-bottom-0"
                                        type="button" id="togglePassword" style="border-color: #dee2e6;">
                                    <i class="fas fa-eye-slash text-muted small"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-login btn-lg text-white">
                                MASUK SEKARANG <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 text-center">
                        @php
                            // [BARU] Logika formatting nomor WhatsApp
                            $waLink = '#'; // Default jika tidak ada nomor

                            if(isset($nomorAdmin) && $nomorAdmin) {
                                // Hapus karakter selain angka
                                $cleanPhone = preg_replace('/[^0-9]/', '', $nomorAdmin);

                                // Ubah awalan '0' menjadi '62'
                                if(substr($cleanPhone, 0, 1) == '0') {
                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                }

                                // Buat link WA
                                $waLink = "https://wa.me/" . $cleanPhone . "?text=Halo%20Admin,%20saya%20mengalami%20kendala%20saat%20login%20di%20SI%20Kepegawaian.";
                            }
                        @endphp

                        <p class="small text-muted">Ada masalah dengan akun anda?
                            <a href="{{ $waLink }}" target="_blank" class="text-decoration-none fw-bold">
                                <i class="fab fa-whatsapp me-1"></i>Hubungi Admin
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function(e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // toggle the icon
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>
