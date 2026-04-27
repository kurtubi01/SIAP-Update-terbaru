<?php

namespace App\Http\Controllers;

use App\Models\Sop;
use App\Models\Subjek;
use App\Models\Timkerja;
use App\Services\SopCsvImportService;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
class SopController extends Controller
{
    private const SOP_FILE_MAX_KB = 51200;
    private const EVALUASI_KRITERIA = [
        'Mampu mendorong peningkatan kinerja',
        'Mudah dipahami',
        'Mudah dilaksanakan',
        'Semua orang dapat menjalankan perannya masing-masing',
        'Mampu mengatasi permasalahan yang berkaitan dengan proses',
        'Mampu menjawab kebutuhan peningkatan kinerja organisasi',
    ];

    public function __construct(
        private UserActivityService $userActivityService
    ) {
    }

    private function routePrefix(): string
    {
        return strtolower((string) Auth::user()?->role ?: 'admin');
    }

    private function currentTeamId(): ?int
    {
        return Auth::user()?->id_timkerja;
    }

    private function isOperator(): bool
    {
        return $this->routePrefix() === 'operator';
    }

    private function ensureAdminAccess(): void
    {
        abort_unless($this->routePrefix() === 'admin', 403);
    }

    private function applyOperatorScope($query)
    {
        if (!$this->isOperator()) {
            return $query;
        }

        $teamId = $this->currentTeamId();

        if (!$teamId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('subjek', function ($subQuery) use ($teamId) {
            $subQuery->where('id_timkerja', $teamId);
        });
    }

    private function visibleSubjekIds(): array
    {
        $query = Subjek::query();

        if ($this->isOperator()) {
            $teamId = $this->currentTeamId();

            if (!$teamId) {
                return [];
            }

            $query->where('id_timkerja', $teamId);
        }

        return $query->pluck('id_subjek')->map(fn ($id) => (int) $id)->all();
    }

    private function visibleSubjekQuery()
    {
        $query = Subjek::query();

        if ($this->isOperator()) {
            $teamId = $this->currentTeamId();

            if (!$teamId) {
                return $query->whereRaw('1 = 0');
            }

            $query->where('id_timkerja', $teamId);
        }

        return $query;
    }

    private function visibleUnitsQuery()
    {
        $query = Timkerja::query()->orderBy('nama_timkerja');

        if ($this->isOperator()) {
            $teamId = $this->currentTeamId();

            if (!$teamId) {
                return $query->whereRaw('1 = 0');
            }

            $query->where('id_timkerja', $teamId);
        }

        return $query;
    }

    private function findVisibleSopOrFail(int $id): Sop
    {
        $query = Sop::with('subjek.timkerja')->where('id_sop', $id);
        $this->applyOperatorScope($query);

        return $query->firstOrFail();
    }

    /**
     * 1. TAMPILKAN DAFTAR SOP (INDEX)
     * Dimodifikasi agar default hanya menampilkan yang aktif.
     */
    public function index(Request $request)
    {
        $visibleSubjek = $this->visibleSubjekQuery()
            ->with('timkerja')
            ->orderBy('nama_subjek')
            ->get();

        $visibleUnits = $this->visibleUnitsQuery()->get();

        $selectedSubjekName = trim((string) $request->input('nama_subjek', ''));
        if ($selectedSubjekName === '' && $request->filled('id_subjek')) {
            $selectedSubjekName = (string) optional(
                $visibleSubjek->firstWhere('id_subjek', (int) $request->input('id_subjek'))
            )->nama_subjek;
        }

        $subjekFilterOptions = $visibleSubjek
            ->groupBy(fn (Subjek $item) => mb_strtolower(trim((string) $item->nama_subjek)))
            ->map(function ($items) {
                /** @var \Illuminate\Support\Collection<int, \App\Models\Subjek> $items */
                $first = $items->first();
                $teams = $items
                    ->filter(fn (Subjek $item) => $item->timkerja)
                    ->map(fn (Subjek $item) => [
                        'id_timkerja' => (int) $item->timkerja->id_timkerja,
                        'nama_timkerja' => $item->timkerja->nama_timkerja,
                    ])
                    ->unique('id_timkerja')
                    ->sortBy('nama_timkerja', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values();

                return [
                    'nama_subjek' => $first->nama_subjek,
                    'teams' => $teams->all(),
                ];
            })
            ->sortBy('nama_subjek', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        if ($selectedSubjekName !== '' && !$subjekFilterOptions->contains(fn ($item) => $item['nama_subjek'] === $selectedSubjekName)) {
            $selectedSubjekName = '';
        }

        $selectedSubjekOption = $selectedSubjekName !== ''
            ? $subjekFilterOptions->firstWhere('nama_subjek', $selectedSubjekName)
            : null;

        $filteredUnits = $selectedSubjekOption
            ? ($selectedSubjekOption['teams'] ?? [])
            : $visibleUnits
                ->map(fn (Timkerja $unit) => [
                    'id_timkerja' => (int) $unit->id_timkerja,
                    'nama_timkerja' => $unit->nama_timkerja,
                ])
                ->values()
                ->all();

        $filteredUnits = collect($filteredUnits)
            ->unique('id_timkerja')
            ->sortBy('nama_timkerja', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
        $selectedSubjekHasTeams = $selectedSubjekName === '' || $filteredUnits->isNotEmpty();

        $selectedUnitId = $request->filled('id_unit') ? (int) $request->input('id_unit') : null;
        if ($selectedUnitId && !$filteredUnits->contains(fn ($unit) => (int) $unit['id_timkerja'] === $selectedUnitId)) {
            $selectedUnitId = null;
        }

        $query = Sop::with(['subjek.timkerja'])
            ->with([
                'latestMonitoring.user.timkerja',
                'latestEvaluasi.user.timkerja',
            ])
            ->withCount([
                'monitorings',
                'evaluasis',
            ]);
        $this->applyOperatorScope($query);

        // Fitur Pencarian
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('nama_sop', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_sop', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan Subjek
        if ($selectedSubjekName !== '') {
            $query->whereHas('subjek', function ($q) use ($selectedSubjekName) {
                $q->where('nama_subjek', $selectedSubjekName);
            });
        }

        if ($selectedUnitId) {
            $query->whereHas('subjek', function ($q) use ($selectedUnitId) {
                $q->where('id_timkerja', $selectedUnitId);
            });
        }

        /**
         * LOGIKA TAMPILAN:
         * Jika sedang melihat riwayat (show_history), tampilkan semua versi untuk SOP tersebut.
         * Jika tidak, maka HANYA tampilkan yang aktif (status_active = 1).
         */
        if ($request->has('show_history') && $request->show_history != '') {
            $query->where('nama_sop', $request->show_history)
                  ->orderBy('revisi_ke', 'desc');
        } else {
            $query->where('status', 'aktif');
        }

        $allSop = $query->orderBy('id_sop', 'desc')->paginate(10);
        $selectedUnitLabel = 'Semua Tim Kerja';
        if ($selectedUnitId) {
            $selectedUnit = $filteredUnits->firstWhere('id_timkerja', $selectedUnitId);
            $selectedUnitLabel = $selectedUnit['nama_timkerja'] ?? 'Semua Tim Kerja';
        }
        $evaluasiCriteriaOptions = self::EVALUASI_KRITERIA;

        return view('pages.admin.sop.index', [
            'allSop' => $allSop,
            'subjek' => $visibleSubjek,
            'units' => $visibleUnits,
            'filterSubjekOptions' => $subjekFilterOptions,
            'filterUnits' => $filteredUnits,
            'selectedSubjekName' => $selectedSubjekName,
            'selectedSubjekHasTeams' => $selectedSubjekHasTeams,
            'selectedUnitId' => $selectedUnitId,
            'selectedUnitLabel' => $selectedUnitLabel,
            'evaluasiCriteriaOptions' => $evaluasiCriteriaOptions,
        ]);
    }

    public function aksesCepat()
    {
        $role = strtolower((string) Auth::user()?->role ?: 'admin');
        $teamId = Auth::user()?->id_timkerja;
        $teamScopedRole = $role === 'operator';

        $subjekQuery = Subjek::query()
            ->where('status', 'aktif')
            ->with('timkerja');

        if ($teamScopedRole) {
            if ($teamId) {
                $subjekQuery->where('id_timkerja', $teamId);
            } else {
                $subjekQuery->whereRaw('1 = 0');
            }
        }

        $subjek = $subjekQuery->get()
            ->groupBy(function (Subjek $item) {
                return mb_strtolower(trim((string) $item->nama_subjek));
            })
            ->map(function ($items) {
                /** @var \Illuminate\Support\Collection $items */
                $first = $items->first();

                return (object) [
                    'nama_subjek' => $first->nama_subjek,
                    'deskripsi' => $items->pluck('deskripsi')->filter()->first(),
                    'visible_sop_count' => Sop::whereIn('id_subjek', $items->pluck('id_subjek'))
                        ->where('status', 'aktif')
                        ->count(),
                ];
            })
            ->sortBy('nama_subjek', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $summary = [
            'total_subjek' => $subjek->count(),
            'total_sop' => $subjek->sum('visible_sop_count'),
        ];

        return view('pages.admin.sop.akses_cepat', compact('subjek', 'summary', 'role'));
    }

    public function history(int $id): JsonResponse
    {
        $sop = $this->findVisibleSopOrFail($id);

        $history = Sop::with('subjek.timkerja')
            ->where('nama_sop', $sop->nama_sop)
            ->orderBy('revisi_ke', 'desc')
            ->orderBy('id_sop', 'desc')
            ->get()
            ->map(function (Sop $item) {
                return [
                    'id_sop' => $item->id_sop,
                    'nama_sop' => $item->nama_sop,
                    'nomor_sop' => $item->nomor_sop,
                    'revisi_ke' => (int) $item->revisi_ke,
                    'revisi_label' => (int) $item->revisi_ke === 0 ? 'Versi Awal' : 'Revisi ke-' . $item->revisi_ke,
                    'status' => $item->status,
                    'status_label' => blank($item->status) ? '-' : ucfirst($item->status),
                    'tahun' => $item->tahun,
                    'subjek' => $item->subjek?->nama_subjek ?? 'Tanpa Subjek',
                    'timkerja' => $item->subjek?->timkerja?->nama_timkerja ?? 'Internal',
                    'keterangan' => $item->keterangan,
                    'view_url' => $item->link_sop ? route('view.pdf', basename($item->link_sop)) : null,
                ];
            })
            ->values();

        return response()->json([
            'latest' => $history->first(),
            'history' => $history,
        ]);
    }

    public function create()
    {
        $subjek = $this->visibleSubjekQuery()->get();
        $units = $this->visibleUnitsQuery()->get();
        return view('pages.admin.sop.create', compact('subjek', 'units'));
    }

    public function importForm()
    {
        $this->ensureAdminAccess();

        return view('pages.admin.sop.import');
    }

    public function importMassal(Request $request)
    {
        $this->ensureAdminAccess();

        $request->validate([
            'excel_file' => 'required|file|mimes:csv,txt',
            'pdf_files' => 'nullable|array',
            'pdf_files.*' => 'nullable|file|mimes:pdf|max:' . self::SOP_FILE_MAX_KB,
        ]);

        $import = new SopCsvImportService(
            $request->file('pdf_files', []),
            Auth::id()
        );

        $summary = $import->importFromPath($request->file('excel_file')->getRealPath());

        $this->userActivityService->log(
            $request->user(),
            'Import SOP massal',
            'Import SOP selesai dengan hasil ' . $summary['success_count'] . ' berhasil dan ' . $summary['failed_count'] . ' gagal.',
            $request
        );

        return redirect()
            ->back()
            ->with('success', 'Import SOP selesai. ' . $summary['success_count'] . ' data berhasil diproses, ' . $summary['failed_count'] . ' data gagal.')
            ->with('import_summary', $summary);
    }

    public function store(Request $request)
    {
        $visibleSubjekIds = $this->visibleSubjekIds();

        $request->validate([
            'nama_sop' => 'nullable|string|max:255',
            'nomor_sop' => 'nullable|string|max:100',
            'link_sop' => 'required|array|min:1',
            'link_sop.*' => 'required|file|mimes:pdf|max:' . self::SOP_FILE_MAX_KB,
            'files_metadata' => 'nullable|array',
            'files_metadata.*.nama_sop' => 'nullable|string|max:255',
            'files_metadata.*.nomor_sop' => 'nullable|string|max:100',
            'files_metadata.*.tahun' => 'nullable|numeric',
            'files_metadata.*.id_subjek' => ['nullable', Rule::in($visibleSubjekIds)],
            'id_subjek' => ['nullable', Rule::in($visibleSubjekIds)],
            'tahun' => 'nullable|numeric',
        ]);

        $uploadedFiles = $request->file('link_sop', []);
        $filesMetadata = $request->input('files_metadata', []);
        $createdSops = DB::transaction(function () use ($request, $uploadedFiles, $filesMetadata) {
            $records = collect();

            foreach ($uploadedFiles as $index => $uploadedFile) {
                if (!$uploadedFile instanceof UploadedFile) {
                    continue;
                }

                $parsedFileData = $this->extractSopDataFromFilename($uploadedFile->getClientOriginalName());
                $inputMetadata = $filesMetadata[$index] ?? [];
                $resolvedNamaSop = trim((string) ($inputMetadata['nama_sop'] ?? '')) ?: $parsedFileData['nama_sop'] ?: trim((string) $request->nama_sop);
                $resolvedNomorSop = trim((string) ($inputMetadata['nomor_sop'] ?? '')) ?: trim((string) $request->nomor_sop);
                $resolvedSubjekId = (int) ($inputMetadata['id_subjek'] ?? $request->id_subjek ?? 0);
                $resolvedYear = trim((string) ($inputMetadata['tahun'] ?? '')) ?: $parsedFileData['tahun'] ?: trim((string) $request->tahun);

                if ($resolvedNamaSop === '' || $resolvedNomorSop === '' || $resolvedSubjekId <= 0 || $resolvedYear === '') {
                    throw ValidationException::withMessages([
                        'link_sop' => 'Nama SOP, nomor SOP, subjek/tim kerja, dan tahun wajib dilengkapi untuk setiap file PDF.',
                    ]);
                }

                $path = $uploadedFile->store('uploads/sop', 'public');

                $records->push(Sop::create([
                    'nama_sop' => $resolvedNamaSop,
                    'nomor_sop' => $resolvedNomorSop,
                    'id_subjek' => $resolvedSubjekId,
                    'revisi_ke' => 0,
                    'link_sop' => $path,
                    'status' => 'aktif',
                    'tahun' => (int) $resolvedYear,
                    'created_date' => now(),
                    'created_by' => Auth::id(),
                ]));
            }

            return $records;
        });

        if ($createdSops->isEmpty()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['link_sop' => 'Dokumen SOP belum berhasil dibaca. Silakan pilih file PDF yang valid.']);
        }

        $this->userActivityService->log(
            $request->user(),
            'Tambah SOP',
            'Menambahkan ' . $createdSops->count() . ' SOP baru. SOP pertama: ' . $createdSops->first()->nama_sop . ' dengan nomor ' . $createdSops->first()->nomor_sop . '.',
            $request
        );

        return redirect()->route($this->routePrefix() . '.sop.index')->with('success', $createdSops->count() > 1
            ? 'Data SOP telah berhasil ditambahkan sebanyak ' . $createdSops->count() . ' dokumen.'
            : 'Data SOP telah berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $sop = $this->findVisibleSopOrFail((int) $id);
        $subjek = $this->visibleSubjekQuery()->get();
        $units = $this->visibleUnitsQuery()->get();
        return view('pages.admin.sop.edit', compact('sop', 'subjek', 'units'));
    }

    public function update(Request $request, $id)
    {
        $sop = $this->findVisibleSopOrFail((int) $id);

        $request->validate([
            'nama_sop'  => 'required|string|max:255',
            'nomor_sop' => 'required|string|max:100',
            'id_subjek' => ['required', Rule::in($this->visibleSubjekIds())],
            'tahun'     => 'required|numeric',
            'link_sop'  => 'nullable|mimes:pdf|max:' . self::SOP_FILE_MAX_KB,
        ]);

        if ($request->hasFile('link_sop')) {
            if ($sop->link_sop) {
                Storage::disk('public')->delete($sop->link_sop);
            }
            $path = $request->file('link_sop')->store('uploads/sop', 'public');
            $sop->link_sop = $path;
        }

        $sop->update([
            'nama_sop'      => $request->nama_sop,
            'nomor_sop'     => $request->nomor_sop,
            'id_subjek'     => $request->id_subjek,
            'status'        => $request->status ?? $sop->status,
            'tahun'         => $request->tahun,
            'modified_date' => now(),
            'modified_by'   => Auth::id(),
        ]);

        $this->userActivityService->log(
            $request->user(),
            'Ubah SOP',
            'Memperbarui SOP ' . $sop->nama_sop . ' dengan nomor ' . $sop->nomor_sop . '.',
            $request
        );

        return redirect()->route($this->routePrefix() . '.sop.index')->with('success', 'Perubahan data SOP telah berhasil diperbarui.');
    }

    /**
     * 7. PROSES SIMPAN REVISI (FIXED)
     */
    public function storeRevisi(Request $request)
    {
        $request->validate([
            'id_sop_induk'       => 'required|exists:tb_sop,id_sop',
            'link_sop'           => 'required|mimes:pdf|max:' . self::SOP_FILE_MAX_KB,
            'keterangan_revisi'  => 'required|string',
        ]);

        $sopInduk = $this->findVisibleSopOrFail((int) $request->id_sop_induk);

        if (!$sopInduk->monitorings()->exists()) {
            return redirect()
                ->route($this->routePrefix() . '.sop.index')
                ->with('error', 'SOP belum dimonitoring, jadi belum bisa direvisi.');
        }

        $logContext = DB::transaction(function () use ($request) {
            $sopInduk = $this->findVisibleSopOrFail((int) $request->id_sop_induk);

            $lastRevisi = Sop::where('nama_sop', $sopInduk->nama_sop)
                ->orderBy('revisi_ke', 'desc')
                ->first();

            $revisiBaru = $lastRevisi ? (int)$lastRevisi->revisi_ke + 1 : 1;

            $path = $request->file('link_sop')->store('uploads/sop', 'public');

            $newSop = Sop::create([
                'nama_sop'      => $sopInduk->nama_sop,
                'nomor_sop'     => $sopInduk->nomor_sop,
                'id_subjek'     => $sopInduk->id_subjek,
                'tahun'         => $sopInduk->tahun,
                'link_sop'      => $path,
                'revisi_ke'     => $revisiBaru,
                'status'        => 'aktif',
                'keterangan'    => $request->keterangan_revisi,
                'created_date'  => now(),
                'created_by'    => Auth::id(),
            ]);

            $this->normalizeRevisionStatuses($sopInduk->nama_sop);

            DB::table('tb_log_revisi')->insert([
                'id_sop'         => $newSop->id_sop,
                'tanggal_revisi' => now(),
                'revisi_ke'      => $revisiBaru,
                'keterangan'     => $request->keterangan_revisi,
                'created_by'     => Auth::id(),
                'created_at'     => now(),
            ]);

            return [
                'nama_sop' => $newSop->nama_sop,
                'revisi_ke' => $revisiBaru,
                'keterangan' => $request->keterangan_revisi,
            ];
        });

        $this->userActivityService->log(
            $request->user(),
            'Revisi SOP',
            'Menambahkan revisi ke-' . $logContext['revisi_ke'] . ' untuk SOP ' . $logContext['nama_sop'] . '. Detail: ' . $logContext['keterangan'],
            $request
        );

        return redirect()->route($this->routePrefix() . '.sop.index')
            ->with('success', 'Revisi SOP berhasil disimpan.');
    }

    private function normalizeRevisionStatuses(string $namaSop): void
    {
        $historyData = Sop::where('nama_sop', $namaSop)
            ->orderBy('revisi_ke', 'desc')
            ->orderBy('id_sop', 'desc')
            ->get();

        if ($historyData->isEmpty()) {
            return;
        }

        $latestRevision = (int) $historyData->first()->revisi_ke;
        $oldestRevisionToKeepVisible = max(1, $latestRevision - 5);

        foreach ($historyData as $index => $history) {
            $targetStatus = 'nonaktif';

            if ($index === 0) {
                $targetStatus = 'aktif';
            } elseif ($latestRevision > 6 && (int) $history->revisi_ke < $oldestRevisionToKeepVisible) {
                $targetStatus = null;
            }

            $history->update([
                'status' => $targetStatus,
                'modified_date' => now(),
                'modified_by' => Auth::id(),
            ]);
        }
    }

    private function extractSopDataFromFilename(string $filename): array
    {
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $normalized = preg_replace('/\s+/', ' ', str_replace(['_', '-'], ' ', $baseName));
        $normalized = trim((string) $normalized);

        $candidatePatterns = [
            '/^(No(?:mor)?\.?\s*[A-Za-z0-9\/\.\-]+)\s+(.*)$/i',
            '/^([A-Za-z0-9]+(?:[\/\.\-][A-Za-z0-9]+){1,})\s+(.*)$/',
            '/^(.*?)\s+\(([^)]+)\)$/',
        ];

        foreach ($candidatePatterns as $pattern) {
            if (!preg_match($pattern, $normalized, $matches)) {
                continue;
            }

            $first = trim((string) ($matches[1] ?? ''));
            $second = trim((string) ($matches[2] ?? ''));

            if ($first !== '' && $second !== '') {
                if (str_contains($pattern, '\\(([^)]+)\\)$')) {
                    return [
                        'nama_sop' => $first,
                        'nomor_sop' => $second,
                        'tahun' => $this->extractYearFromText($normalized),
                    ];
                }

                return [
                    'nama_sop' => $second,
                    'nomor_sop' => $first,
                    'tahun' => $this->extractYearFromText($normalized),
                ];
            }
        }

        return [
            'nama_sop' => $normalized,
            'nomor_sop' => '',
            'tahun' => $this->extractYearFromText($normalized),
        ];
    }

    private function extractYearFromText(string $value): string
    {
        preg_match_all('/\b(?:19|20)\d{2}\b/', $value, $matches);

        if (!empty($matches[0])) {
            return (string) end($matches[0]);
        }

        return now()->format('Y');
    }

    public function destroy(Request $request, $id)
    {
        $sop = $this->findVisibleSopOrFail((int) $id);
        $namaSop = $sop->nama_sop;
        $statusHapus = $sop->status;

        if ($sop->link_sop) {
            Storage::disk('public')->delete($sop->link_sop);
        }
        $sop->delete();

        $this->normalizeRevisionStatuses($namaSop);

        $this->userActivityService->log(
            $request->user(),
            'Hapus SOP',
            'Menghapus SOP ' . $namaSop . ' dengan status terakhir ' . ($statusHapus ?: '-') . '.',
            $request
        );

        return redirect()->route($this->routePrefix() . '.sop.index')->with('success', 'Data SOP telah berhasil dihapus.');
    }

    public function getUnits($id_subjek)
    {
        $subjek = Subjek::with('timkerja')->find($id_subjek);

        if (!$subjek || !$subjek->timkerja) {
            return response()->json([]);
        }

        return response()->json([[
            'id_unit' => $subjek->timkerja->id_timkerja,
            'nama_unit' => $subjek->timkerja->nama_timkerja,
        ]]);
    }

    /**
     * FUNGSI HAPUS SEMUA (BULK DELETE)
     * Menggunakan Redirect agar halaman refresh dan dashboard sinkron
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || count($ids) == 0) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        try {
            $sops = Sop::whereIn('id_sop', $ids)->get();
            $affectedNames = $sops->pluck('nama_sop')->unique()->filter()->values();
            $deletedCount = $sops->count();

            foreach ($sops as $sop) {
                if ($sop->link_sop) {
                    Storage::disk('public')->delete($sop->link_sop);
                }
                $sop->delete();
            }

            foreach ($affectedNames as $namaSop) {
                $this->normalizeRevisionStatuses($namaSop);
            }

            $this->userActivityService->log(
                $request->user(),
                'Hapus SOP massal',
                'Menghapus ' . $deletedCount . ' data SOP sekaligus.',
                $request
            );

            // Redirect kembali ke index agar angka dashboard & tabel terupdate
            return redirect()->route($this->routePrefix() . '.sop.index')->with('success', 'Data terpilih berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        return redirect()->route($this->routePrefix() . '.sop.index');
    }
}
