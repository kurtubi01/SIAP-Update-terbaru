<?php

namespace App\Services\Monev;

use App\Models\Evaluasi;
use App\Models\Monitoring;
use App\Models\Sop;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ViewerMonevReportService
{
    public const EVALUASI_KRITERIA = [
        'Mampu mendorong peningkatan kinerja',
        'Mudah dipahami',
        'Mudah dilaksanakan',
        'Semua orang dapat menjalankan perannya masing-masing',
        'Mampu mengatasi permasalahan yang berkaitan dengan proses',
        'Mampu menjawab kebutuhan peningkatan kinerja organisasi',
    ];

    public function availableYears(): array
    {
        $years = Monitoring::query()
            ->pluck('tanggal')
            ->merge(Evaluasi::query()->pluck('tanggal'))
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->year)
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        return $years ?: [(int) now()->format('Y')];
    }

    public function resolvePeriod(?string $period): int
    {
        $requested = (int) $period;

        if ($requested >= 2000 && $requested <= 2100) {
            return $requested;
        }

        return (int) ($this->availableYears()[0] ?? now()->format('Y'));
    }

    public function rows(int $period): Collection
    {
        return Sop::with([
            'subjek.timkerja',
            'monitorings' => function ($query) use ($period) {
                $query->with('user')
                    ->whereYear('tanggal', $period)
                    ->orderByDesc('id_monitoring');
            },
            'evaluasis' => function ($query) use ($period) {
                $query->with('user')
                    ->whereYear('tanggal', $period)
                    ->orderByDesc('id_evaluasi');
            },
        ])
            ->where(function ($query) use ($period) {
                $query->whereHas('monitorings', function ($monitoringQuery) use ($period) {
                    $monitoringQuery->whereYear('tanggal', $period);
                })->orWhereHas('evaluasis', function ($evaluasiQuery) use ($period) {
                    $evaluasiQuery->whereYear('tanggal', $period);
                });
            })
            ->get()
            ->map(function (Sop $sop) {
                $monitoring = $sop->monitorings->first();
                $evaluasi = $sop->evaluasis->first();

                return [
                    'sop' => $sop,
                    'monitoring' => $monitoring,
                    'evaluasi' => $evaluasi,
                    'unit_label' => $sop->subjek?->timkerja?->nama_timkerja ?: 'Tanpa Tim Kerja',
                    'criteria' => collect($evaluasi?->kriteria_evaluasi ?? [])->filter()->values()->all(),
                ];
            })
            ->sortBy(fn ($row) => mb_strtolower($row['unit_label'] . '|' . (string) $row['sop']->nama_sop))
            ->values();
    }

    public function groupedRows(int $period): Collection
    {
        return $this->rows($period)->groupBy('unit_label');
    }
}
