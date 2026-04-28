<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($browserTitle = match (true) {
        request()->routeIs('*.dashboard') => 'Dashboard',
        request()->routeIs('*.sop.*') => 'Data SOP',
        request()->routeIs('*.monitoring.*') => 'Monitoring',
        request()->routeIs('*.evaluasi.*') => 'Evaluasi',
        request()->routeIs('admin.user.*') => 'Manajemen User',
        request()->routeIs('admin.timkerja.*') => 'Manajemen Tim Kerja',
        request()->routeIs('admin.subjek.*') => 'Manajemen Subjek',
        request()->routeIs('admin.activity.*') => 'Log Aktivitas',
        default => 'SIAP'
    })
    <title>{{ $browserTitle }} | SIAP BPS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Nunito:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script>
        (function () {
            const savedTheme = localStorage.getItem('siap-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <style>
        :root {
            --sidebar-bg: #4b2e2a;
            --sidebar-surface: rgba(255, 255, 255, 0.08);
            --sidebar-border: rgba(255, 179, 71, 0.22);
            --sidebar-text: rgba(255, 244, 230, 0.86);
            --sidebar-text-strong: #fff7ed;
            --sidebar-accent: #f97316;
            --sidebar-accent-soft: linear-gradient(135deg, rgba(249, 115, 22, 0.22), rgba(251, 191, 36, 0.22));
            --sidebar-width: 292px;
            --top-navbar-height: 78px;
            --page-bg: #f8fbff;
            --panel-bg: #ffffff;
            --panel-border: #e2e8f0;
            --text-main: #0f172a;
            --text-soft: #64748b;
            --shadow-soft: 0 22px 48px rgba(15, 23, 42, 0.08);
            --shadow-sidebar: 18px 0 42px rgba(37, 99, 235, 0.12);
            --radius-md: 16px;
            --radius-lg: 20px;
        }

        body {
            margin: 0;
            overflow-x: hidden;
            font-family: 'Inter', 'Nunito', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(191, 219, 254, 0.55), transparent 28%),
                linear-gradient(180deg, #ffffff 0%, #f5f9ff 100%);
            color: var(--text-main);
        }

        #wrapper {
            display: flex;
            min-height: 100vh;
        }

        #sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            background:
                radial-gradient(circle at top, rgba(255, 190, 92, 0.22), transparent 24%),
                linear-gradient(180deg, #5f352f 0%, #432521 52%, var(--sidebar-bg) 100%);
            color: var(--sidebar-text);
            box-shadow: 18px 0 42px rgba(67, 37, 33, 0.28);
            border-right: 1px solid rgba(255, 191, 105, 0.16);
            transition: transform 0.3s ease, width 0.3s ease;
        }

        #sidebar.minimized {
            transform: translateX(calc(var(--sidebar-width) * -1));
        }

        .sidebar-header {
            padding: 26px 22px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-mark {
            width: 60px;
            height: 60px;
            flex-shrink: 0;
            border-radius: 18px;
            background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85), 0 10px 24px rgba(249, 115, 22, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .brand-name {
            display: block;
            color: #fffaf5;
            font-size: 1.42rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: 0.04em;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.12);
        }

        .brand-sub {
            display: block;
            margin-top: 2px;
            color: rgba(255, 237, 213, 0.84);
            font-size: 0.82rem;
            font-weight: 600;
            line-height: 1.45;
        }

        #btn-toggle-custom {
            position: fixed;
            top: 18px;
            left: calc(var(--sidebar-width) - 16px);
            z-index: 1100;
            width: 44px;
            height: 44px;
            border: 1px solid rgba(255, 214, 170, 0.75);
            border-radius: 14px;
            background: linear-gradient(180deg, #fff7ed 0%, #ffedd5 100%);
            color: #9a3412;
            box-shadow: 0 14px 28px rgba(124, 45, 18, 0.16);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: left 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            cursor: pointer;
        }

        #btn-toggle-custom:hover {
            transform: translateY(-1px);
            background: linear-gradient(180deg, #ffffff 0%, #ffedd5 100%);
            box-shadow: 0 16px 30px rgba(124, 45, 18, 0.2);
        }

        #btn-toggle-custom i {
            font-size: 1.2rem;
            line-height: 1;
        }

        #sidebar.minimized + #content #btn-toggle-custom {
            left: 18px;
        }

        .role-pill {
            margin: 18px 18px 12px;
            padding: 16px 18px;
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(255, 247, 237, 0.18), rgba(255, 255, 255, 0.08));
            border: 1px solid var(--sidebar-border);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
        }

        .role-pill .small {
            color: rgba(255, 237, 213, 0.86);
        }

        .nav-menu {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0 18px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .nav-menu::-webkit-scrollbar {
            width: 0;
            height: 0;
            display: none;
        }

        .menu-label {
            margin: 20px 22px 10px;
            color: rgba(255, 214, 170, 0.62);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.14em;
        }

        .nav-menu li a {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 6px 14px;
            padding: 13px 16px 13px 18px;
            border-radius: 16px;
            border-left: 4px solid transparent;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: background 0.25s ease, color 0.25s ease, border-color 0.25s ease, transform 0.25s ease;
        }

        .nav-menu li a i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
        }

        .nav-menu li a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--sidebar-text-strong);
            border-left-color: rgba(251, 146, 60, 0.92);
            transform: translateX(2px);
        }

        .nav-menu li a.is-placeholder {
            opacity: 0.92;
        }

        .menu-soon {
            margin-left: auto;
            padding: 4px 8px;
            border-radius: 999px;
            background: rgba(251, 191, 36, 0.18);
            color: #fdba74;
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .nav-menu li.active > a {
            background: var(--sidebar-accent-soft);
            color: #fff7ed;
            border-left-color: var(--sidebar-accent);
            box-shadow: inset 0 0 0 1px rgba(249, 115, 22, 0.18);
        }

        .sidebar-footer {
            padding: 18px;
            background: rgba(255, 255, 255, 0.06);
        }

        .sidebar-footer .btn {
            border-radius: 14px;
            padding: 12px 14px;
            font-weight: 700;
            background: rgba(255, 247, 237, 0.92);
        }

        #content {
            flex: 1;
            min-width: 0;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        #sidebar.minimized + #content {
            margin-left: 0;
        }

        .top-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            min-height: var(--top-navbar-height);
            display: flex;
            align-items: center;
            padding: 16px 30px 16px 88px;
            background: rgba(248, 250, 252, 0.86);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.88);
        }

        .top-title {
            font-size: 0.98rem;
            font-weight: 700;
            color: #334155;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 8px 10px 8px 14px;
            border-radius: 18px;
            border: 1px solid rgba(219, 231, 245, 0.95);
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .user-info:hover {
            transform: translateY(-1px);
            border-color: #c9daf0;
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
        }

        .user-info.dropdown-toggle::after {
            display: none;
        }

        .avatar-box {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1d4ed8;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 22px rgba(59, 130, 246, 0.18);
        }

        .profile-trigger-icon {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            background: #f8fbff;
            border: 1px solid #dbe5f1;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .dropdown-menu {
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: var(--shadow-soft);
            padding: 8px;
        }

        .dropdown-item {
            border-radius: 12px;
            font-weight: 600;
            color: #334155;
        }

        .dropdown-item:hover {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .theme-toggle-btn {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            border: 1px solid rgba(219, 231, 245, 0.95);
            background: rgba(255, 255, 255, 0.88);
            color: #334155;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;
        }

        .theme-toggle-btn:hover {
            transform: translateY(-1px);
            border-color: #c9daf0;
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
        }

        main {
            padding: 30px;
        }

        footer {
            margin-top: auto;
            padding: 18px 30px 24px;
            color: #94a3b8;
            font-size: 0.82rem;
        }

        .app-table-card {
            border-radius: 24px;
            border: 1px solid #dce6f2;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .app-table-toolbar {
            padding: 22px 24px 18px;
            border-bottom: 1px solid #e7eef7;
            background: linear-gradient(180deg, #ffffff 0%, #f4f8ff 100%);
        }

        .app-table-wrap {
            padding: 14px 18px 18px;
        }

        .soft-note {
            padding: 0.9rem 1rem;
            border-radius: 16px;
            background: linear-gradient(135deg, #eef6ff 0%, #f8fbff 100%);
            border: 1px solid #dbeafe;
            color: #475569;
            font-size: 0.89rem;
            line-height: 1.7;
        }

        .app-table-modern {
            border-collapse: separate;
            border-spacing: 0 12px;
            min-width: 980px;
        }

        .app-table-modern thead th {
            background: linear-gradient(180deg, #eaf3ff 0%, #f6faff 100%);
            color: #294264;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            border-top: 1px solid #d7e3f0;
            border-bottom: 1px solid #d7e3f0;
            padding: 1rem;
            white-space: nowrap;
        }

        .app-table-modern thead th:first-child {
            border-left: 1px solid #d7e3f0;
            border-radius: 16px 0 0 16px;
        }

        .app-table-modern thead th:last-child {
            border-right: 1px solid #d7e3f0;
            border-radius: 0 16px 16px 0;
        }

        .app-table-modern tbody tr {
            background: linear-gradient(180deg, #ffffff 0%, #fcfdff 100%);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.055);
        }

        .app-table-modern tbody td {
            border-top: 1px solid #e6edf5;
            border-bottom: 1px solid #e6edf5;
            border-left: 0;
            border-right: 0;
            padding: 1rem;
            vertical-align: middle;
            color: #0f172a;
        }

        .app-table-modern tbody td:first-child {
            border-left: 1px solid #e6edf5;
            border-radius: 16px 0 0 16px;
        }

        .app-table-modern tbody td:last-child {
            border-right: 1px solid #e6edf5;
            border-radius: 0 16px 16px 0;
        }

        .app-page-shell {
            font-family: 'Inter', 'Nunito', sans-serif;
        }

        .app-page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .app-page-title {
            color: #0f172a;
            font-size: 1.85rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .app-page-subtitle {
            color: #64748b;
            margin-bottom: 0;
            font-size: 0.96rem;
            max-width: 760px;
            line-height: 1.65;
        }

        .app-section-card {
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 24px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        }

        [data-theme="dark"] {
            color-scheme: dark;
            --page-bg: #0f172a;
            --panel-bg: #111827;
            --panel-border: #263244;
            --text-main: #e5edf7;
            --text-soft: #9ca3af;
            --shadow-soft: 0 22px 48px rgba(0, 0, 0, 0.28);
        }

        [data-theme="dark"] body {
            background: linear-gradient(180deg, #0f172a 0%, #111827 100%);
            color: var(--text-main);
        }

        [data-theme="dark"] .top-navbar {
            background: rgba(15, 23, 42, 0.9);
            border-bottom-color: #263244;
        }

        [data-theme="dark"] .top-title,
        [data-theme="dark"] .app-page-title,
        [data-theme="dark"] .cell-title,
        [data-theme="dark"] .detail-value,
        [data-theme="dark"] .fw-bold,
        [data-theme="dark"] .text-dark {
            color: #e5edf7 !important;
        }

        [data-theme="dark"] .text-muted,
        [data-theme="dark"] .cell-subtitle,
        [data-theme="dark"] .app-page-subtitle,
        [data-theme="dark"] .soft-note,
        [data-theme="dark"] .detail-label,
        [data-theme="dark"] footer {
            color: #9ca3af !important;
        }

        [data-theme="dark"] .user-info,
        [data-theme="dark"] .theme-toggle-btn,
        [data-theme="dark"] .dropdown-menu,
        [data-theme="dark"] .app-table-card,
        [data-theme="dark"] .app-section-card,
        [data-theme="dark"] .monev-stat-card,
        [data-theme="dark"] .monev-guidance-card,
        [data-theme="dark"] .main-card,
        [data-theme="dark"] .monitoring-card,
        [data-theme="dark"] .evaluasi-card,
        [data-theme="dark"] .detail-card,
        [data-theme="dark"] .card-premium,
        [data-theme="dark"] .modal-content,
        [data-theme="dark"] .history-panel,
        [data-theme="dark"] .sop-action-panel,
        [data-theme="dark"] .sop-form-shell,
        [data-theme="dark"] .form-block,
        [data-theme="dark"] .detail-item,
        [data-theme="dark"] .detail-wide {
            background: #111827 !important;
            border-color: #263244 !important;
            color: #e5edf7;
            box-shadow: var(--shadow-soft);
        }

        [data-theme="dark"] .app-table-toolbar,
        [data-theme="dark"] .app-table-wrap,
        [data-theme="dark"] .table-shell,
        [data-theme="dark"] .detail-head,
        [data-theme="dark"] .evaluasi-sheet-header,
        [data-theme="dark"] .locked-sop-card,
        [data-theme="dark"] .sop-action-summary,
        [data-theme="dark"] .soft-note {
            background: #172033 !important;
            border-color: #2d3a4f !important;
        }

        [data-theme="dark"] .app-table-modern thead th,
        [data-theme="dark"] .custom-table thead th,
        [data-theme="dark"] .evaluasi-sheet-table thead th,
        [data-theme="dark"] .monitoring-table thead th {
            background: #1f2a3d !important;
            color: #dbeafe !important;
            border-color: #334155 !important;
        }

        [data-theme="dark"] .app-table-modern tbody tr,
        [data-theme="dark"] .custom-table tbody tr,
        [data-theme="dark"] .custom-table tbody tr:nth-child(even),
        [data-theme="dark"] .monitoring-table td,
        [data-theme="dark"] .evaluasi-sheet-table td,
        [data-theme="dark"] .upload-preview-card,
        [data-theme="dark"] .search-select-menu,
        [data-theme="dark"] .filter-dropdown-menu {
            background: #111827 !important;
            color: #e5edf7 !important;
            border-color: #334155 !important;
        }

        [data-theme="dark"] .app-table-modern tbody td,
        [data-theme="dark"] .custom-table tbody td,
        [data-theme="dark"] .monitoring-table td,
        [data-theme="dark"] .evaluasi-sheet-table td,
        [data-theme="dark"] .table,
        [data-theme="dark"] .table > :not(caption) > * > * {
            color: #e5edf7 !important;
            background-color: #111827 !important;
            border-color: #334155 !important;
        }

        [data-theme="dark"] .monev-stat-value,
        [data-theme="dark"] .guidance-list,
        [data-theme="dark"] .table td,
        [data-theme="dark"] .table th {
            color: #f8fafc !important;
        }

        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select,
        [data-theme="dark"] .search-box,
        [data-theme="dark"] .filter-dropdown-toggle,
        [data-theme="dark"] .filter-dropdown-search {
            background: #0f172a !important;
            color: #e5edf7 !important;
            border-color: #334155 !important;
        }

        [data-theme="dark"] .form-control::placeholder,
        [data-theme="dark"] .search-box input::placeholder {
            color: #94a3b8 !important;
        }

        [data-theme="dark"] .dropdown-item {
            color: #dbeafe;
        }

        [data-theme="dark"] .dropdown-item:hover {
            background: #1f2a3d;
            color: #93c5fd;
        }

        @media (max-width: 992px) {
            #sidebar {
                transform: translateX(-100%);
                width: min(84vw, var(--sidebar-width));
                min-width: min(84vw, var(--sidebar-width));
            }

            #sidebar.show-mobile {
                transform: translateX(0);
            }

            #content {
                margin-left: 0 !important;
            }

            #btn-toggle-custom {
                left: 18px !important;
                top: 14px;
            }

            .top-navbar {
                padding-inline: 76px 18px;
                min-height: 72px;
            }

            main {
                padding: 22px 16px;
            }

            .app-page-title {
                font-size: 1.55rem;
            }

            .app-page-subtitle {
                font-size: 0.92rem;
            }

            .app-table-toolbar,
            .app-table-wrap {
                padding-inline: 14px;
            }

            .brand-name {
                font-size: 1.28rem;
            }

            .brand-sub {
                font-size: 0.76rem;
            }
        }

        @media (max-width: 576px) {
            .sidebar-header {
                padding: 22px 16px 14px;
            }

            .brand-mark {
                width: 52px;
                height: 52px;
                border-radius: 16px;
            }

            .role-pill {
                margin: 14px;
                padding: 14px 16px;
            }

            .nav-menu li a {
                margin-inline: 10px;
                padding: 12px 14px 12px 16px;
            }

            .top-navbar {
                padding-left: 68px;
            }

            footer {
                padding: 16px 16px 24px;
            }
        }
    </style>
</head>
<body>
    @php($role = strtolower(Auth::user()->role ?? 'admin'))
    @php($dashboardPatterns = [$role . '.dashboard'])
    @php($dashboardPatterns[] = $role . '.sop.aksescepat')
    @php($isDashboardActive = request()->routeIs(...$dashboardPatterns))
    @php($isSopActive = request()->routeIs($role . '.sop.*') && !request()->routeIs($role . '.sop.aksescepat'))
    @php($roleTheme = [
        'admin' => ['accent' => '#3b82f6', 'soft' => 'rgba(59, 130, 246, 0.18)', 'label' => 'Kontrol Penuh Sistem'],
        'operator' => ['accent' => '#14b8a6', 'soft' => 'rgba(20, 184, 166, 0.18)', 'label' => 'Pusat Operasional Tim'],
        'viewer' => ['accent' => '#f59e0b', 'soft' => 'rgba(245, 158, 11, 0.18)', 'label' => 'Mode Lihat Dokumen'],
    ][$role] ?? ['accent' => '#3b82f6', 'soft' => 'rgba(59, 130, 246, 0.18)', 'label' => 'Kontrol Sistem'])

    <div id="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <div class="brand-mark" aria-hidden="true">
                    <img src="{{ asset('storage/images/log_siapp.png') }}" alt="Logo SIAPP">
                </div>
                <div class="brand-text">
                    <span class="brand-name">SIAPP</span>
                    <span class="brand-sub">Sistem Informasi Administrasi Prosedur Pemerintahan</span>
                </div>
            </div>

            <div class="role-pill" style="background-color: {{ $roleTheme['soft'] }}; border-color: rgba(148, 163, 184, 0.14);">
                <div class="fw-bold text-uppercase small" style="color: {{ $roleTheme['accent'] }};">{{ strtoupper($role) }}</div>
                <div class="small">{{ $roleTheme['label'] }}</div>
            </div>

            <ul class="list-unstyled nav-menu">
                <li class="{{ $isDashboardActive ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if($role !== 'viewer')
                    <div class="menu-label">Repositori</div>
                    <li class="{{ $isSopActive ? 'active' : '' }}">
                        <a href="{{ route($role . '.sop.index') }}">
                            <i class="bi bi-file-earmark-richtext"></i>
                            <span>Data SOP</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs($role . '.monitoring.*') ? 'active' : '' }}">
                        <a href="{{ route($role . '.monitoring.index') }}">
                            <i class="bi bi-clipboard2-pulse"></i>
                            <span>Monitoring</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs($role . '.evaluasi.*') ? 'active' : '' }}">
                        <a href="{{ route($role . '.evaluasi.index') }}">
                            <i class="bi bi-ui-checks-grid"></i>
                            <span>Evaluasi</span>
                        </a>
                    </li>
                @else
                    <div class="menu-label">Laporan</div>
                    <li class="{{ request()->routeIs('viewer.monitoring.*') ? 'active' : '' }}">
                        <a href="{{ route('viewer.monitoring.index') }}">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan Monev</span>
                        </a>
                @endif

                @if($role === 'admin')
                    <div class="menu-label">Sistem</div>
                    <li class="{{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.user.index') }}">
                            <i class="bi bi-people-fill"></i>
                            <span>Manajemen User</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.timkerja.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.timkerja.index') }}">
                            <i class="bi bi-diagram-3-fill"></i>
                            <span>Manajemen Tim Kerja</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.subjek.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.subjek.index') }}">
                            <i class="bi bi-collection-fill"></i>
                            <span>Manajemen Subjek</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.activity.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.activity.index') }}" title="Menu Log Aktivitas">
                            <i class="bi bi-activity"></i>
                            <span>Log Aktivitas</span>
                        </a>
                    </li>
                @endif
            </ul>

            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="button" class="btn w-100 text-danger border-0 js-logout-trigger">
                        <i class="bi bi-box-arrow-left me-2"></i>Keluar
                    </button>
                </form>
            </div>
        </nav>

        <div id="content">
            <button id="btn-toggle-custom" type="button" aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>

            <header class="top-navbar">
                <div class="top-title d-none d-md-block">
                    Badan Pusat Statistik Provinsi Banten
                </div>

                <div class="ms-auto d-flex align-items-center gap-2">
                    <button id="themeToggle" class="theme-toggle-btn" type="button" title="Ubah tema" aria-label="Ubah tema">
                        <i id="themeToggleIcon" class="bi bi-moon-stars"></i>
                    </button>

                    <div class="dropdown">
                        <div class="user-info dropdown-toggle" style="cursor:pointer" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="text-end d-none d-sm-block">
                                <div class="fw-bold small text-dark">{{ Auth::user()->nama }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ strtoupper(Auth::user()->role) }} | {{ $roleTheme['label'] }}</div>
                            </div>
                            <div class="avatar-box">
                                {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                            </div>
                            <span class="profile-trigger-icon">
                                <i class="bi bi-chevron-down"></i>
                            </span>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end mt-3">
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="button" class="dropdown-item text-danger fw-bold js-logout-trigger">
                                        <i class="bi bi-box-arrow-left me-2"></i>Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <main>
                @yield('content')
            </main>

            <footer>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span>&copy; 2026 Badan Pusat Statistik Provinsi Banten</span>
                    <span class="opacity-75">SIAP Enterprise Monitoring</span>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            const sidebar = $('#sidebar');
            const btnToggle = $('#btn-toggle-custom');
            const themeToggle = $('#themeToggle');
            const themeIcon = $('#themeToggleIcon');

            function setTheme(theme) {
                const normalizedTheme = theme === 'dark' ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', normalizedTheme);
                localStorage.setItem('siap-theme', normalizedTheme);
                themeIcon
                    .toggleClass('bi-sun', normalizedTheme === 'dark')
                    .toggleClass('bi-moon-stars', normalizedTheme !== 'dark');
                themeToggle.attr('title', normalizedTheme === 'dark' ? 'Pakai bright mode' : 'Pakai dark mode');
            }

            setTheme(document.documentElement.getAttribute('data-theme') || 'light');

            themeToggle.on('click', function () {
                const currentTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
                setTheme(currentTheme === 'dark' ? 'light' : 'dark');
            });

            btnToggle.on('click', function () {
                if ($(window).width() > 992) {
                    sidebar.toggleClass('minimized');
                } else {
                    sidebar.toggleClass('show-mobile');
                }
            });

            $(document).on('click', function (e) {
                if ($(window).width() <= 992) {
                    if (!sidebar.is(e.target) && sidebar.has(e.target).length === 0 && !btnToggle.is(e.target) && btnToggle.has(e.target).length === 0) {
                        sidebar.removeClass('show-mobile');
                    }
                }
            });

            $(document).on('click', '.js-logout-trigger', function (e) {
                e.preventDefault();
                const logoutForm = $(this).closest('form');
                const isConfirmed = window.confirm('Apakah kamu ingin keluar dari sistem?');

                if (isConfirmed && logoutForm.length) {
                    logoutForm.trigger('submit');
                }
            });
        });
    </script>
</body>
</html>
