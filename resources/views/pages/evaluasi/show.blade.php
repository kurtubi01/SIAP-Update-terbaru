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

    .criteria-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
    }

    .criteria-chip {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0.45rem 0.8rem;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-weight: 700;
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
            <h1 class="app-page-title">Detail Evaluasi SOP</h1>
            <p class="app-page-subtitle">Hasil evaluasi ditampilkan lengkap supaya mudah dilihat kembali sebelum diperbarui.</p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route($prefix . '.evaluasi.edit', $evaluasi->id_evaluasi) }}" class="btn btn-primary px-4 fw-bold rounded-4">
                <i class="bi bi-pencil-square me-2"></i>Edit
            </a>
            <a href="{{ route($prefix . '.evaluasi.index') }}" class="btn btn-outline-secondary px-4 fw-bold rounded-4">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-head">
            <div class="fw-bold fs-5">{{ $evaluasi->sop->nama_sop ?? '-' }}</div>
            <div class="text-muted small">ID Evaluasi #{{ $evaluasi->id_evaluasi }} | ID SOP {{ $evaluasi->id_sop }}</div>
        </div>

        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Tanggal</span>
                <div class="detail-value">{{ \Illuminate\Support\Carbon::parse($evaluasi->tanggal)->translatedFormat('d F Y, H:i') }} WIB</div>
            </div>

            <div class="detail-item">
                <span class="detail-label">Petugas</span>
                <div class="detail-value">{{ $evaluasi->user->nama ?? '-' }}</div>
            </div>

            <div class="detail-wide">
                <span class="detail-label">Kriteria Evaluasi</span>
                <div class="criteria-group">
                    @forelse(($evaluasi->kriteria_evaluasi ?? []) as $item)
                        <span class="criteria-chip">{{ $item }}</span>
                    @empty
                        <span class="detail-value">-</span>
                    @endforelse
                </div>
            </div>

            <div class="detail-wide">
                <span class="detail-label">Hasil Evaluasi</span>
                <div class="detail-value">{{ $evaluasi->hasil_evaluasi }}</div>
            </div>

            <div class="detail-wide">
                <span class="detail-label">Catatan</span>
                <div class="detail-value">{{ $evaluasi->catatan ?: '-' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
