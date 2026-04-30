@extends('layouts.sidebarmenu')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@php($dashboardRole = $role ?? strtolower(Auth::user()->role ?? 'admin'))
@php($dashboardTheme = [
    'admin' => ['accent' => '#0d47a1', 'badge' => 'Kontrol penuh', 'summary' => 'Pusat kendali untuk seluruh unit kerja.', 'quickLinks' => [
        ['label' => 'Nama Subjek Sesuai SOP', 'route' => 'admin.sop.aksescepat', 'icon' => 'bi-lightning-charge-fill', 'tone' => 'text-warning'],
        ['label' => 'Manajemen User', 'route' => 'admin.user.index', 'icon' => 'bi-person-gear', 'tone' => 'text-info'],
        ['label' => 'Monitoring', 'route' => 'admin.monitoring.index', 'icon' => 'bi-bar-chart-steps', 'tone' => 'text-success'],
        ['label' => 'Manajemen Subjek', 'route' => 'admin.subjek.index', 'icon' => 'bi-tags', 'tone' => 'text-secondary'],
    ]],
    'operator' => ['accent' => '#0f766e', 'badge' => 'Operasional', 'summary' => 'Fokus pada input SOP, monitoring, dan evaluasi tim kerja Anda.', 'quickLinks' => [
        ['label' => 'Nama Subjek Sesuai SOP', 'route' => 'operator.sop.aksescepat', 'icon' => 'bi-lightning-charge-fill', 'tone' => 'text-warning'],
        ['label' => 'Tambah SOP', 'route' => 'operator.sop.create', 'icon' => 'bi-plus-circle-fill', 'tone' => 'text-primary'],
        ['label' => 'Monitoring', 'route' => 'operator.monitoring.create', 'icon' => 'bi-clipboard2-plus-fill', 'tone' => 'text-success'],
        ['label' => 'Evaluasi', 'route' => 'operator.evaluasi.create', 'icon' => 'bi-ui-checks-grid', 'tone' => 'text-warning'],
    ]],
    'viewer' => ['accent' => '#7c3aed', 'badge' => 'Read only', 'summary' => 'Viewer dapat membuka data yang diizinkan tanpa menu sidebar repositori.', 'quickLinks' => [
        ['label' => 'Lihat SOP', 'route' => 'viewer.sop.aksescepat', 'icon' => 'bi-folder2-open', 'tone' => 'text-primary'],
        ['label' => 'Monitoring', 'route' => 'viewer.monitoring.index', 'icon' => 'bi-graph-up', 'tone' => 'text-success'],
        ['label' => 'Evaluasi', 'route' => 'viewer.evaluasi.index', 'icon' => 'bi-ui-checks-grid', 'tone' => 'text-warning'],
    ]],
][$dashboardRole])

