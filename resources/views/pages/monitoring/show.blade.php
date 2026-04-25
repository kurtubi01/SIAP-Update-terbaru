@extends('layouts.sidebarmenu')

@section('content')
@php($prefix = strtolower(Auth::user()->role ?? 'admin'))

<style>
    .detail-card {
        border: 1px solid #dbe5f1;
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 22px 48px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .detail-head {
        padding: 1.4rem 1.6rem;
        border-bottom: 1px solid #e8eef6;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
        padding: 1.5rem;
    }

    .detail-item,
    .detail-wide {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #fcfdff;
        padding: 1rem 1.05rem;
    }

    .detail-wide {
        grid-column: 1 / -1;
    }

    .detail-label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.78rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .detail-value {
        color: #0f172a;
        line-height: 1.75;
        white-space: pre-line;
    }

    .badge-monitoring {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 0.85rem;
        border-radius: 999px;
        font-weight: 700;
        background: #eaf2ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
            padding: 1rem;
        }
    }
</style>

<div class="container-fluid app-page-shell py-4">
    <div class="app-page-header">
        <div>
            <h1 class="app-page-title">Detail Monitoring SOP</h1>
            <p class="app-page-subtitle">Ringkasan hasil monitoring ditampilkan lebih jelas agar mudah ditinjau sebelum edit atau hapus data.</p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route($prefix . '.monitoring.edit', $monitoring->id_monitoring) }}" class="btn btn-primary px-4 fw-bold rounded-4">
                <i class="bi bi-pencil-square me-2"></i>Edit
            </a>
            <a href="{{ route($prefix . '.monitoring.index') }}" class="btn btn-outline-secondary px-4 fw-bold rounded-4">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-head">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="fw-bold fs-5">{{ $monitoring->sop->nama_sop ?? '-' }}</div>
                    <div class="text-muted small">ID Monitoring #{{ $monitoring->id_monitoring }} | ID SOP {{ $monitoring->id_sop }}</div>
                </div>
                <span class="badge-monitoring">{{ $monitoring->kriteria_penilaian }}</span>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Tanggal</span>
                <div class="detail-value">{{ \Illuminate\Support\Carbon::parse($monitoring->tanggal)->translatedFormat('d F Y, H:i') }} WIB</div>
            </div>

            <div class="detail-item">
                <span class="detail-label">Petugas</span>
                <div class="detail-value">{{ $monitoring->user->nama ?? '-' }}</div>
            </div>

            <div class="detail-wide">
                <span class="detail-label">Prosedur</span>
                <div class="detail-value">{{ $monitoring->prosedur ?: '-' }}</div>
            </div>

            <div class="detail-wide">
                <span class="detail-label">Catatan Hasil</span>
                <div class="detail-value">{{ $monitoring->hasil_monitoring }}</div>
            </div>

            <div class="detail-wide">
                <span class="detail-label">Tindakan yang Harus Diambil</span>
                <div class="detail-value">{{ $monitoring->tindakan ?: '-' }}</div>
            </div>

            <div class="detail-wide">
                <span class="detail-label">Catatan Tambahan</span>
                <div class="detail-value">{{ $monitoring->catatan ?: '-' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
