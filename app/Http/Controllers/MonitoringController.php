<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\Sop;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MonitoringController extends Controller
{
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

    private function visibleSopIds(): array
    {
        return $this->visibleSopQuery()->pluck('id_sop')->map(fn ($id) => (int) $id)->all();
    }

    private function findVisibleMonitoringOrFail(int $id): Monitoring
    {
        $query = Monitoring::with(['sop.subjek.timkerja', 'user.timkerja'])
            ->where('id_monitoring', $id)
            ->whereHas('sop', function ($sopQuery) {
                $sopQuery->where('status', 'aktif');
            });
        $this->applyRoleScope($query);

        return $query->firstOrFail();
    }

    private function resolveRedirectRoute(Request $request): string
    {
        $allowedTargets = [
            'monitoring.index',
            'sop.index',
        ];

        $requestedTarget = (string) $request->input('redirect_route', 'monitoring.index');

        if (!in_array($requestedTarget, $allowedTargets, true)) {
            $requestedTarget = 'monitoring.index';
        }

        return $this->routePrefix() . '.' . $requestedTarget;
    }

    public function index()
    {
        $monitorings = Monitoring::with(['sop.subjek.timkerja', 'user.timkerja'])
            ->whereHas('sop', function ($sopQuery) {
                $sopQuery->where('status', 'aktif');
            })
            ->orderBy('id_monitoring', 'desc');

        
        $this->applyRoleScope($monitorings);

        $monitorings = $monitorings
            ->get();

        return view('pages.monitoring.index', compact('monitorings'));
    }

    public function create()
    {
        $sops = $this->visibleSopQuery()->get();
        $selectedSopId = request()->integer('id_sop');
        $selectedSop = $selectedSopId
            ? $sops->firstWhere('id_sop', $selectedSopId)
            : null;

        return view('pages.monitoring.create', [
            'sops' => $sops,
            'selectedSop' => $selectedSop,
            'monitoring' => null,
            'pageMode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_sop' => ['required', Rule::in($this->visibleSopIds())],
            'prosedur' => 'required|string',
            'kriteria_penilaian' => 'required|in:Berjalan dengan baik,Tidak berjalan dengan baik',
            'hasil_monitoring' => 'required|string',
            'tindakan' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        $monitoring = Monitoring::create([
            'id_sop' => $request->id_sop,
            'id_user' => Auth::id(),
            'tanggal' => now(),
            'prosedur' => $request->prosedur,
            'kriteria_penilaian' => $request->kriteria_penilaian,
            'hasil_monitoring' => $request->hasil_monitoring,
            'tindakan' => $request->tindakan,
            'catatan' => $request->catatan,
        ]);

        $this->userActivityService->log(
            $request->user(),
            'Tambah monitoring',
            'Menambahkan monitoring untuk SOP ID ' . $monitoring->id_sop . '.',
            $request
        );

        $prefix = $this->routePrefix();

        return redirect()->route($this->resolveRedirectRoute($request))->with('success', 'Data Monitoring berhasil disimpan!');
    }

    public function destroy(Request $request, $id)
    {
        $monitoring = $this->findVisibleMonitoringOrFail((int) $id);
        $targetId = $monitoring->id_sop;
        $monitoring->delete();

        $this->userActivityService->log(
            $request->user(),
            'Hapus monitoring',
            'Menghapus monitoring untuk SOP ID ' . $targetId . '.',
            $request
        );

        return redirect()->back()->with('success', 'Data Monitoring berhasil dihapus!');
    }

    public function show($id)
    {
        $monitoring = $this->findVisibleMonitoringOrFail((int) $id);

        return view('pages.monitoring.show', compact('monitoring'));
    }

    public function edit($id)
    {
        $monitoring = $this->findVisibleMonitoringOrFail((int) $id);
        $sops = $this->visibleSopQuery()->get();
        $selectedSop = $sops->firstWhere('id_sop', $monitoring->id_sop);

        return view('pages.monitoring.create', [
            'sops' => $sops,
            'selectedSop' => $selectedSop,
            'monitoring' => $monitoring,
            'pageMode' => 'edit',
        ]);
    }

    public function update(Request $request, $id)
    {
        $monitoring = $this->findVisibleMonitoringOrFail((int) $id);

        $request->validate([
            'id_sop' => ['required', Rule::in($this->visibleSopIds())],
            'prosedur' => 'required|string',
            'kriteria_penilaian' => 'required|in:Berjalan dengan baik,Tidak berjalan dengan baik',
            'hasil_monitoring' => 'required|string',
            'tindakan' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        $monitoring->update([
            'id_sop' => $request->id_sop,
            'prosedur' => $request->prosedur,
            'kriteria_penilaian' => $request->kriteria_penilaian,
            'hasil_monitoring' => $request->hasil_monitoring,
            'tindakan' => $request->tindakan,
            'catatan' => $request->catatan,
        ]);

        $this->userActivityService->log(
            $request->user(),
            'Ubah monitoring',
            'Memperbarui monitoring untuk SOP ID ' . $monitoring->id_sop . '.',
            $request
        );

        return redirect()
            ->route($this->resolveRedirectRoute($request))
            ->with('success', 'Data monitoring berhasil diperbarui!');
    }
}
