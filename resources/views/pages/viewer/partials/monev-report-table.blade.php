@php
    $rowNumber = 1;
    $toLines = function ($value) {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values();
    };
@endphp

<table class="report-table">
    <thead>
        <tr>
            <th rowspan="2" class="report-no">NO</th>
            <th rowspan="2" class="report-sop">NOMOR dan NAMA<br>SOP AP</th>
            <th rowspan="2" class="report-evaluasi">KRITERIA EVALUASI PENILAIAN</th>
            <th colspan="3" class="report-monitoring-head">KRITERIA PENILAIAN MONITORING</th>
        </tr>
        <tr>
            <th class="report-monitoring-choice">Penilaian Terhadap<br>Penerapan</th>
            <th class="report-monitoring-result">Catatan Hasil Penilaian</th>
            <th class="report-monitoring-action">Tindakan Yang Harus Diambil</th>
        </tr>
    </thead>
    <tbody>
        @forelse($groupedRows as $unitLabel => $rows)
            <tr>
                <td colspan="6" class="report-unit">UNIT {{ strtoupper($unitLabel) }}</td>
            </tr>

            @foreach($rows as $row)
                @php($sop = $row['sop'])
                @php($monitoring = $row['monitoring'])
                @php($selectedCriteria = $row['criteria'])
                @php($hasilLines = $toLines($monitoring?->hasil_monitoring))
                @php($tindakanLines = $toLines($monitoring?->tindakan))
                <tr>
                    <td class="report-no text-center">{{ $rowNumber++ }}</td>
                    <td class="report-sop">
                        <div>{{ $sop->nomor_sop ?: '-' }}</div>
                        <div>{{ $sop->nama_sop ?: '-' }}</div>
                    </td>
                    <td class="report-evaluasi">
                        @if($selectedCriteria)
                            @foreach($criteriaOptions as $criteria)
                                <div class="check-line">
                                    <span>{!! in_array($criteria, $selectedCriteria, true) ? '&#9745;' : '&#9744;' !!}</span>
                                    <span>{{ $criteria }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-muted">Belum ada evaluasi pada periode ini.</div>
                        @endif
                    </td>
                    <td class="report-monitoring-choice">
                        @if($monitoring)
                            <div class="check-line">
                                <span>{!! $monitoring->kriteria_penilaian === 'Berjalan dengan baik' ? '&#9745;' : '&#9744;' !!}</span>
                                <span>Berjalan<br>Dengan Baik</span>
                            </div>
                            <div class="check-line mt-3">
                                <span>{!! $monitoring->kriteria_penilaian === 'Tidak berjalan dengan baik' ? '&#9745;' : '&#9744;' !!}</span>
                                <span>Tidak Berjalan<br>Dengan Baik</span>
                            </div>
                        @else
                            <div class="text-muted">Belum ada monitoring.</div>
                        @endif
                    </td>
                    <td class="report-monitoring-result">
                        @if($hasilLines->isNotEmpty())
                            <ul class="report-list">
                                @foreach($hasilLines as $line)
                                    <li>{{ $line }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="report-monitoring-action">
                        @if($tindakanLines->isNotEmpty())
                            <ul class="report-list">
                                @foreach($tindakanLines as $line)
                                    <li>{{ $line }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="6" class="text-center py-5">Belum ada data monitoring dan evaluasi pada periode ini.</td>
            </tr>
        @endforelse
    </tbody>
</table>
