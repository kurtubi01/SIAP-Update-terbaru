<?php

namespace App\Http\Controllers;

use App\Services\Monev\ViewerMonevReportService;
use Illuminate\Http\Request;

class MonevReportController extends Controller
{
    public function __construct(
        private ViewerMonevReportService $reportService
    ) {
    }

    public function monitoring(Request $request)
    {
        return $this->reportView($request, 'monitoring');
    }

    public function evaluasi(Request $request)
    {
        return $this->reportView($request, 'evaluasi');
    }

    public function downloadExcel(Request $request)
    {
        $period = $this->reportService->resolvePeriod($request->query('periode'));
        $groupedRows = $this->reportService->groupedRows($period);
        $filename = 'laporan-monev-sop-ap-' . $period . '.xls';
        $content = view('pages.viewer.monev-report-excel', [
            'period' => $period,
            'groupedRows' => $groupedRows,
            'criteriaOptions' => ViewerMonevReportService::EVALUASI_KRITERIA,
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function reportView(Request $request, string $section)
    {
        $period = $this->reportService->resolvePeriod($request->query('periode'));

        return view('pages.viewer.monev-report', [
            'section' => $section,
            'period' => $period,
            'availableYears' => $this->reportService->availableYears(),
            'groupedRows' => $this->reportService->groupedRows($period),
            'criteriaOptions' => ViewerMonevReportService::EVALUASI_KRITERIA,
        ]);
    }
}
