<?php

namespace App\Imports;

use App\Models\Sop;
use App\Models\Subjek;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SopMassImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private array $pdfFiles = [];
    private int $successCount = 0;
    private int $failedCount = 0;
    private array $failedRows = [];

    public function __construct(
        array $uploadedPdfFiles,
        private readonly ?int $userId = null,
    ) {
        foreach ($uploadedPdfFiles as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);

            $this->pdfFiles[$this->normalizeKey($originalName)] = $file;
            $this->pdfFiles[$this->normalizeKey($baseName)] = $file;
        }
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $data = $this->normalizeRow($row);

            if ($this->isRowEmpty($data)) {
                continue;
            }

            $subjekLabel = trim((string) ($data['subjek'] ?? ''));
            $fileName = trim((string) ($data['nama_file'] ?? ''));

            if ($subjekLabel === '') {
                $this->markFailed($rowNumber, 'Kolom subjek kosong.');
                continue;
            }

            if ($fileName === '') {
                $this->markFailed($rowNumber, 'Kolom nama_file kosong.');
                continue;
            }
            

            $subjek = $this->resolveSubjek($subjekLabel);

            if (!$subjek) {
                $this->markFailed($rowNumber, 'Subjek "' . $subjekLabel . '" tidak ditemukan.');
                continue;
            }

            if (!$subjek->timkerja) {
                $this->markFailed($rowNumber, 'Tim kerja untuk subjek "' . $subjekLabel . '" tidak ditemukan.');
                continue;
            }

            $pdfFile = $this->findPdfFile($fileName);

            if (!$pdfFile) {
                $this->markFailed($rowNumber, 'File PDF "' . $fileName . '" tidak ditemukan.');
                continue;
            }

            $storedFilename = time() . '_' . Str::random(8) . '_' . $pdfFile->getClientOriginalName();
            $storedPath = $pdfFile->storeAs('sop', $storedFilename, 'public');

            Sop::create([
                'nama_sop' => trim((string) ($data['nama_sop'] ?? '')),
                'nomor_sop' => trim((string) ($data['nomor_sop'] ?? '')),
                'tahun' => $this->resolveYear($data['tahun'] ?? null),
                'id_subjek' => $subjek->id_subjek,
                'revisi_ke' => 0,
                'link_sop' => $storedPath,
                'status' => 'aktif',
                'created_date' => now(),
                'created_by' => $this->userId,
            ]);

            $this->successCount++;
        }
    }

    public function getSummary(): array
    {
        return [
            'success_count' => $this->successCount,
            'failed_count' => $this->failedCount,
            'failed_rows' => $this->failedRows,
        ];
    }

    private function normalizeRow($row): array
    {
        $normalized = [];

        foreach ($row->toArray() as $key => $value) {
            $normalizedKey = Str::snake(trim((string) $key));
            $normalized[$normalizedKey] = is_string($value) ? trim($value) : $value;
        }

        return $normalized;
    }

    private function isRowEmpty(array $data): bool
    {
        return collect($data)
            ->filter(fn ($value) => trim((string) $value) !== '')
            ->isEmpty();
    }

    private function findPdfFile(string $fileName): ?UploadedFile
    {
        $normalizedName = $this->normalizeKey($fileName);
        $normalizedBaseName = $this->normalizeKey(pathinfo($fileName, PATHINFO_FILENAME));

        return $this->pdfFiles[$normalizedName]
            ?? $this->pdfFiles[$normalizedBaseName]
            ?? null;
    }

    private function normalizeKey(string $value): string
    {
        $normalized = Str::lower(trim($value));
        $normalized = str_replace('\\', '/', $normalized);

        return preg_replace('/\s+/', ' ', $normalized) ?? $normalized;
    }

    private function resolveSubjek(string $subjekLabel): ?Subjek
    {
        [$subjekName, $timkerjaName] = $this->splitSubjekLabel($subjekLabel);

        $query = Subjek::with('timkerja')
            ->whereRaw('LOWER(TRIM(nama_subjek)) = ?', [Str::lower($subjekName)])
            ->orderBy('id_subjek');

        $candidates = $query->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        if ($timkerjaName === null) {
            return $candidates->first();
        }

        return $candidates->first(function (Subjek $subjek) use ($timkerjaName) {
            return Str::lower(trim((string) $subjek->timkerja?->nama_timkerja)) === Str::lower($timkerjaName);
        });
    }

    private function splitSubjekLabel(string $subjekLabel): array
    {
        foreach (['|', ' - '] as $separator) {
            if (!str_contains($subjekLabel, $separator)) {
                continue;
            }

            [$subjekName, $timkerjaName] = array_pad(explode($separator, $subjekLabel, 2), 2, null);

            return [
                trim((string) $subjekName),
                trim((string) $timkerjaName) ?: null,
            ];
        }

        return [trim($subjekLabel), null];
    }

    private function resolveYear(mixed $year): int
    {
        $yearString = trim((string) $year);

        if (preg_match('/\b(19|20)\d{2}\b/', $yearString, $matches)) {
            return (int) $matches[0];
        }

        return (int) now()->format('Y');
    }

    private function markFailed(int $rowNumber, string $reason): void
    {
        $this->failedCount++;
        $this->failedRows[] = [
            'row' => $rowNumber,
            'reason' => $reason,
        ];
    }
}
