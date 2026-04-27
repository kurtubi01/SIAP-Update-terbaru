@extends('layouts.sidebarmenu')

@section('content')
<style>
    .report-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }

    .report-actions {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        flex-wrap: wrap;
    }

    .report-sheet {
        background: #ffffff;
        border: 1px solid #d6e0ec;
        border-radius: 10px;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
        padding: 28px 28px 18px;
        overflow-x: auto;
    }

    .report-title {
        text-align: center;
        color: #000000;
        font-weight: 800;
        line-height: 1.35;
        margin-bottom: 3rem;
        text-transform: uppercase;
    }

    .report-title div {
        font-size: 1.05rem;
    }

    .report-table {
        width: 100%;
        min-width: 1120px;
        border-collapse: collapse;
        color: #000000;
        font-size: 0.86rem;
        line-height: 1.55;
    }

    .report-table th,
    .report-table td {
        border: 1px solid #000000;
        padding: 7px 8px;
        vertical-align: top;
        background: #ffffff;
    }

    .report-table thead th {
        vertical-align: middle;
        text-align: center;
        font-weight: 800;
    }

    .report-no {
        width: 42px;
    }

    .report-sop {
        width: 190px;
    }

    .report-evaluasi {
        width: 330px;
    }

    .report-monitoring-choice {
        width: 135px;
        text-align: left;
    }

    .report-monitoring-result,
    .report-monitoring-action {
        width: 270px;
    }

    .report-table thead .report-no,
    .report-table thead .report-sop {
        background: #8fb5df;
    }

    .report-table thead .report-evaluasi {
        background: #d8e4bd;
    }

    .report-monitoring-head {
        background: #f5bf8a !important;
    }

    .report-unit {
        background: #d9edf4 !important;
        font-weight: 800;
        text-transform: uppercase;
    }

    .check-line {
        display: flex;
        align-items: flex-start;
        gap: 0.35rem;
    }

    .report-list {
        margin: 0;
        padding-left: 1.1rem;
    }

    .report-list li + li {
        margin-top: 0.55rem;
    }

    [data-theme="dark"] .report-sheet {
        background: #111827 !important;
        border-color: #334155 !important;
    }

    [data-theme="dark"] .report-title,
    [data-theme="dark"] .report-table {
        color: #f8fafc !important;
    }

    [data-theme="dark"] .report-table th,
    [data-theme="dark"] .report-table td {
        background: #0f172a !important;
        color: #f8fafc !important;
        border-color: #64748b !important;
    }

    [data-theme="dark"] .report-table thead .report-no,
    [data-theme="dark"] .report-table thead .report-sop {
        background: #1d4ed8 !important;
    }

    [data-theme="dark"] .report-table thead .report-evaluasi {
        background: #365314 !important;
    }

    [data-theme="dark"] .report-monitoring-head {
        background: #9a3412 !important;
    }

    [data-theme="dark"] .report-unit {
        background: #164e63 !important;
    }

    @media print {
        #sidebar,
        #btn-toggle-custom,
        .top-navbar,
        footer,
        .report-toolbar {
            display: none !important;
        }

        #content {
            margin-left: 0 !important;
        }

        main {
            padding: 0;
        }

        .report-sheet {
            box-shadow: none;
            border: 0;
            padding: 0;
        }
    }
</style>

<div class="container-fluid app-page-shell py-4">
    <div class="report-toolbar">
        <div>
            <h1 class="app-page-title">Laporan Monitoring dan Evaluasi</h1>
            <p class="app-page-subtitle">Format laporan viewer disusun seperti lembar resmi hasil monitoring dan evaluasi SOP AP.</p>
        </div>

        <div class="report-actions">
            <form method="GET" action="{{ route('viewer.' . $section . '.index') }}" class="d-flex align-items-center gap-2">
                <select name="periode" class="form-select rounded-3" onchange="this.form.submit()">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ (int) $period === (int) $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('viewer.monev.report.download', ['periode' => $period]) }}" class="btn btn-success fw-bold rounded-3">
                <i class="bi bi-download me-2"></i>Unduh Laporan
            </a>
            <button type="button" class="btn btn-outline-secondary fw-bold rounded-3" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Cetak
            </button>
        </div>
    </div>

    <div class="report-sheet">
        <div class="report-title">
            <div>Laporan Hasil</div>
            <div>Monitoring dan Evaluasi</div>
            <div>Sistem Operasional Prosedur Administrasi Pemerintahan (SOP AP)</div>
            <div>Badan Pusat Statistik Provinsi Banten</div>
            <div>Periode {{ $period }}</div>
        </div>

        @include('pages.viewer.partials.monev-report-table', [
            'groupedRows' => $groupedRows,
            'criteriaOptions' => $criteriaOptions,
        ])
    </div>
</div>
@endsection