<style>
    .dashboard-shell {
        font-family: 'Inter', 'Nunito', sans-serif;
    }

    /* Animasi masuk */
    .fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Top Bar Luxury Style */
    .top-header {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,0.16), transparent 24%),
            linear-gradient(135deg, {{ $dashboardTheme['accent'] }} 0%, #111827 100%);
        border-radius: 28px;
        padding: 24px 30px;
        box-shadow: 0 16px 32px rgba(15,23,42,0.14);
        margin-bottom: 25px;
        border: 1px solid rgba(255,255,255,0.08);
    }

    /* Card Stats dengan Gradasi & Soft Shadow */
    .card-stat {
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 24px;
        padding: 25px;
        color: white;
        transition: 0.3s ease;
        position: relative;
        overflow: hidden;
        min-height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .card-stat:hover { transform: translateY(-7px); box-shadow: 0 15px 30px rgba(0,0,0,0.12); }

    .bg-gradient-blue { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); }
    .bg-gradient-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .bg-gradient-orange { background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%); }
    .bg-gradient-red { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }

    /* Icon Glassmorphism effect */
    .stat-icon {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 80px;
        opacity: 0.15;
        transform: rotate(-15deg);
    }

    /* Navigasi Cepat Style */
    .nav-box {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border: 1px solid #dbe5f1;
        border-radius: 22px;
        padding: 22px 18px;
        text-align: center;
        text-decoration: none !important;
        transition: 0.28s ease;
        height: 100%;
        display: block;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.04);
    }
    .nav-box:hover {
        background: linear-gradient(180deg, #ffffff 0%, #f1f7ff 100%);
        border-color: #bfdbfe;
        box-shadow: 0 18px 30px rgba(30, 60, 114, 0.10);
        transform: translateY(-4px);
    }
    .nav-box .nav-icon-wrap {
        width: 56px;
        height: 56px;
        margin: 0 auto 14px;
        border-radius: 18px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .nav-box .nav-label {
        color: #0f172a;
        font-size: 0.92rem;
        font-weight: 800;
        display: block;
    }
    .nav-box .nav-caption {
        color: #64748b;
        font-size: 0.76rem;
        margin-top: 6px;
    }

    /* Digital Clock Header */
    .time-badge {
        background: rgba(255,255,255,0.12);
        color: #ffffff;
        padding: 10px 16px;
        border-radius: 18px;
        border: 1px solid rgba(255,255,255,0.18);
        min-width: 176px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.08);
    }

    .time-badge .date-line {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.76);
        margin-bottom: 6px;
    }

    .time-badge .time-line {
        font-weight: 800;
        font-family: 'Courier New', Courier, monospace;
        font-size: 1.15rem;
        line-height: 1.1;
        letter-spacing: 0.14em;
    }

    .time-badge .zone-line {
        font-size: 0.66rem;
        font-weight: 700;
        letter-spacing: 0.16em;
        color: rgba(255,255,255,0.7);
        margin-top: 6px;
    }

    .user-greeting h4 { color: #ffffff; font-weight: 800; margin: 0; }
    .user-greeting p { color: rgba(255,255,255,0.82); font-size: 0.9rem; margin: 0; }
    .role-chip {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,0.14);
        border: 1px solid rgba(255,255,255,0.18);
        color: #fff;
        font-size: 0.8rem;
        font-weight: 700;
        margin-top: 10px;
    }

    .dashboard-chart-card {
        border-radius: 24px;
        border: 1px solid #dfe7f1;
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.05);
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .viewer-report-card {
        border-radius: 26px;
        border: 1px solid #dbe5f1;
        background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .viewer-report-head {
        padding: 24px 26px;
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%);
        color: #fff;
    }

    .viewer-report-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .viewer-report-table th,
    .viewer-report-table td {
        border: 1px solid #d8e2f0;
        padding: 14px 12px;
        vertical-align: top;
        font-size: 0.88rem;
    }

    .viewer-report-table th {
        background: #eef4ff;
        color: #1e3a8a;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.78rem;
    }

    .report-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.45rem 0.8rem;
        border-radius: 999px;
        border: 1px solid #dbeafe;
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 700;
        font-size: 0.78rem;
    }

    [data-theme="dark"] .dashboard-chart-card,
    [data-theme="dark"] .viewer-report-card,
    [data-theme="dark"] .nav-box {
        background: linear-gradient(180deg, #111827 0%, #172033 100%) !important;
        border-color: #334155 !important;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
    }

    [data-theme="dark"] .nav-box:hover {
        background: linear-gradient(180deg, #172033 0%, #1f2a3d 100%) !important;
        border-color: #475569 !important;
        box-shadow: 0 18px 30px rgba(0, 0, 0, 0.26);
    }

    [data-theme="dark"] .nav-box .nav-icon-wrap {
        background: linear-gradient(135deg, #1f2a3d 0%, #23314a 100%) !important;
    }

    [data-theme="dark"] .nav-box .nav-label,
    [data-theme="dark"] .viewer-report-table td,
    [data-theme="dark"] .viewer-report-table td .fw-bold {
        color: #e5edf7 !important;
    }

    [data-theme="dark"] .nav-box .nav-caption,
    [data-theme="dark"] .viewer-report-table .text-muted,
    [data-theme="dark"] .viewer-report-table td .text-muted {
        color: #94a3b8 !important;
    }

    [data-theme="dark"] .viewer-report-head {
        background: linear-gradient(135deg, #1e293b 0%, #3730a3 100%) !important;
    }

    [data-theme="dark"] .viewer-report-table {
        background: #111827 !important;
        border-radius: 18px;
    }

    [data-theme="dark"] .viewer-report-table table {
        background: #111827 !important;
    }

    [data-theme="dark"] .viewer-report-table th {
        background: #1f2a3d !important;
        color: #93c5fd !important;
        border-color: #334155 !important;
    }

    [data-theme="dark"] .viewer-report-table td {
        background: #111827 !important;
        border-color: #334155 !important;
    }

    [data-theme="dark"] .viewer-report-card .btn-outline-success,
    [data-theme="dark"] .viewer-report-card .btn-outline-primary,
    [data-theme="dark"] .viewer-report-card .btn-outline-dark {
        background: #172033 !important;
        color: #e5edf7 !important;
        border-color: #475569 !important;
    }

    [data-theme="dark"] .viewer-report-card .btn-outline-success:hover,
    [data-theme="dark"] .viewer-report-card .btn-outline-primary:hover,
    [data-theme="dark"] .viewer-report-card .btn-outline-dark:hover {
        background: #1f2a3d !important;
        color: #ffffff !important;
    }

    [data-theme="dark"] .bg-light-subtle {
        background: #172033 !important;
        border-color: #334155 !important;
    }

    [data-theme="dark"] .badge.bg-light.text-dark {
        background: #1f2a3d !important;
        color: #dbeafe !important;
        border-color: #334155 !important;
    }

    @media (max-width: 991.98px) {
        .top-header {
            padding: 20px;
            align-items: flex-start !important;
            flex-direction: column;
            gap: 18px;
        }

        .card-stat {
            min-height: 126px;
        }

        .nav-box {
            padding: 18px 14px;
        }
    }

    @media (max-width: 576px) {
        .dashboard-shell {
            padding-top: 8px !important;
        }

        .user-greeting {
            gap: 12px !important;
            align-items: flex-start !important;
        }

        .user-greeting h4 {
            font-size: 1.08rem;
            line-height: 1.35;
        }

        .user-greeting p {
            font-size: 0.84rem;
            line-height: 1.55;
        }

        .card-stat {
            min-height: 112px;
            padding: 20px;
        }

        .card-stat h1 {
            font-size: 2rem;
        }

        .nav-box .nav-label {
            font-size: 0.88rem;
        }

        .nav-box .nav-caption {
            font-size: 0.72rem;
            line-height: 1.45;
        }
    }
</style>

<div class="container-fluid dashboard-shell fade-in-up py-4">

    <div class="top-header d-flex justify-content-between align-items-center">
        <div class="user-greeting d-flex align-items-center gap-3">
            <div class="avatar-wrapper">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama) }}&background=1e3c72&color=fff"
                     class="rounded-circle" width="50" alt="Profile">
            </div>
            <div>
                <h4>Selamat Datang, {{ Auth::user()->nama }}! 👋</h4>
                <p>{{ $dashboardTheme['summary'] }}</p>
                <div class="role-chip">{{ strtoupper(Auth::user()->role) }} • {{ $dashboardTheme['badge'] }}</div>
            </div>
        </div>
        <div class="text-end d-none d-md-block">
            <div class="time-badge shadow-sm text-center">
                <div class="date-line" id="realtime-date">{{ now()->translatedFormat('d F Y') }}</div>
                <div class="time-line" id="realtime-clock">00:00:00</div>
                <div class="zone-line">WIB</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card card-stat bg-gradient-blue shadow">
                <i class="bi bi-file-earmark-text stat-icon"></i>
                <div class="fw-bold opacity-75 small">TOTAL DOKUMEN</div>
                <h1 class="fw-extrabold mb-0">{{ $totalSop }}</h1>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat bg-gradient-green shadow">
                <i class="bi bi-shield-check stat-icon"></i>
                <div class="fw-bold opacity-75 small">SOP AKTIF</div>
                <h1 class="fw-extrabold mb-0">{{ $aman ?? 0 }}</h1>
            </div>

        </div>
        <div class="col-md-3">
            <div class="card card-stat bg-gradient-orange shadow">
                <i class="bi bi-clipboard2-data stat-icon"></i>
                <div class="fw-bold opacity-75 small">MONITORING</div>
                <h1 class="fw-extrabold mb-0">{{ $totalMonitoring ?? 0 }}</h1>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat bg-gradient-red shadow">
                <i class="bi bi-ui-checks-grid stat-icon"></i>
                <div class="fw-bold opacity-75 small">EVALUASI</div>
                <h1 class="fw-extrabold mb-0">{{ $totalEvaluasi ?? 0 }}</h1>
            </div>
        </div>
    </div>

    @if($dashboardRole === 'admin' && ($pendingAccountRecoveryCount ?? 0) > 0)
        <div class="card dashboard-chart-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Notifikasi Bantuan Akun</h5>
                    <div class="text-muted small">Permintaan lupa username/password dari halaman login masuk ke admin di sini.</div>
                </div>
                <span class="report-chip"><i class="bi bi-bell-fill"></i> {{ $pendingAccountRecoveryCount }} permintaan aktif</span>
            </div>
            <div class="row g-3">
                @foreach($accountRecoveryNotifications as $item)
                    @php($meta = $item->metadata ?? [])
                    <div class="col-12 col-lg-6">
                        <div class="border rounded-4 p-3 h-100 bg-light-subtle">
                            <div class="fw-bold text-dark">{{ $meta['nama'] ?? ($item->user->nama ?? 'Pengguna') }}</div>
                            <div class="text-muted small mb-2">NIP: {{ $meta['nip'] ?? ($item->user->nip ?? '-') }}</div>
                            <div class="small mb-2">{{ $item->pesan }}</div>
                            <div class="small text-muted mb-3">{{ $meta['catatan'] ?? 'Tanpa catatan tambahan.' }}</div>
                            @if($item->user)
                                <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-primary fw-bold">Proses di Manajemen User</a>
                            @else
                                <span class="text-danger small fw-bold">User belum teridentifikasi dari nama/NIP.</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($dashboardRole === 'viewer')
        <div class="viewer-report-card mb-4">
            <div class="viewer-report-head">
                <div class="text-uppercase fw-bold small opacity-75">Laporan Viewer</div>
                <h4 class="fw-bold mb-1">Laporan Hasil Monitoring dan Evaluasi SOP</h4>
                <div class="opacity-75">Ringkasan baca untuk dokumen yang sudah dimonitoring atau dievaluasi.</div>
            </div>
            <div class="p-4">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="report-chip"><i class="bi bi-clipboard2-pulse"></i> {{ $totalMonitoring ?? 0 }} monitoring</span>
                    <span class="report-chip"><i class="bi bi-ui-checks-grid"></i> {{ $totalEvaluasi ?? 0 }} evaluasi</span>
                    <span class="report-chip"><i class="bi bi-file-earmark-text"></i> {{ $totalSop ?? 0 }} dokumen</span>
                </div>
                <div class="viewer-report-table table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor dan Nama SOP</th>
                                <th>Subjek</th>
                                <th>Status Monitoring</th>
                                <th>Status Evaluasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($viewerReportItems as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $item->nomor_sop }}</div>
                                        <div>{{ $item->nama_sop }}</div>
                                    </td>
                                    <td>
                                        {{ $item->subjek?->nama_subjek ?? '-' }}
                                        @if($item->subjek?->timkerja?->nama_timkerja)
                                            <div class="text-muted small">{{ $item->subjek->timkerja->nama_timkerja }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->monitorings_count > 0)
                                            <span class="text-success fw-bold">Sudah dimonitoring</span>
                                        @else
                                            <span class="text-muted">Belum ada monitoring</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->evaluasis_count > 0)
                                            <span class="text-primary fw-bold">Sudah dievaluasi</span>
                                        @else
                                            <span class="text-muted">Belum ada evaluasi</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada data laporan monitoring/evaluasi yang bisa ditampilkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <a href="{{ route('viewer.monitoring.index') }}" class="btn btn-outline-success fw-bold">Lihat Monitoring</a>
                    <a href="{{ route('viewer.evaluasi.index') }}" class="btn btn-outline-primary fw-bold">Lihat Evaluasi</a>
                    <a href="{{ route('viewer.sop.aksescepat') }}" class="btn btn-outline-dark fw-bold">Buka Repositori SOP</a>
                </div>
            </div>
        </div>
    @endif

    @if(!empty($dashboardTheme['quickLinks']))
        <div class="mb-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-lightning-fill text-warning me-2"></i>Akses Cepat Subjek</h5>
            <div class="row g-3">
                @foreach($dashboardTheme['quickLinks'] as $item)
                    <div class="col-6 col-md-3 col-xl-3">
                        <a href="{{ route($item['route']) }}" class="nav-box">
                            <div class="nav-icon-wrap">
                                <i class="bi {{ $item['icon'] }} {{ $item['tone'] }} fs-3"></i>
                            </div>
                            <span class="nav-label">{{ $item['label'] }}</span>
                            <span class="nav-caption">Buka menu terkait dengan cepat</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="row g-4 mt-2">
        <div class="col-lg-8">
            <div class="card dashboard-chart-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold text-dark mb-0">Statistik Nama Subjek</h6>
                    <span class="badge bg-light text-dark rounded-pill">Data Terkini</span>
                </div>
                <div style="height: 320px;">
                    <canvas id="bidangChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card dashboard-chart-card p-4">
                    <h6 class="fw-bold text-dark mb-4">Kesehatan Berkas</h6>
                    <div style="height: 220px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>SOP revisi:</span>
                        <span class="fw-bold">{{ $review ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>SOP nonaktif/kadaluarsa:</span>
                        <span class="fw-bold">{{ $kritis ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Real-time Clock Header
    function updateClock() {
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        document.getElementById('realtime-clock').textContent = now.toLocaleTimeString('id-ID', options);
        document.getElementById('realtime-date').textContent = now.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Data Dinamis dari HomeController
    const subjekLabels = {!! json_encode($labels) !!};
    const subjekCounts = {!! json_encode($dataCounts) !!};

    // Fungsi untuk generate warna dinamis berdasarkan jumlah label
    function generateColors(count) {
        const colors = [
            '#1e3c72', '#2a5298', '#11998e', '#38ef7d', '#f2994a',
            '#f2c94c', '#eb3349', '#f45c43', '#8e44ad', '#2c3e50',
            '#16a085', '#27ae60', '#2980b9', '#f39c12', '#d35400'
        ];
        let dynamicColors = [];
        for (let i = 0; i < count; i++) {
            // Gunakan warna dari array, jika habis ulangi dari awal
            dynamicColors.push(colors[i % colors.length]);
        }
        return dynamicColors;
    }

    function isDarkTheme() {
        return document.documentElement.getAttribute('data-theme') === 'dark';
    }

    function chartTextColor() {
        return isDarkTheme() ? '#e5edf7' : '#334155';
    }

    function chartGridColor() {
        return isDarkTheme() ? 'rgba(148, 163, 184, 0.22)' : '#f0f0f0';
    }

    function chartCardBorderColor() {
        return isDarkTheme() ? '#334155' : '#ffffff';
    }

    // Chart Bar (Warna Berbeda tiap Subjek)
    const bidangChart = new Chart(document.getElementById('bidangChart'), {
        type: 'bar',
        data: {
            labels: subjekLabels,
            datasets: [{
                label: 'Jumlah SOP',
                data: subjekCounts,
                backgroundColor: generateColors(subjekLabels.length), // Menggunakan fungsi warna
                borderColor: chartCardBorderColor(),
                borderWidth: 1,
                borderRadius: 10,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.raw + ' Dokumen';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: chartGridColor() },
                    ticks: { stepSize: 1, color: chartTextColor() }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: chartTextColor() }
                }
            }
        }
    });

    // Chart Doughnut (Status Berkas)
    const statusChart = new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Aktif', 'Revisi', 'Nonaktif/Kadaluarsa'],
            datasets: [{
                data: [{{ $aman ?? 0 }}, {{ $review ?? 0 }}, {{ $kritis ?? 0 }}],
                backgroundColor: ['#11998e', '#f2994a', '#eb3349'],
                borderColor: isDarkTheme() ? '#111827' : '#ffffff',
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 20, font: { size: 12 }, color: chartTextColor() }
                }
            }
        }
    });

    function syncDashboardChartsWithTheme() {
        bidangChart.data.datasets[0].borderColor = chartCardBorderColor();
        bidangChart.options.scales.y.grid.color = chartGridColor();
        bidangChart.options.scales.y.ticks.color = chartTextColor();
        bidangChart.options.scales.x.ticks.color = chartTextColor();
        bidangChart.update();

        statusChart.data.datasets[0].borderColor = isDarkTheme() ? '#111827' : '#ffffff';
        statusChart.options.plugins.legend.labels.color = chartTextColor();
        statusChart.update();
    }

    document.addEventListener('siap-theme-change', syncDashboardChartsWithTheme);
    syncDashboardChartsWithTheme();
</script>
@endsection
