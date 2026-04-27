<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000000;
        }
        .report-title {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.35;
            margin-bottom: 34px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            line-height: 1.45;
        }
        .report-table th,
        .report-table td {
            border: 1px solid #000000;
            padding: 6px 7px;
            vertical-align: top;
        }
        .report-table thead th {
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
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
            background: #f5bf8a;
        }
        .report-unit {
            background: #d9edf4;
            font-weight: bold;
            text-transform: uppercase;
        }
        .check-line {
            display: block;
            margin-bottom: 3px;
        }
        .report-list {
            margin: 0;
            padding-left: 16px;
        }
        .text-center {
            text-align: center;
        }
        .text-muted {
            color: #555555;
        }
    </style>
</head>
<body>
    <div class="report-title">
        <div>LAPORAN HASIL</div>
        <div>MONITORING DAN EVALUASI</div>
        <div>SISTEM OPERASIONAL PROSEDUR ADMINISTRASI PEMERINTAHAN (SOP AP)</div>
        <div>BADAN PUSAT STATISTIK PROVINSI BANTEN</div>
        <div>PERIODE {{ $period }}</div>
    </div>

    @include('pages.viewer.partials.monev-report-table', [
        'groupedRows' => $groupedRows,
        'criteriaOptions' => $criteriaOptions,
    ])
</body>
</html>
