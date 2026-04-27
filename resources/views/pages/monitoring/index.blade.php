@extends('layouts.sidebarmenu')

@section('content')
@php($prefix = strtolower(Auth::user()->role ?? 'admin'))
@php($canManage = in_array($prefix, ['admin', 'operator'], true))

<style>
    .monitoring-summary {
        display: grid;
        gap: 0.2rem;
    }

    .badge-matte {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0.45rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .badge-matte.badge-aman {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }

    .badge-matte.badge-review {
        background: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
    }

    .badge-matte.badge-expired {
        background: #fee2e2;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .badge-matte.badge-readonly {
        background: #e2e8f0;
        color: #475569;
        border-color: #cbd5e1;
    }

    .action-tools {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        border: 1px solid #dbe4f0;
        background: #ffffff;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .icon-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 20px rgba(15, 23, 42, 0.08);
    }

    .icon-btn.icon-danger {
        color: #dc2626;
        border-color: #fecaca;
        background: #fff5f5;
    }

    .icon-btn.icon-edit {
        color: #1d4ed8;
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .cell-title {
        font-weight: 700;
        color: #0f172a;
    }

    .cell-subtitle {
        display: block;
        margin-top: 0.3rem;
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .cell-inline-stack {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .table-top-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .search-box {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 18px;
        transition: 0.3s;
        display: flex;
        align-items: center;
        min-width: 320px;
    }

    .search-box:focus-within {
        background: #ffffff;
        border-color: #bfdbfe;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08);
    }

    .search-box input {
        width: 100%;
        border: 0;
        background: transparent;
        outline: none;
        color: #0f172a;
    }

    .search-box input::placeholder {
        color: #64748b;
    }

    .monev-workspace {
        display: grid;
        gap: 1.25rem;
    }

    .monev-stat-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 1rem;
    }

    .monev-stat-card,
    .monev-guidance-card {
        border: 1px solid #dbe5f1;
        border-radius: 18px;
        background: #ffffff;
        padding: 1.1rem;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
    }

    .monev-stat-value {
        color: #0f172a;
        font-size: 1.65rem;
        font-weight: 800;
        line-height: 1;
    }

    .monev-stat-label {
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 700;
        margin-top: 0.5rem;
    }

    .guidance-grid {
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: 1rem;
    }

    .guidance-list {
        margin: 0;
        padding-left: 1.1rem;
        color: #475569;
        line-height: 1.8;
    }

    @media (max-width: 1200px) {
        .monev-stat-grid,
        .guidance-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 768px) {
        .monev-stat-grid,
        .guidance-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container-fluid app-page-shell py-4">
    <div class="app-page-header">
        <div>
            <h1 class="app-page-title">Monitoring SOP</h1>
            <p class="app-page-subtitle">Catat hasil monitoring untuk dokumen SOP aktif dengan tampilan yang konsisten, huruf yang lebih nyaman dibaca, dan garis tabel yang lebih jelas.</p>
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route($prefix . '.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold">Monitoring</li>
                </ol>
            </nav>
        </div>
    </div>

    @if($canManage)
        @php($stats = $workspaceStats ?? [])
        <div class="monev-workspace">
            <div class="monev-stat-grid">
                <div class="monev-stat-card">
                    <div class="monev-stat-value">{{ $stats['total_active'] ?? 0 }}</div>
                    <div class="monev-stat-label">SOP aktif</div>
                </div>
                <div class="monev-stat-card">
                    <div class="monev-stat-value">{{ $stats['waiting_monitoring'] ?? 0 }}</div>
                    <div class="monev-stat-label">Menunggu monitoring</div>
                </div>
                <div class="monev-stat-card">
                    <div class="monev-stat-value">{{ $stats['monitored'] ?? 0 }}</div>
                    <div class="monev-stat-label">Sudah dimonitoring</div>
                </div>
                <div class="monev-stat-card">
                    <div class="monev-stat-value">{{ $stats['waiting_evaluasi'] ?? 0 }}</div>
                    <div class="monev-stat-label">Perlu evaluasi</div>
                </div>
                <div class="monev-stat-card">
                    <div class="monev-stat-value">{{ $stats['ready_revision'] ?? 0 }}</div>
                    <div class="monev-stat-label">Siap revisi</div>
                </div>
            </div>

            <div class="guidance-grid">
                <div class="monev-guidance-card">
                    <h5 class="fw-bold mb-2">Halaman Monitoring Sebagai Panel Kerja</h5>
                    <p class="text-muted mb-3">Tabel monitoring di halaman ini dihilangkan agar admin dan operator fokus memulai monitoring dari Data SOP. Setiap catatan monitoring tetap tersimpan sebagai riwayat dan muncul dalam laporan viewer.</p>
                    <a href="{{ route($prefix . '.sop.index') }}" class="btn btn-primary fw-bold rounded-3">
                        <i class="bi bi-file-earmark-richtext me-2"></i>Buka Data SOP
                    </a>
                </div>

                <div class="monev-guidance-card">
                    <h5 class="fw-bold mb-2">Saran Isi Halaman</h5>
                    <ul class="guidance-list">
                        <li>Ringkasan SOP yang belum dimonitoring.</li>
                        <li>Daftar prioritas SOP aktif berdasarkan tahun dan revisi terakhir.</li>
                        <li>Shortcut ke Data SOP untuk membuat monitoring dari SOP terkait.</li>
                        <li>Indikator SOP yang sudah bisa lanjut evaluasi.</li>
                    </ul>
                </div>
            </div>
        </div>
    @else

    <div class="app-table-card">
        <div class="app-table-toolbar">
            <div class="table-top-actions">
                <div class="soft-note">Daftar ini menjadi history monitoring SOP. Catatan dari versi SOP lama tetap tampil setelah SOP direvisi, sedangkan edit dan hapus hanya tersedia untuk SOP aktif.</div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="search-box">
                        <i class="bi bi-search text-muted me-2"></i>
                        <input type="text" id="searchMonitoringTable" placeholder="Cari tanggal, SOP, petugas, kriteria, atau hasil...">
                    </div>
                    @if(!$canManage)
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Mode baca untuk viewer</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="app-table-wrap">
        <div class="table-responsive">
            <table class="table app-table-modern mb-0" id="monitoringTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>SOP</th>
                        <th>Tim Kerja</th>
                        <th>Kriteria</th>
                        <th>Hasil</th>
                        <th class="text-center">{{ $canManage ? 'Aksi' : 'Mode' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monitorings as $monitoring)
                        @php($sopStatus = strtolower($monitoring->sop->status ?? ''))
                        @php($sopRevisi = (int) ($monitoring->sop->revisi_ke ?? 0))
                        @php($healthLabel = in_array($sopStatus, ['kadaluarsa', 'nonaktif'], true) ? 'Expired' : ($sopRevisi > 0 ? 'Review' : 'Aman'))
                        @php($healthClass = $healthLabel === 'Expired' ? 'badge-expired' : ($healthLabel === 'Review' ? 'badge-review' : 'badge-aman'))
                        @php($canEditHistory = $canManage && ($monitoring->sop->status ?? null) === 'aktif')
                        <tr>
                            <td>
                                <span class="cell-title">{{ \Illuminate\Support\Carbon::parse($monitoring->tanggal)->format('d M Y') }}</span>
                                <span class="cell-subtitle">{{ \Illuminate\Support\Carbon::parse($monitoring->tanggal)->format('H:i') }} WIB</span>
                            </td>
                            <td>
                                <span class="cell-title">{{ $monitoring->sop->nama_sop ?? '-' }}</span>
                                <span class="cell-subtitle">ID SOP: {{ $monitoring->id_sop }}</span>
                            </td>
                            <td>
                                <div class="cell-inline-stack">
                                    <span class="cell-title">{{ $monitoring->user->nama ?? '-' }}</span>
                                    <span class="cell-subtitle">{{ $monitoring->user->timkerja->nama_timkerja ?? $monitoring->sop->subjek->timkerja->nama_timkerja ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="monitoring-summary">
                                    <span class="cell-title">{{ $monitoring->kriteria_penilaian }}</span>
                                    <span class="cell-subtitle">{{ \Illuminate\Support\Str::limit($monitoring->prosedur ?: 'Prosedur belum diisi', 60) }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="monitoring-summary">
                                    <span>{{ \Illuminate\Support\Str::limit($monitoring->hasil_monitoring, 70) }}</span>
                                    <span class="cell-subtitle">{{ \Illuminate\Support\Str::limit($monitoring->tindakan ?: 'Tindakan belum diisi', 60) }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($canManage)
                                    <div class="action-tools">
                                        <a href="{{ route($prefix . '.monitoring.show', $monitoring->id_monitoring) }}" class="icon-btn" title="Lihat monitoring">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($canEditHistory)
                                            <a href="{{ route($prefix . '.monitoring.edit', $monitoring->id_monitoring) }}" class="icon-btn icon-edit" title="Edit monitoring">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form method="POST" action="{{ route($prefix . '.monitoring.destroy', $monitoring->id_monitoring) }}" class="delete-monitoring-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="icon-btn icon-danger btn-delete-monitoring" title="Hapus monitoring">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge-matte badge-readonly">Riwayat</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="badge-matte badge-readonly">Read Only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr id="monitoringEmptyState">
                            <td colspan="6" class="text-center py-5 text-muted">Belum ada data monitoring.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const monitoringSearch = document.getElementById('searchMonitoringTable');
        const monitoringTableBody = document.querySelector('#monitoringTable tbody');

        monitoringSearch?.addEventListener('input', function () {
            const keyword = this.value.trim().toLowerCase();
            const emptyState = document.getElementById('monitoringEmptyState');
            const rows = Array.from(monitoringTableBody.querySelectorAll('tr')).filter((row) => row.id !== 'monitoringEmptyState' && row.id !== 'monitoringNoSearch');
            let visibleCount = 0;

            if (emptyState) {
                emptyState.style.display = keyword === '' ? '' : 'none';
            }

            rows.forEach((row) => {
                const match = row.innerText.toLowerCase().includes(keyword);
                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            document.getElementById('monitoringNoSearch')?.remove();

            if (keyword !== '' && visibleCount === 0) {
                const row = document.createElement('tr');
                row.id = 'monitoringNoSearch';
                row.innerHTML = '<td colspan="6" class="text-center py-5 text-muted">Pencarian monitoring tidak ditemukan.</td>';
                monitoringTableBody.appendChild(row);
            }
        });

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @json(session('success')),
                confirmButtonText: 'OK'
            });
        @endif

        document.querySelectorAll('.btn-delete-monitoring').forEach((button) => {
            button.addEventListener('click', function () {
                const form = this.closest('.delete-monitoring-form');

                Swal.fire({
                    title: 'Hapus monitoring?',
                    text: 'Data monitoring akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed && form) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
    @endif
@endsection
