@extends('layouts.sidebarmenu')

@section('content')
<style>
    .activity-hero {
        border-radius: 30px;
        padding: 32px;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,0.92), transparent 24%),
            linear-gradient(135deg, #fff7ed 0%, #ffffff 45%, #fff1e6 100%);
        border: 1px solid rgba(249, 115, 22, 0.14);
        box-shadow: 0 24px 52px rgba(15, 23, 42, 0.06);
    }

    .activity-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: #ffffff;
        color: #c2410c;
        font-size: 0.82rem;
        font-weight: 800;
        border: 1px solid rgba(15, 23, 42, 0.06);
    }

    .summary-card {
        height: 100%;
        border-radius: 24px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #ffffff 0%, #fffaf5 100%);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.05);
    }

    .summary-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #ffedd5 0%, #ffffff 100%);
        color: #c2410c;
        font-size: 1.45rem;
        margin-bottom: 16px;
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #fed7aa;
        background: #fffaf5;
        border-radius: 14px;
        padding: 10px 14px;
        min-width: 300px;
    }

    .search-box input,
    .entries-select {
        border: 0;
        background: transparent;
        outline: none;
    }

    .entries-box {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #fed7aa;
        background: #fffaf5;
        border-radius: 14px;
        padding: 10px 14px;
        color: #7c2d12;
        font-weight: 700;
    }

    .row-no {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fff7ed;
        color: #c2410c;
        font-weight: 800;
    }

    .user-stack {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #ffedd5 0%, #fdba74 100%);
        color: #9a3412;
        font-weight: 800;
        flex-shrink: 0;
    }

    .meta-block {
        font-size: 0.85rem;
        color: #64748b;
    }

    .activity-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0.55rem 0.9rem;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 0.78rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .activity-detail {
        max-width: 360px;
        color: #334155;
        line-height: 1.45;
    }

    .soft-note {
        color: #64748b;
        font-size: 0.86rem;
    }

    .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #64748b;
    }

    [data-theme="dark"] .activity-hero {
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, 0.16), transparent 24%),
            linear-gradient(135deg, #111827 0%, #172033 55%, #1f2937 100%) !important;
        border-color: #334155 !important;
        box-shadow: 0 24px 52px rgba(0, 0, 0, 0.22);
    }

    [data-theme="dark"] .activity-badge,
    [data-theme="dark"] .summary-card,
    [data-theme="dark"] .entries-box {
        background: #172033 !important;
        color: #e5edf7 !important;
        border-color: #334155 !important;
    }

    [data-theme="dark"] .summary-icon,
    [data-theme="dark"] .row-no,
    [data-theme="dark"] .user-avatar {
        background: linear-gradient(135deg, #1f2a3d 0%, #23314a 100%) !important;
        color: #93c5fd !important;
    }

    [data-theme="dark"] .search-box {
        border-color: #334155 !important;
        background: #111827 !important;
    }

    [data-theme="dark"] .search-box input,
    [data-theme="dark"] .entries-select {
        color: #e5edf7 !important;
    }

    [data-theme="dark"] .search-box input::placeholder {
        color: #94a3b8 !important;
    }

    [data-theme="dark"] .activity-pill {
        background: #1f2a3d !important;
        color: #93c5fd !important;
        border: 1px solid #334155;
    }

    [data-theme="dark"] .activity-detail {
        color: #cbd5e1 !important;
    }

    [data-theme="dark"] .btn.btn-light {
        background: #172033 !important;
        color: #e5edf7 !important;
        border-color: #475569 !important;
    }

    [data-theme="dark"] .btn.btn-light:hover {
        background: #1f2a3d !important;
    }
</style>

<div class="container-fluid py-4">
    <div class="activity-hero mb-4">
        <div class="d-flex justify-content-between align-items-start gap-4 flex-wrap">
            <div>
                <div class="activity-badge mb-3">
                    <i class="bi bi-activity"></i>
                    Log Aktivitas Sistem
                </div>
                <h3 class="fw-bold text-dark mb-2">Audit aktivitas user di dalam sistem</h3>
                <p class="text-muted mb-0" style="max-width: 760px;">Semua aksi penting seperti login, tambah SOP, revisi SOP, tambah monitoring, evaluasi, dan perubahan user akan tercatat di sini lengkap dengan waktu, IP address, dan device.</p>
                <nav aria-label="breadcrumb" class="mt-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary fw-bold">Log Aktivitas</li>
                    </ol>
                </nav>
            </div>
            <div class="summary-icon" style="width:72px;height:72px;border-radius:24px;font-size:1.8rem;">
                <i class="bi bi-clipboard-data"></i>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon"><i class="bi bi-journal-text"></i></div>
                <div class="text-muted small fw-bold">TOTAL LOG</div>
                <div class="display-6 fw-bold text-dark">{{ $summary['total_log'] }}</div>
                <div class="text-muted small">Semua entri aktivitas yang tersimpan.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon"><i class="bi bi-calendar-check"></i></div>
                <div class="text-muted small fw-bold">AKTIVITAS HARI INI</div>
                <div class="display-6 fw-bold text-dark">{{ $summary['aktivitas_hari_ini'] }}</div>
                <div class="text-muted small">Jumlah aktivitas pada hari ini.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon"><i class="bi bi-people-fill"></i></div>
                <div class="text-muted small fw-bold">USER AKTIF HARI INI</div>
                <div class="display-6 fw-bold text-dark">{{ $summary['user_aktif_hari_ini'] }}</div>
                <div class="text-muted small">Akun unik yang melakukan aksi hari ini.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon"><i class="bi bi-box-arrow-in-right"></i></div>
                <div class="text-muted small fw-bold">LOGIN HARI INI</div>
                <div class="display-6 fw-bold text-dark">{{ $summary['sesi_login_hari_ini'] }}</div>
                <div class="text-muted small">Total sesi login berhasil hari ini.</div>
            </div>
        </div>
    </div>

    <div class="app-table-card">
        <div class="app-table-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">Daftar Aktivitas Terakhir</h5>
                <div class="soft-note">Format tabel mengikuti referensi: No, Waktu, User, Aktivitas, Detail, IP Address, Device.</div>
            </div>

            <form method="GET" action="{{ route('admin.activity.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
                <label class="entries-box mb-0">
                    <span>Tampilkan</span>
                    <select name="entries" class="entries-select" onchange="this.form.submit()">
                        @foreach([10, 25, 50, 100] as $option)
                            <option value="{{ $option }}" {{ $entries === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                    <span>entri</span>
                </label>

                <div class="search-box">
                    <i class="bi bi-search text-muted"></i>
                    <input type="text" name="search" value="{{ $keyword }}" placeholder="Cari user, aktivitas, detail, IP, atau device...">
                </div>

                <button type="submit" class="btn btn-primary fw-bold px-3" style="border-radius: 12px; background:#c2410c; border:none;">Cari</button>
                <a href="{{ route('admin.activity.index') }}" class="btn btn-light fw-bold px-3" style="border-radius: 12px;">Reset</a>
            </form>
        </div>

        <div class="app-table-wrap">
            <div class="table-responsive">
                <table class="table app-table-modern mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aktivitas</th>
                            <th>Detail</th>
                            <th>IP Address</th>
                            <th>Device</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activityLogs as $index => $log)
                            <tr>
                                <td>
                                    <span class="row-no">{{ $activityLogs->firstItem() + $index }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $log->activity_time?->format('d M Y') ?? '-' }}</div>
                                    <div class="meta-block">{{ $log->activity_time?->format('H:i:s') ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="user-stack">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($log->user->nama ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $log->user->nama ?? 'Sistem' }}</div>
                                            <div class="meta-block">
                                                {{ $log->user->username ?? 'system' }}
                                                @if($log->user?->role)
                                                    | {{ strtoupper($log->user->role) }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="activity-pill">
                                        <i class="bi bi-lightning-charge-fill"></i>
                                        {{ $log->activity }}
                                    </span>
                                </td>
                                <td>
                                    <div class="activity-detail">{{ $log->detail ?: '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $log->ip_address ?: '-' }}</div>
                                    <div class="meta-block">{{ $log->http_method ?: '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $log->device ?: '-' }}</div>
                                    <div class="meta-block">{{ \Illuminate\Support\Str::limit($log->route_name ?: '-', 24) }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-inboxes fs-2 d-block mb-2"></i>
                                        Belum ada data log aktivitas.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3">
                <div class="text-muted small fw-bold">
                    Menampilkan {{ $activityLogs->count() }} data dari total {{ $activityLogs->total() }} entri
                </div>
                <div>{{ $activityLogs->links('pagination::bootstrap-5') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
