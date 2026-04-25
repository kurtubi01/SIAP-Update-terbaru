<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use App\Models\Monitoring;
use App\Models\Sop;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EvaluasiController extends Controller
{
    public function __construct(
        private UserActivityService $userActivityService
    ) {
    }

    private const KRITERIA = [
        'Mampu mendorong peningkatan kinerja',
        'Mudah dipahami',
        'Mudah dilaksanakan',
        'Semua orang dapat menjalankan perannya masing-masing',
        'Mampu mengatasi permasalahan yang berkaitan dengan proses',
        'Mampu menjawab kebutuhan peningkatan kinerja organisasi',
    ];

    private function routePrefix(): string
    {
        return strtolower((string) Auth::user()?->role ?: 'admin');
    }

    private function currentTeamId(): ?int
    {
        return Auth::user()?->id_timkerja;
    }

    private function isScopedRole(): bool
    {
        return $this->routePrefix() === 'operator';
    }

    private function applyRoleScope($query)
    {
        if (!$this->isScopedRole()) {
            return $query;
        }

        $teamId = $this->currentTeamId();

        if (!$teamId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('sop.subjek', function ($subQuery) use ($teamId) {
            $subQuery->where('id_timkerja', $teamId);
        });
    }

    private function visibleSopQuery()
    {
        $query = Sop::query()->where('status', 'aktif')->orderBy('nama_sop');

        if (!$this->isScopedRole()) {
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

    private function visibleMonitoringSopQuery(?int $includeSopId = null)
    {
        $query = $this->visibleSopQuery();

        $query->where(function ($monitoringQuery) use ($includeSopId) {
            $monitoringQuery->whereHas('monitorings');

            if ($includeSopId) {
                $monitoringQuery->orWhere('id_sop', $includeSopId);
            }
        });

        return $query->orderBy('nama_sop');
    }

    private function visibleSopIds(): array
    {
        return $this->visibleMonitoringSopQuery()->pluck('id_sop')->map(fn ($id) => (int) $id)->all();
    }

    private function findVisibleEvaluasiOrFail(int $id): Evaluasi
    {
        $query = Evaluasi::with(['sop.subjek.timkerja', 'user.timkerja'])
            ->where('id_evaluasi', $id)
            ->whereHas('sop', function ($sopQuery) {
                $sopQuery->where('status', 'aktif');
            });
        $this->applyRoleScope($query);

        return $query->firstOrFail();
    }

    private function resolveRedirectRoute(Request $request): string
    {
        $allowedTargets = [
            'evaluasi.index',
            'sop.index',
        ];

        $requestedTarget = (string) $request->input('redirect_route', 'evaluasi.index');

        if (!in_array($requestedTarget, $allowedTargets, true)) {
            $requestedTarget = 'evaluasi.index';
        }

        return $this->routePrefix() . '.' . $requestedTarget;
    }

    public function index()
    {
        $evaluasis = Evaluasi::with(['sop', 'user'])
            ->whereHas('sop', function ($sopQuery) {
                $sopQuery->where('status', 'aktif');
            })
            ->orderBy('id_evaluasi', 'desc');

        $this->applyRoleScope($evaluasis);

        $evaluasis = $evaluasis
            ->get();

        return view('pages.evaluasi.index', [
            'evaluasis' => $evaluasis,
            'kriteriaOptions' => self::KRITERIA,
        ]);
    }

    public function create()
    {
        $sops = $this->visibleMonitoringSopQuery()->get();
        $selectedSopId = request()->integer('id_sop');
        $selectedSop = $selectedSopId
            ? $sops->firstWhere('id_sop', $selectedSopId)
            : null;

        return view('pages.evaluasi.create', [
            'sops' => $sops,
            'selectedSop' => $selectedSop,
            'kriteriaOptions' => self::KRITERIA,
            'evaluasi' => null,
            'pageMode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_sop' => ['required', Rule::in($this->visibleSopIds())],
            'kriteria_evaluasi' => 'required|array|min:1',
            'kriteria_evaluasi.*' => 'required|string|in:' . implode(',', self::KRITERIA),
            'hasil_evaluasi' => 'required|string',
            'catatan' => 'nullable|string',
        ], [
            'kriteria_evaluasi.required' => 'Pilih minimal satu kriteria evaluasi.',
            'kriteria_evaluasi.min' => 'Pilih minimal satu kriteria evaluasi.',
            'hasil_evaluasi.required' => 'Hasil evaluasi wajib diisi.',
        ]);

        $evaluasi = Evaluasi::create([
            'id_sop' => $request->id_sop,
            'id_user' => Auth::id(),
            'tanggal' => now(),
            'kriteria_evaluasi' => array_values($request->kriteria_evaluasi),
            'hasil_evaluasi' => $request->hasil_evaluasi,
            'catatan' => $request->catatan,
        ]);

        $this->userActivityService->log(
            $request->user(),
            'Tambah evaluasi',
            'Menambahkan evaluasi untuk SOP ID ' . $evaluasi->id_sop . '.',
            $request
        );

        $prefix = $this->routePrefix();

        return redirect()
            ->route($this->resolveRedirectRoute($request))
            ->with('success', 'Data evaluasi berhasil disimpan!');
    }

    public function destroy(Request $request, $id)
    {
        $evaluasi = $this->findVisibleEvaluasiOrFail((int) $id);
        $targetId = $evaluasi->id_sop;
        $evaluasi->delete();

        $this->userActivityService->log(
            $request->user(),
            'Hapus evaluasi',
            'Menghapus evaluasi untuk SOP ID ' . $targetId . '.',
            $request
        );

        return redirect()->back()->with('success', 'Data evaluasi berhasil dihapus!');
    }

    public function show($id)
    {
        $evaluasi = $this->findVisibleEvaluasiOrFail((int) $id);

        return view('pages.evaluasi.show', [
            'evaluasi' => $evaluasi,
        ]);
    }

    public function edit($id)
    {
        $evaluasi = $this->findVisibleEvaluasiOrFail((int) $id);
        $sops = $this->visibleMonitoringSopQuery($evaluasi->id_sop)->get();
        $selectedSop = $sops->firstWhere('id_sop', $evaluasi->id_sop);

        return view('pages.evaluasi.create', [
            'sops' => $sops,
            'selectedSop' => $selectedSop,
            'kriteriaOptions' => self::KRITERIA,
            'evaluasi' => $evaluasi,
            'pageMode' => 'edit',
        ]);
    }

    public function update(Request $request, $id)
    {
        $evaluasi = $this->findVisibleEvaluasiOrFail((int) $id);

        $request->validate([
            'id_sop' => ['required', Rule::in($this->visibleSopIds())],
            'kriteria_evaluasi' => 'required|array|min:1',
            'kriteria_evaluasi.*' => 'required|string|in:' . implode(',', self::KRITERIA),
            'hasil_evaluasi' => 'required|string',
            'catatan' => 'nullable|string',
        ], [
            'kriteria_evaluasi.required' => 'Pilih minimal satu kriteria evaluasi.',
            'kriteria_evaluasi.min' => 'Pilih minimal satu kriteria evaluasi.',
            'hasil_evaluasi.required' => 'Hasil evaluasi wajib diisi.',
        ]);

        $evaluasi->update([
            'id_sop' => $request->id_sop,
            'kriteria_evaluasi' => array_values($request->kriteria_evaluasi),
            'hasil_evaluasi' => $request->hasil_evaluasi,
            'catatan' => $request->catatan,
        ]);

        $this->userActivityService->log(
            $request->user(),
            'Ubah evaluasi',
            'Memperbarui evaluasi untuk SOP ID ' . $evaluasi->id_sop . '.',
            $request
        );

        return redirect()
            ->route($this->resolveRedirectRoute($request))
            ->with('success', 'Data evaluasi berhasil diperbarui!');
    }
}
