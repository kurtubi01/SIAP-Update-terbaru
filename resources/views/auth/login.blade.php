<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SIAP Sistem Informasi Administrasi Prosedur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap');

        :root {
            --primary-color: #1976d2;
            --hover-color: #1565c0;
            --bg-gradient: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
        }

        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 400px;
            transition: all 0.3s ease;
        }

        /* Branding Section */
        .brand-mark {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .brand-mark img {
            width: 90px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }

        .brand-text {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .brand-name {
            font-size: 24px;
            letter-spacing: 1px;
            color: #333;
        }

        .brand-sub {
            font-size: 13px;
            color: #6c757d;
            line-height: 1.4;
        }

        /* Form Styling */
        .form-label {
            margin-bottom: 8px;
            font-size: 0.85rem;
            color: #495057;
        }

        .input-group {
            border-radius: 12px;
            overflow: hidden;
            transition: 0.2s;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            font-size: 0.95rem;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-color);
            background-color: #fff;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            padding-left: 15px;
            padding-right: 15px;
        }

        /* Button Styling */
        .btn-bps {
            background: var(--primary-color);
            color: white;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            border: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 10px;
        }

        .btn-bps:hover {
            background: var(--hover-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(25, 118, 210, 0.3);
        }

        .btn-bps:active {
            transform: translateY(0);
        }

        /* Responsiveness */
        @media (max-width: 576px) {
            body {
                padding: 15px;
            }
            .login-card {
                padding: 1.5rem !important;
                border-radius: 20px;
            }
            .brand-name {
                font-size: 20px;
            }
            .brand-sub {
                font-size: 11px;
            }
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /* Smooth Alert */
        .alert {
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="card login-card p-4 p-md-5">
    <div class="brand-mark">
        <img src="{{ asset('storage/images/logo-siap.jpeg') }}" alt="Logo SIAP">
    </div>

    <div class="brand-text">
        <span class="brand-name d-block fw-bold">SIAP</span>
        <span class="brand-sub d-block text-muted">
            Sistem Informasi Administrasi Prosedur
        </span>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 text-center shadow-sm">
            <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold">Username</label>
            <div class="input-group">
                <span class="input-group-text border-end-0"><i class="bi bi-person text-primary"></i></span>
                <input type="text" name="username" class="form-control border-start-0" placeholder="Masukkan username" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Password</label>
            <div class="input-group">
                <span class="input-group-text border-end-0"><i class="bi bi-lock text-primary"></i></span>
                <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0" placeholder="••••••••" required>
                <span class="input-group-text border-start-0 cursor-pointer" onclick="togglePassword()">
                    <i class="bi bi-eye-slash text-muted" id="toggleIcon"></i>
                </span>
            </div>
        </div>

        <button type="submit" class="btn btn-bps w-100 shadow-sm">
            <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
        </button>
    </form>

    <div class="text-center mt-5">
        <small class="text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            &copy; 2026 BPS PROVINSI BANTEN
        </small>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
            toggleIcon.classList.replace('text-muted', 'text-primary');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
            toggleIcon.classList.replace('text-primary', 'text-muted');
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if($errors->has('username'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Login Gagal',
        text: '{{ $errors->first('username') }}',
        confirmButtonColor: '#1976d2'
    });
</script>
@endif
</body>
</html>
