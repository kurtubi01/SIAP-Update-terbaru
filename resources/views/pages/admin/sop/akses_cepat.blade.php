@extends('layouts.sidebarmenu')

@section('content')
@php($prefix = strtolower(Auth::user()->role ?? 'admin'))

<style>
    .quick-simple-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
    }

    .quick-simple-list {
        border-top: 1px solid #e2e8f0;
    }

    .quick-simple-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 20px;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #eef2f7;
        transition: background-color 0.2s ease;
    }

    .quick-simple-item:last-child {
        border-bottom: none;
    }

    .quick-simple-item:hover {
        background: #f8fafc;
    }

    .quick-simple-name {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .quick-simple-meta {
        font-size: 0.88rem;
        color: #64748b;
    }

    .quick-simple-count {
        min-width: 78px;
        text-align: center;
        padding: 8px 12px;
        border-radius: 12px;
        background: #eff6ff;
        color: #0d47a1;
        font-weight: 700;
        font-size: 0.9rem;
    }
</style>

<div class="container-fluid app-page-shell py-4">
    <div class="app-page-header mb-4">
        <div>
            <h1 class="app-page-title">Akses Cepat SOP</h1>
            <p class="app-page-subtitle">Pilih subjek untuk membuka daftar SOP. Setiap baris hanya menampilkan satu subjek.</p>
        </div>
    </div>

    <div class="quick-simple-card">
        <div class="p-4 border-bottom">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-1 fw-bold text-dark">Daftar Subjek</h5>
                    <div class="text-muted small">{{ $summary['total_subjek'] ?? 0 }} subjek, {{ $summary['total_sop'] ?? 0 }} SOP aktif</div>
                </div>
            </div>
        </div>

        <div class="quick-simple-list">
            @forelse($subjek as $s)
                <a href="{{ route($prefix . '.sop.index', ['nama_subjek' => $s->nama_subjek]) }}" class="quick-simple-item">
                    <div>
                        <div class="quick-simple-name">{{ $s->nama_subjek }}</div>
                        <div class="quick-simple-meta">{{ $s->deskripsi ?: 'Klik untuk melihat daftar SOP pada subjek ini.' }}</div>
                    </div>
                    <div class="quick-simple-count">{{ $s->visible_sop_count ?? 0 }} SOP</div>
                </a>
            @empty
                <div class="p-4 text-center text-muted">
                    Belum ada subjek yang tersedia.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
