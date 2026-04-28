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

        .link-action {
            color: #1565c0;
            font-weight: 700;
            font-size: 0.88rem;
            text-decoration: none;
        }

        .link-action:hover {
            color: #0d47a1;
        }
    </style>
</head>
<body>
@php($recoveryErrors = $errors->getBag('accountRecovery'))
@php($shouldOpenRecoveryModal = $recoveryErrors->any() || session('account_recovery_status'))

<div class="card login-card p-4 p-md-5">
    <div class="brand-mark">
        <img src="{{ asset('storage/images/log_siapp.png') }}" alt="Logo SIAPP">
    </div>

    <div class="brand-text">
        <span class="brand-name d-block fw-bold">SIAPP</span>
        <span class="brand-sub d-block text-muted">
            Sistem Informasi Administrasi Prosedur Pemerintahan
        </span>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 text-center shadow-sm">
            <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    @if(session('status'))
        <div class="alert alert-success border-0 text-center shadow-sm">
            <i class="bi bi-check-circle me-2"></i> {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold">Username</label>
            <div class="input-group">
                <span class="input-group-text border-end-0"><i class="bi bi-person text-primary"></i></span>
                <input type="text" name="username" value="{{ old('username') }}" class="form-control border-start-0 @error('username') is-invalid @enderror" placeholder="Masukkan username" required autofocus>
            </div>
            @error('username')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Password</label>
            <div class="input-group">
                <span class="input-group-text border-end-0"><i class="bi bi-lock text-primary"></i></span>
                <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" placeholder="Masukkan password" required>
                <span class="input-group-text border-start-0 cursor-pointer" onclick="togglePassword()">
                    <i class="bi bi-eye-slash text-muted" id="toggleIcon"></i>
                </span>
            </div>
            @error('password')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <a href="#" class="link-action" data-bs-toggle="modal" data-bs-target="#accountRecoveryModal">
                Lupa username atau password?
            </a>
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

<div class="modal fade" id="accountRecoveryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold mb-0">Permintaan Bantuan Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('account.recovery.store') }}">
                @csrf
                <div class="modal-body pt-3">
                    <div class="alert alert-info border-0">
                        Pilih apakah Anda lupa username atau password. Admin akan menerima notifikasi untuk membantu reset akun.
                    </div>
                    @if(session('account_recovery_status'))
                        <div class="alert alert-success border-0">
                            <i class="bi bi-check-circle me-2"></i>{{ session('account_recovery_status') }}
                        </div>
                    @endif
                    @if($recoveryErrors->any())
                        <div class="alert alert-danger border-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ $recoveryErrors->first() }}
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="form-control {{ $recoveryErrors->has('nama') ? 'is-invalid' : '' }}" required>
                        @if($recoveryErrors->has('nama'))
                            <div class="invalid-feedback">{{ $recoveryErrors->first('nama') }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIP</label>
                        <input type="text" name="nip" value="{{ old('nip') }}" class="form-control {{ $recoveryErrors->has('nip') ? 'is-invalid' : '' }}" required>
                        @if($recoveryErrors->has('nip'))
                            <div class="invalid-feedback">{{ $recoveryErrors->first('nip') }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Permintaan</label>
                        <select name="jenis_permohonan" class="form-select {{ $recoveryErrors->has('jenis_permohonan') ? 'is-invalid' : '' }}" required>
                            <option value="username" {{ old('jenis_permohonan') === 'username' ? 'selected' : '' }}>Lupa Username</option>
                            <option value="password" {{ old('jenis_permohonan', 'password') === 'password' ? 'selected' : '' }}>Lupa Password</option>
                        </select>
                        @if($recoveryErrors->has('jenis_permohonan'))
                            <div class="invalid-feedback">{{ $recoveryErrors->first('jenis_permohonan') }}</div>
                        @endif
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Catatan Tambahan</label>
                        <textarea name="catatan" rows="3" class="form-control {{ $recoveryErrors->has('catatan') ? 'is-invalid' : '' }}" placeholder="Opsional">{{ old('catatan') }}</textarea>
                        @if($recoveryErrors->has('catatan'))
                            <div class="invalid-feedback">{{ $recoveryErrors->first('catatan') }}</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-bps px-4">Kirim Permintaan</button>
                </div>
            </form>
        </div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if($errors->has('username') || $errors->has('password'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Login Gagal',
        text: '{{ $errors->first('username') ?: $errors->first('password') }}',
        confirmButtonColor: '#1976d2'
    });
</script>
@endif
@if($shouldOpenRecoveryModal)
<script>
    const recoveryModal = new bootstrap.Modal(document.getElementById('accountRecoveryModal'));
    recoveryModal.show();
</script>
@endif
</body>
</html>
