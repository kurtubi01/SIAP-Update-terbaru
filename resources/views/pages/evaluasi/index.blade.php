@extends('layouts.sidebarmenu')

@section('content')
@php($prefix = strtolower(Auth::user()->role ?? 'admin'))
@php($canManage = in_array($prefix, ['admin', 'operator'], true))

<style>
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

    .criteria-chip {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0.42rem 0.7rem;
        font-size: 0.75rem;
        font-weight: 700;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
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
</style>

<div class="container-fluid app-page-shell py-4">
    <div class="app-page-header">
        <div>
            <h1 class="app-page-title">Evaluasi SOP</h1>
            <p class="app-page-subtitle">Catat hasil evaluasi SOP aktif dengan pola tampilan yang konsisten, spasi yang lega, dan batas tabel yang jelas agar lebih nyaman dibaca.</p>
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route($prefix . '.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold">Evaluasi</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="app-table-card">
        <div class="app-table-toolbar">
            <div class="table-top-actions">
                <div class="soft-note">Layout evaluasi disamakan dengan dashboard, data SOP, dan monitoring agar tampilan menu terasa satu gaya.</div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="search-box">
                        <i class="bi bi-search text-muted me-2"></i>
                        <input type="text" id="searchEvaluasiTable" placeholder="Cari tanggal, SOP, petugas, kriteria, atau hasil...">
                    </div>
                    @if(!$canManage)
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Mode baca untuk viewer</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="app-table-wrap">
        <div class="table-responsive">
            <table class="table app-table-modern mb-0" id="evaluasiTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>SOP</th>
                        <th>Petugas</th>
                        <th>Kriteria</th>
                        <th>Hasil</th>
                        <th>Status</th>
                        <th class="text-center">{{ $canManage ? 'Aksi' : 'Mode' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluasis as $evaluasi)
                        @php($sopStatus = strtolower($evaluasi->sop->status ?? ''))
                        @php($sopRevisi = (int) ($evaluasi->sop->revisi_ke ?? 0))
                        @php($healthLabel = in_array($sopStatus, ['kadaluarsa', 'nonaktif'], true) ? 'Expired' : ($sopRevisi > 0 ? 'Review' : 'Aman'))
                        @php($healthClass = $healthLabel === 'Expired' ? 'badge-expired' : ($healthLabel === 'Review' ? 'badge-review' : 'badge-aman'))
                        <tr>
                            <td>
                                <span class="cell-title">{{ \Illuminate\Support\Carbon::parse($evaluasi->tanggal)->format('d M Y') }}</span>
                                <span class="cell-subtitle">{{ \Illuminate\Support\Carbon::parse($evaluasi->tanggal)->format('H:i') }} WIB</span>
                            </td>
                            <td>
                                <span class="cell-title">{{ $evaluasi->sop->nama_sop ?? '-' }}</span>
                                <span class="cell-subtitle">ID SOP: {{ $evaluasi->id_sop }}</span>
                            </td>
                            <td>{{ $evaluasi->user->nama ?? '-' }}</td>
                            <td style="min-width: 300px;">
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach(($evaluasi->kriteria_evaluasi ?? []) as $item)
                                        <span class="criteria-chip">{{ $item }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>{{ $evaluasi->hasil_evaluasi }}</td>
                            <td>
                                <span class="badge-matte {{ $healthClass }}">{{ $healthLabel }}</span>
                            </td>
                            <td class="text-center">
                                @if($canManage)
                                    <div class="action-tools">
                                        <a href="{{ route($prefix . '.evaluasi.show', $evaluasi->id_evaluasi) }}" class="icon-btn" title="Lihat evaluasi">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route($prefix . '.evaluasi.edit', $evaluasi->id_evaluasi) }}" class="icon-btn icon-edit" title="Edit evaluasi">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route($prefix . '.evaluasi.destroy', $evaluasi->id_evaluasi) }}" class="delete-evaluasi-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="icon-btn icon-danger btn-delete-evaluasi" title="Hapus evaluasi">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="badge-matte badge-readonly">Read Only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr id="evaluasiEmptyState">
                            <td colspan="7" class="text-center py-5 text-muted">Belum ada data evaluasi.</td>
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
        const evaluasiSearch = document.getElementById('searchEvaluasiTable');
        const evaluasiTableBody = document.querySelector('#evaluasiTable tbody');

        evaluasiSearch?.addEventListener('input', function () {
            const keyword = this.value.trim().toLowerCase();
            const emptyState = document.getElementById('evaluasiEmptyState');
            const rows = Array.from(evaluasiTableBody.querySelectorAll('tr')).filter((row) => row.id !== 'evaluasiEmptyState' && row.id !== 'evaluasiNoSearch');
            let visibleCount = 0;

            if (emptyState) {
                emptyState.style.display = keyword === '' ? '' : 'none';
            }

            rows.forEach((row) => {
                const match = row.innerText.toLowerCase().includes(keyword);
                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            document.getElementById('evaluasiNoSearch')?.remove();

            if (keyword !== '' && visibleCount === 0) {
                const row = document.createElement('tr');
                row.id = 'evaluasiNoSearch';
                row.innerHTML = '<td colspan="7" class="text-center py-5 text-muted">Pencarian evaluasi tidak ditemukan.</td>';
                evaluasiTableBody.appendChild(row);
            }
        });

        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 1200,
                timerProgressBar: true
            });
        @endif

        document.querySelectorAll('.btn-delete-evaluasi').forEach((button) => {
            button.addEventListener('click', function () {
                const form = this.closest('.delete-evaluasi-form');

                Swal.fire({
                    title: 'Hapus evaluasi?',
                    text: 'Data evaluasi akan dihapus permanen.',
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
@endsection
