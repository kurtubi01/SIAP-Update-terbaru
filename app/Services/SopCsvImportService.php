<?php

namespace App\Services;

use App\Models\Sop;
use App\Models\Subjek;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SopCsvImportService
{
    private array $pdfFiles = [];
    private array $storedPdfPaths = [];
    private int $successCount = 0;
    private int $failedCount = 0;
    private int $createdCount = 0;
    private int $updatedCount = 0;
    private array $failedRows = [];

    public function __construct(
        array $uploadedPdfFiles = [],
        private readonly ?int $userId = null,
    ) {
        $this->indexStoredPdfPaths();

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

    public function importFromPath(string $csvPath): array
    {
        if (!is_file($csvPath)) {
            return $this->failForMissingFile($csvPath);
        }

        $handle = fopen($csvPath, 'rb');

        if ($handle === false) {
            return $this->failForMissingFile($csvPath);
        }

        $delimiter = $this->detectDelimiter($handle);
        $header = fgetcsv($handle, 0, $delimiter);

        if (!is_array($header)) {
            fclose($handle);

            $this->markFailed(1, 'Header CSV tidak ditemukan.');
            return $this->getSummary();
        }

        $normalizedHeader = array_map(
            fn ($value) => Str::snake(trim((string) $value)),
            $header
        );

        $rowNumber = 1;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNumber++;

            $data = $this->combineRow($normalizedHeader, $row);

            if ($this->isRowEmpty($data)) {
                continue;
            }

            $this->processRow($rowNumber, $data);
        }

        fclose($handle);

        return $this->getSummary();
    }

    public function getSummary(): array
    {
        return [
            'success_count' => $this->successCount,
            'failed_count' => $this->failedCount,
            'created_count' => $this->createdCount,
            'updated_count' => $this->updatedCount,
            'failed_rows' => $this->failedRows,
        ];
    }

    private function processRow(int $rowNumber, array $data): void
    {
        $subjekLabel = trim((string) ($data['subjek'] ?? ''));
        $fileName = trim((string) ($data['nama_file'] ?? ''));
        $namaSop = trim((string) ($data['nama_sop'] ?? ''));
        $nomorSop = trim((string) ($data['nomor_sop'] ?? ''));

        if ($subjekLabel === '') {
            $this->markFailed($rowNumber, 'Kolom subjek kosong.');
            return;
        }

        if ($fileName === '') {
            $this->markFailed($rowNumber, 'Kolom nama_file kosong.');
            return;
        }

        if ($namaSop === '') {
            $this->markFailed($rowNumber, 'Kolom nama_sop kosong.');
            return;
        }

        if ($nomorSop === '') {
            $this->markFailed($rowNumber, 'Kolom nomor_sop kosong.');
            return;
        }

        $subjek = $this->resolveSubjek($subjekLabel);

        if (!$subjek) {
            $this->markFailed($rowNumber, 'Subjek "' . $subjekLabel . '" tidak ditemukan.');
            return;
        }

        $storedPath = $this->resolveStoredPdfPath($fileName);

        if (!$storedPath) {
            $this->markFailed($rowNumber, 'File PDF "' . $fileName . '" tidak ditemukan di uploads/sop.');
            return;
        }

        $sop = Sop::firstOrNew([
            'nomor_sop' => $nomorSop,
            'id_subjek' => $subjek->id_subjek,
            'revisi_ke' => 0,
        ]);

        $isNew = !$sop->exists;

        $sop->fill([
            'nama_sop' => $namaSop,
            'tahun' => $this->resolveYear($data['tahun'] ?? null),
            'link_sop' => $storedPath,
            'status' => 'aktif',
            'created_by' => $sop->created_by ?: $this->userId,
            'created_date' => $sop->created_date ?: now(),
            'modified_by' => $this->userId,
            'modified_date' => now(),
        ]);
        $sop->save();

        $this->successCount++;
        if ($isNew) {
            $this->createdCount++;
            return;
        }

        $this->updatedCount++;
    }

    private function detectDelimiter($handle): string
    {
        $firstLine = fgets($handle) ?: '';
        rewind($handle);

        $delimiters = [';', ',', "\t"];
        $selected = ',';
        $maxColumns = 0;

        foreach ($delimiters as $delimiter) {
            $columns = str_getcsv($firstLine, $delimiter);
            $count = is_array($columns) ? count($columns) : 0;

            if ($count > $maxColumns) {
                $maxColumns = $count;
                $selected = $delimiter;
            }
        }

        return $selected;
    }

    private function combineRow(array $header, array $row): array
    {
        $data = [];

        foreach ($header as $index => $key) {
            if ($key === '') {
                continue;
            }

            $value = $row[$index] ?? null;
            $data[$key] = is_string($value) ? trim($value) : $value;
        }

        return $data;
    }

    private function isRowEmpty(array $data): bool
    {
        return collect($data)
            ->filter(fn ($value) => trim((string) $value) !== '')
            ->isEmpty();
    }

    private function resolveStoredPdfPath(string $fileName): ?string
    {
        $normalizedName = $this->normalizeKey($fileName);
        $normalizedBaseName = $this->normalizeKey(pathinfo($fileName, PATHINFO_FILENAME));

        $uploadedFile = $this->pdfFiles[$normalizedName]
            ?? $this->pdfFiles[$normalizedBaseName]
            ?? null;

        if ($uploadedFile instanceof UploadedFile) {
            $targetPath = 'uploads/sop/' . $uploadedFile->getClientOriginalName();

            if (!Storage::disk('public')->exists($targetPath)) {
                $uploadedFile->storeAs('uploads/sop', $uploadedFile->getClientOriginalName(), 'public');
            }

            return $targetPath;
        }

        foreach ([
            'uploads/sop/' . $fileName,
            'sop/' . $fileName,
        ] as $candidatePath) {
            if (Storage::disk('public')->exists($candidatePath)) {
                return $candidatePath;
            }
        }

        return $this->storedPdfPaths[$normalizedName]
            ?? $this->storedPdfPaths[$normalizedBaseName]
            ?? null;
    }

    private function indexStoredPdfPaths(): void
    {
        foreach (['uploads/sop', 'sop'] as $directory) {
            foreach (Storage::disk('public')->files($directory) as $path) {
                $filename = pathinfo($path, PATHINFO_BASENAME);
                $baseName = pathinfo($path, PATHINFO_FILENAME);

                $this->storedPdfPaths[$this->normalizeKey($filename)] = $path;
                $this->storedPdfPaths[$this->normalizeKey($baseName)] = $path;
            }
        }
    }

    private function resolveSubjek(string $subjekLabel): ?Subjek
    {
        [$subjekName, $timkerjaName] = $this->splitSubjekLabel($subjekLabel);

        $candidates = Subjek::with('timkerja')
            ->whereRaw('LOWER(TRIM(nama_subjek)) = ?', [Str::lower($subjekName)])
            ->orderBy('id_subjek')
            ->get();

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

    private function normalizeKey(string $value): string
    {
        $normalized = Str::lower(trim($value));
        $normalized = str_replace('\\', '/', $normalized);

        return preg_replace('/\s+/', ' ', $normalized) ?? $normalized;
    }

    private function markFailed(int $rowNumber, string $reason): void
    {
        $this->failedCount++;
        $this->failedRows[] = [
            'row' => $rowNumber,
            'reason' => $reason,
        ];
    }

    private function failForMissingFile(string $path): array
    {
        $this->markFailed(0, 'File CSV tidak ditemukan: ' . $path);

        return $this->getSummary();
    }
}
