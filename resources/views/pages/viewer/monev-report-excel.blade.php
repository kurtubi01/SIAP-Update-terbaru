<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:x="urn:schemas-microsoft-com:office:excel"
    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:html="http://www.w3.org/TR/REC-html40">
    <Styles>
        <Style ss:ID="Default" ss:Name="Normal">
            <Alignment ss:Vertical="Top" ss:WrapText="1"/>
            <Borders/>
            <Font ss:FontName="Arial" ss:Size="10"/>
            <Interior/>
            <NumberFormat/>
            <Protection/>
        </Style>
        <Style ss:ID="Title">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
            <Font ss:FontName="Arial" ss:Size="11" ss:Bold="1"/>
        </Style>
        <Style ss:ID="HeaderBlue">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
            </Borders>
            <Font ss:FontName="Arial" ss:Size="10" ss:Bold="1"/>
            <Interior ss:Color="#8FB5DF" ss:Pattern="Solid"/>
        </Style>
        <Style ss:ID="HeaderGreen">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
            </Borders>
            <Font ss:FontName="Arial" ss:Size="10" ss:Bold="1"/>
            <Interior ss:Color="#D8E4BD" ss:Pattern="Solid"/>
        </Style>
        <Style ss:ID="HeaderOrange">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
            </Borders>
            <Font ss:FontName="Arial" ss:Size="10" ss:Bold="1"/>
            <Interior ss:Color="#F5BF8A" ss:Pattern="Solid"/>
        </Style>
        <Style ss:ID="UnitRow">
            <Alignment ss:Vertical="Center" ss:WrapText="1"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
            </Borders>
            <Font ss:FontName="Arial" ss:Size="10" ss:Bold="1"/>
            <Interior ss:Color="#D9EDF4" ss:Pattern="Solid"/>
        </Style>
        <Style ss:ID="Cell">
            <Alignment ss:Vertical="Top" ss:WrapText="1"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
            </Borders>
            <Font ss:FontName="Arial" ss:Size="10"/>
        </Style>
        <Style ss:ID="CellCenter">
            <Alignment ss:Horizontal="Center" ss:Vertical="Top" ss:WrapText="1"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
            </Borders>
            <Font ss:FontName="Arial" ss:Size="10"/>
        </Style>
    </Styles>
    <Worksheet ss:Name="Laporan Monev {{ $period }}">
        <Table>
            <Column ss:Width="45"/>
            <Column ss:Width="180"/>
            <Column ss:Width="320"/>
            <Column ss:Width="135"/>
            <Column ss:Width="260"/>
            <Column ss:Width="260"/>
            <Row><Cell ss:MergeAcross="5" ss:StyleID="Title"><Data ss:Type="String">LAPORAN HASIL</Data></Cell></Row>
            <Row><Cell ss:MergeAcross="5" ss:StyleID="Title"><Data ss:Type="String">MONITORING DAN EVALUASI</Data></Cell></Row>
            <Row><Cell ss:MergeAcross="5" ss:StyleID="Title"><Data ss:Type="String">SISTEM OPERASIONAL PROSEDUR ADMINISTRASI PEMERINTAHAN (SOP AP)</Data></Cell></Row>
            <Row><Cell ss:MergeAcross="5" ss:StyleID="Title"><Data ss:Type="String">BADAN PUSAT STATISTIK PROVINSI BANTEN</Data></Cell></Row>
            <Row><Cell ss:MergeAcross="5" ss:StyleID="Title"><Data ss:Type="String">PERIODE {{ $period }}</Data></Cell></Row>
            <Row/>
            <Row>
                <Cell ss:MergeDown="1" ss:StyleID="HeaderBlue"><Data ss:Type="String">NO</Data></Cell>
                <Cell ss:MergeDown="1" ss:StyleID="HeaderBlue"><Data ss:Type="String">NOMOR dan NAMA SOP AP</Data></Cell>
                <Cell ss:MergeDown="1" ss:StyleID="HeaderGreen"><Data ss:Type="String">KRITERIA EVALUASI PENILAIAN</Data></Cell>
                <Cell ss:MergeAcross="2" ss:StyleID="HeaderOrange"><Data ss:Type="String">KRITERIA PENILAIAN MONITORING</Data></Cell>
            </Row>
            <Row>
                <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">Penilaian Terhadap Penerapan</Data></Cell>
                <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">Catatan Hasil Penilaian</Data></Cell>
                <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">Tindakan Yang Harus Diambil</Data></Cell>
            </Row>
            @php
                $rowNumber = 1;
                $toLines = function ($value) {
                    return collect(preg_split('/\r\n|\r|\n/', (string) $value))
                        ->map(fn ($line) => trim($line))
                        ->filter()
                        ->values();
                };
            @endphp
            @forelse($groupedRows as $unitLabel => $rows)
                <Row>
                    <Cell ss:MergeAcross="5" ss:StyleID="UnitRow"><Data ss:Type="String">UNIT {{ strtoupper($unitLabel) }}</Data></Cell>
                </Row>
                @foreach($rows as $row)
                    @php($sop = $row['sop'])
                    @php($monitoring = $row['monitoring'])
                    @php($selectedCriteria = $row['criteria'])
                    @php($criteriaText = $selectedCriteria
                        ? collect($criteriaOptions)->map(fn ($criteria) => (in_array($criteria, $selectedCriteria, true) ? '[x] ' : '[ ] ') . $criteria)->implode('&#10;')
                        : 'Belum ada evaluasi pada periode ini.')
                    @php($monitoringChoice = $monitoring
                        ? (($monitoring->kriteria_penilaian === 'Berjalan dengan baik' ? '[x] ' : '[ ] ') . 'Berjalan Dengan Baik' . '&#10;' .
                           ($monitoring->kriteria_penilaian === 'Tidak berjalan dengan baik' ? '[x] ' : '[ ] ') . 'Tidak Berjalan Dengan Baik')
                        : 'Belum ada monitoring.')
                    @php($hasilText = $toLines($monitoring?->hasil_monitoring)->isNotEmpty() ? $toLines($monitoring?->hasil_monitoring)->map(fn ($line) => '- ' . $line)->implode('&#10;') : '-')
                    @php($tindakanText = $toLines($monitoring?->tindakan)->isNotEmpty() ? $toLines($monitoring?->tindakan)->map(fn ($line) => '- ' . $line)->implode('&#10;') : '-')
                    <Row>
                        <Cell ss:StyleID="CellCenter"><Data ss:Type="Number">{{ $rowNumber++ }}</Data></Cell>
                        <Cell ss:StyleID="Cell"><Data ss:Type="String">{{ $sop->nomor_sop ?: '-' }}&#10;{{ $sop->nama_sop ?: '-' }}</Data></Cell>
                        <Cell ss:StyleID="Cell"><Data ss:Type="String">{!! $criteriaText !!}</Data></Cell>
                        <Cell ss:StyleID="Cell"><Data ss:Type="String">{!! $monitoringChoice !!}</Data></Cell>
                        <Cell ss:StyleID="Cell"><Data ss:Type="String">{!! $hasilText !!}</Data></Cell>
                        <Cell ss:StyleID="Cell"><Data ss:Type="String">{!! $tindakanText !!}</Data></Cell>
                    </Row>
                @endforeach
            @empty
                <Row>
                    <Cell ss:MergeAcross="5" ss:StyleID="CellCenter"><Data ss:Type="String">Belum ada data monitoring dan evaluasi pada periode ini.</Data></Cell>
                </Row>
            @endforelse
        </Table>
        <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
            <PageSetup>
                <Layout x:Orientation="Landscape"/>
            </PageSetup>
            <DisplayGridlines/>
            <FitToPage/>
        </WorksheetOptions>
    </Worksheet>
</Workbook>
