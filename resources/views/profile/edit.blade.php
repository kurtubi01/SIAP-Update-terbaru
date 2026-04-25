@extends('layouts.sidebarmenu')

@section('content')
<style>
    .profile-shell {
        max-width: 760px;
    }

    .profile-card {
        border: 1px solid #dce6f2;
        border-radius: 26px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .profile-card-body {
        padding: 2rem;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 420px;
    }

    .profile-photo {
        width: 210px;
        height: 210px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1d4ed8;
        font-size: 4.8rem;
        font-weight: 800;
        border: 10px solid #ffffff;
        box-shadow: 0 24px 48px rgba(59, 130, 246, 0.18);
    }
</style>

<div class="container-fluid app-page-shell py-4">
    <div class="app-page-header profile-shell">
        <div>
            <h1 class="app-page-title">Profil</h1>
            <p class="app-page-subtitle">Halaman profil sementara dibuat sederhana dan konsisten dengan tampilan utama aplikasi.</p>
        </div>
    </div>

    <div class="profile-shell">
        <div class="profile-card">
            <div class="profile-card-body">
                <div class="profile-photo">
                    {{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
