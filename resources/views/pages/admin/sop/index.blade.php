@extends('layouts.sidebarmenu')

@section('content')
@php($prefix = strtolower(Auth::user()->role ?? 'admin'))
@php($canManage = in_array($prefix, ['admin', 'operator'], true))
@php($canBulkDelete = $prefix === 'admin')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
    :root {
        --sop-primary: #0d47a1;
        --sop-border: #e2e8f0;
        --sop-text-soft: #64748b;
        --sop-table-head-bg: #eaf2fb;
        --sop-table-head-border: #d4e2f1;
        --sop-table-row-border: #e4edf7;
        --sop-table-row-alt: #f9fbfe;
        --sop-table-text: #1e293b;
        --sop-action-gap: 18px;
    }

    .nama-sop-link {
        color: #0f172a;
        text-decoration: none;
        transition: 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        padding: 10px 14px;
        border-radius: 14px;
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
        border: 1px solid #dbeafe;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.8);
    }
    .nama-sop-link:hover {
        color: #0d47a1;
        transform: translateY(-1px);
        border-color: #93c5fd;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.12);
    }
    .nama-sop-link .click-indicator {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: #dbeafe;
        color: #1d4ed8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .nama-sop-link[type="button"] {
        width: 100%;
        text-align: left;
    }

    /* Card & Table Styling */
    .page-intro {
        margin-bottom: 1.4rem;
    }
    .page-intro .title-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .page-intro-title {
        font-size: 1.9rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0.2rem;
    }
    .page-intro .breadcrumb {
        margin-bottom: 0;
    }
    .main-card {
        border: 1px solid #dce6f2;
        border-radius: 26px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }
    .table-container { padding: 0 1.2rem 1.2rem 1.2rem; }
    .custom-table {
        border-collapse: separate;
        border-spacing: 0 10px;
        min-width: 1040px;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    .table-shell {
        border-radius: 22px;
        background: linear-gradient(180deg, #f8fbff 0%, #fdfefe 100%);
        border: 1px solid #dfe9f3;
        padding: 12px 12px 6px;
    }
    .custom-table thead th {
        background: var(--sop-table-head-bg);
        border-top: 1px solid var(--sop-table-head-border);
        border-bottom: 1px solid var(--sop-table-head-border);
        color: #274160;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
        padding: 1rem 1rem 0.95rem;
        white-space: nowrap;
    }
    .custom-table thead th:first-child {
        border-left: 1px solid var(--sop-table-head-border);
        border-radius: 16px 0 0 16px;
    }
    .custom-table thead th:last-child {
        border-right: 1px solid var(--sop-table-head-border);
        border-radius: 0 16px 16px 0;
    }
    .custom-table tbody tr {
        background: #ffffff;
        transition: all 0.25s ease;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.045);
    }
    .custom-table tbody tr:nth-child(even) {
        background: var(--sop-table-row-alt);
    }
    .custom-table tbody tr:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 26px rgba(15, 23, 42, 0.08);
    }
    .custom-table tbody td {
        padding: 1.05rem 0.95rem;
        border-top: 1px solid var(--sop-table-row-border);
        border-bottom: 1px solid var(--sop-table-row-border);
        vertical-align: middle;
        color: var(--sop-table-text);
        font-size: 0.97rem;
        line-height: 1.55;
        font-weight: 500;
    }
    .custom-table tbody tr td:first-child {
        border-radius: 12px 0 0 12px;
        border-left: 1px solid var(--sop-table-row-border);
    }
    .custom-table tbody tr td:last-child {
        border-radius: 0 12px 12px 0;
        border-right: 1px solid var(--sop-table-row-border);
    }

    /* Button Actions */
    .btn-action {
        width: 38px; height: 38px; display: inline-flex; align-items: center;
        justify-content: center; border-radius: 10px; transition: 0.2s;
        border: 1px solid #e2e8f0; background: #fff; text-decoration: none;
        cursor: pointer;
    }
    .btn-action:hover { background: #0d47a1; color: #fff !important; border-color: #0d47a1; }
    .btn-action.btn-revisi:hover { background: #f59e0b; border-color: #f59e0b; }
    .btn-action.btn-revisi-disabled {
        color: #94a3b8;
        border-color: #e2e8f0;
        background: #f8fafc;
    }
    .btn-action.btn-revisi-disabled:hover {
        background: #f8fafc;
        color: #94a3b8 !important;
        border-color: #e2e8f0;
    }

    /* Search & Badges */
    .search-box {
        background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 10px 20px; transition: 0.3s;
    }
    .search-box:focus-within {
        background: #ffffff;
        border-color: #bfdbfe;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08);
    }
    .badge-subjek {
        background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 0.7rem;
        padding: 5px 12px; border-radius: 8px; text-transform: uppercase;
        display: inline-block;
    }
    .table-toolbar {
        background: #ffffff;
        border-bottom: 1px solid #e7eef7;
    }
    .table-heading-note {
        color: var(--sop-text-soft);
        font-size: 0.86rem;
    }
    .row-number-pill {
        min-width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: var(--sop-primary);
        font-weight: 800;
    }
    .tahun-label {
        color: var(--sop-text-soft);
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 6px;
    }
    .number-badge,
    .revision-badge,
    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        font-weight: 700;
        white-space: nowrap;
    }
    .number-badge {
        background: #eef4ff;
        color: #2563eb;
        padding: 0.52rem 0.9rem;
    }
    .revision-badge {
        background: #f8fafc;
        color: #334155;
        border: 1px solid var(--sop-border);
        padding: 0.52rem 0.9rem;
    }
    .status-badge {
        padding: 0.52rem 0.95rem;
    }
    .tim-label {
        color: #334155;
        font-size: 0.96rem;
        font-weight: 700;
    }
    .target-nama .fw-bold,
    .target-subjek,
    .target-nomor,
    .target-tim {
        color: var(--sop-table-text);
    }
    .search-box input::placeholder {
        color: #64748b;
    }
    .empty-table-state {
        padding: 3rem 1rem;
        text-align: center;
    }
    .empty-table-state i {
        font-size: 2rem;
        color: #94a3b8;
        margin-bottom: 12px;
    }
    .sop-meta-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
    }
    .sop-meta-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 999px;
        font-size: 0.95rem;
        border: 1px solid transparent;
    }
    .sop-meta-icon.monitoring {
        background: #eefbf3;
        color: #15803d;
        border-color: #bbf7d0;
    }
    .sop-meta-icon.evaluasi {
        background: #fff7ed;
        color: #c2410c;
        border-color: #fed7aa;
    }

    .monev-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 0.55rem 0.95rem;
        font-weight: 800;
        border: 1px solid transparent;
        background: #f8fafc;
        color: #475569;
    }
    .monev-pill.is-done {
        background: #eefbf3;
        color: #15803d;
        border-color: #bbf7d0;
    }
    .monev-pill.is-empty {
        background: #fff5f5;
        color: #b91c1c;
        border-color: #fecaca;
    }
    .action-combo-btn {
        min-width: 132px;
        border-radius: 14px;
        padding: 0.72rem 1rem;
        border: 1px solid #d5e6ff;
        background: linear-gradient(135deg, #eef5ff 0%, #ffffff 100%);
        color: #1d4ed8;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 8px 18px rgba(29, 78, 216, 0.08);
    }
    .action-combo-btn:hover {
        background: #1d4ed8;
        color: #fff;
        border-color: #1d4ed8;
    }

    /* MODER PAGINATION STYLING */
    .pagination-wrapper {
        margin-top: 2rem;
        padding: 1rem 0 0;
        border-top: 1px solid #f1f5f9;
    }
    .pagination {
        gap: 8px;
        margin-bottom: 0;
    }
    .page-item .page-link {
        border: none;
        border-radius: 10px !important;
        padding: 10px 16px;
        color: #64748b;
        font-weight: 600;
        transition: 0.3s;
        background: #f8fafc;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .page-item.active .page-link {
        background: #0d47a1 !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(13, 71, 161, 0.25);
    }
    .page-item.disabled .page-link {
        background: #f8fafc;
        color: #cbd5e1;
    }

    /* New Styling for Locked SOP Row */
    .locked-sop-row {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    /* FIX BACKDROP FREEZE */
    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }

    /* Checkbox Styling */
    .form-check-input-custom {
        width: 1.2rem;
        height: 1.2rem;
        cursor: pointer;
    }
    .history-panel {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 18px;
        background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
    }
    .history-stat {
        border: 1px solid #dbeafe;
        border-radius: 16px;
        padding: 16px;
        background: #ffffff;
    }
    .filter-dropdown {
        position: relative;
    }
    .filter-dropdown-toggle {
        width: 100%;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 10px 14px;
        background: #fff;
        min-height: 46px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #334155;
    }
    .filter-dropdown-toggle [data-filter-label] {
        flex: 1;
        min-width: 0;
        text-align: left;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .filter-dropdown-toggle:disabled {
        background: #f8fafc;
        color: #94a3b8;
        cursor: not-allowed;
    }
    .filter-dropdown-menu {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.14);
        padding: 10px;
        z-index: 1065;
    }
    .filter-dropdown.open .filter-dropdown-menu {
        display: block;
    }
    .filter-dropdown-search {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 9px 12px;
        margin-bottom: 10px;
    }
    .filter-dropdown-list {
        max-height: 220px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .filter-dropdown-item {
        border: 0;
        background: transparent;
        text-align: left;
        border-radius: 10px;
        padding: 10px 12px;
        color: #334155;
    }
    .filter-dropdown-item.active {
        background: #5b8def;
        color: #fff;
    }
    .filter-dropdown-item:hover:not(.active),
    .filter-dropdown-item:focus-visible:not(.active) {
        background: #eef4ff;
        color: #1d4ed8;
    }
    .filter-dropdown-empty {
        padding: 10px 12px;
        color: #64748b;
        font-size: 0.9rem;
    }
    .sop-action-summary {
        border: 1px solid #dbeafe;
        border-radius: 18px;
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
        padding: 1rem 1.1rem;
        margin-bottom: 1rem;
    }
    .sop-action-summary-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        flex-wrap: wrap;
    }
    .sop-action-meta-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 0.85rem;
    }
    .sop-action-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 0.52rem 0.85rem;
        font-size: 0.82rem;
        font-weight: 800;
        white-space: nowrap;
        background: #ffffff;
        border: 1px solid #dbeafe;
    }
    .sop-action-meta-chip.number {
        color: #1d4ed8;
    }
    .sop-action-meta-chip.revision {
        color: #0f172a;
        border-color: #dbe5f1;
        background: #f8fbff;
    }
    .sop-action-kicker {
        color: #2563eb;
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 6px;
    }
    .sop-action-tabs .nav-link {
        border-radius: 14px;
        font-weight: 800;
        color: #475569;
        border: 1px solid #dbe5f1;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.75);
    }
    .sop-action-tabs .nav-link.active {
        background: #0d47a1;
        color: #fff;
        border-color: #0d47a1;
        box-shadow: 0 12px 24px rgba(13, 71, 161, 0.18);
    }
    .sop-action-panel {
        border: 1px solid #dce7f3;
        border-radius: 20px;
        padding: 1.25rem;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.75);
    }
    .sop-form-shell {
        background: #ffffff;
        border: 1px solid #e5edf6;
        border-radius: 18px;
        padding: 1rem;
    }
    .sop-form-shell + .sop-form-shell {
        margin-top: 1rem;
    }
    .sop-form-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 0.95rem;
        color: #0f172a;
        font-size: 0.98rem;
        font-weight: 800;
    }
    .sop-form-section-title .icon {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: #1d4ed8;
    }
    #modalSopAction .form-label {
        color: #334155;
        font-size: 0.83rem;
        font-weight: 800;
        margin-bottom: 0.45rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    #modalSopAction .form-control,
    #modalSopAction .form-select {
        min-height: 46px;
        border-radius: 12px;
        border: 1px solid #cfdbe8;
        background: #ffffff;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.02);
    }
    #modalSopAction .form-control:focus,
    #modalSopAction .form-select:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.12);
    }
    #modalSopAction textarea.form-control {
        min-height: 108px;
        resize: vertical;
    }
    #modalSopAction .form-control[readonly] {
        background: #f8fafc;
        color: #334155;
        font-weight: 600;
    }
    .sop-panel-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 1.15rem;
        padding-top: 1rem;
        border-top: 1px solid #e7eef7;
    }
    .btn-sop-soft {
        border-radius: 12px;
        padding: 0.78rem 1.1rem;
        font-weight: 800;
        border: 1px solid #d7e2ef;
        background: #f8fafc;
        color: #334155;
    }
    .btn-sop-soft:hover {
        background: #eef2f7;
        color: #0f172a;
    }
    .btn-sop-primary,
    .btn-sop-warning {
        border-radius: 12px;
        padding: 0.78rem 1.15rem;
        font-weight: 800;
        border: 0;
        color: #fff;
    }
    .btn-sop-primary {
        background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.16);
    }
    .btn-sop-warning {
        background: linear-gradient(180deg, #f59e0b 0%, #ea580c 100%);
        box-shadow: 0 12px 24px rgba(245, 158, 11, 0.18);
    }
    .sop-file-drop {
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        padding: 1rem;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
    }
    .sop-file-drop .form-control {
        background: #ffffff;
    }
    #modalSopAction .modal-dialog {
        width: calc(100vw - var(--sidebar-width) - (var(--sop-action-gap) * 2));
        max-width: calc(100vw - var(--sidebar-width) - (var(--sop-action-gap) * 2));
        min-height: calc(100vh - (var(--sop-action-gap) * 2));
        margin: var(--sop-action-gap) var(--sop-action-gap) var(--sop-action-gap) auto;
    }
    #modalSopAction .modal-content {
        min-height: calc(100vh - (var(--sop-action-gap) * 2));
        border: 1px solid #dce7f3;
        border-radius: 26px !important;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        overflow: hidden;
    }
    #modalSopAction .modal-body {
        overflow-y: auto;
    }
    .modal-backdrop.sop-sidebar-backdrop {
        left: var(--sidebar-width);
        width: calc(100vw - var(--sidebar-width));
    }
    #sidebar.minimized + #content #modalSopAction .modal-dialog {
        width: calc(100vw - (var(--sop-action-gap) * 2));
        max-width: calc(100vw - (var(--sop-action-gap) * 2));
    }
    body:has(#sidebar.minimized) .modal-backdrop.sop-sidebar-backdrop {
        left: 0;
        width: 100vw;
    }
    #modalSopAction .sop-action-tabs {
        flex-wrap: nowrap;
        width: 100%;
    }
    #modalSopAction .sop-action-tabs .nav-item {
        flex: 1 1 0;
    }
    #modalSopAction .sop-action-tabs .nav-link {
        width: 100%;
        text-align: center;
        padding: 0.8rem 1rem;
    }
    .form-note-soft {
        color: #64748b;
        font-size: 0.88rem;
        line-height: 1.6;
        padding: 0.85rem 1rem;
        border-radius: 14px;
        background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
        border: 1px solid #e2e8f0;
    }

    @media (max-width: 991.98px) {
        #modalSopAction .modal-dialog {
            max-width: calc(100vw - 2rem);
            width: calc(100vw - 2rem);
            margin: 1rem auto;
            min-height: auto;
        }
        #modalSopAction .modal-content {
            min-height: auto;
            border-radius: 20px !important;
        }
        .table-container {
            padding-inline: 1rem;
        }
        .sop-panel-actions {
            flex-direction: column-reverse;
        }
        .btn-sop-soft,
        .btn-sop-primary,
        .btn-sop-warning {
            width: 100%;
        }

        .custom-table {
            min-width: 960px;
        }

        .table-toolbar {
            padding: 18px !important;
        }

        .search-box input {
            width: 220px !important;
        }
    }

    @media (max-width: 576px) {
        #modalSopAction .sop-action-tabs {
            flex-wrap: wrap;
        }
        #modalSopAction .sop-action-tabs .nav-item {
            flex: 1 1 100%;
        }
        .main-card {
            border-radius: 20px;
        }

        .table-toolbar {
            gap: 12px;
        }

        .table-heading-note {
            padding-inline: 18px !important;
            font-size: 0.8rem;
            line-height: 1.5;
        }

        .search-box {
            width: 100%;
            padding-inline: 14px;
        }

        .search-box input {
            width: 100% !important;
            min-width: 0;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="page-intro">
        <div class="title-row">
            <div>
                <div class="page-intro-title">Repository SOP</div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route($prefix . '.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary fw-bold">Data SOP</li>
                    </ol>
                </nav>
            </div>

            @if($canManage)
                <a href="{{ route($prefix . '.sop.create') }}" class="btn btn-primary px-4 py-2 fw-bold shadow-sm" style="border-radius: 12px; background: #0d47a1; border: none;">
                    <i class="bi bi-plus-lg me-2"></i>Tambah SOP Baru
                </a>
            @endif
        </div>
    </div>

    <div class="card main-card">
        <div class="table-toolbar p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="search-box d-flex align-items-center">
                    <i class="bi bi-search text-muted me-2"></i>
                    <input type="text" id="searchTable" class="border-0 bg-transparent shadow-none" placeholder="Cari subjek, nama, nomor, atau tim..." style="outline: none; width: 300px;">
                </div>

                {{-- TOMBOL HAPUS MASAL (Muncul Otomatis via JS) --}}
                @if($canBulkDelete)
                    <button type="button" id="btnBulkDelete" class="btn btn-danger px-3 py-2 fw-bold shadow-sm animate__animated animate__fadeIn" style="display: none; border-radius: 12px;">
                        <i class="bi bi-trash3 me-2"></i> Hapus Terpilih (<span id="checkCount">0</span>)
                    </button>
                @endif
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary border-0 fw-bold small d-flex align-items-center" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalFilter">
                    <i class="bi bi-filter me-2"></i> Filter
                </button>
                <a href="{{ route($prefix . '.sop.index') }}" class="btn btn-outline-danger border-0 fw-bold small" style="border-radius: 10px;">
                    <i class="bi bi-arrow-clockwise me-2"></i> Reset
                </a>
            </div>
        </div>

        <div class="px-4 pb-3 table-heading-note d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>Daftar SOP aktif</span>
            <span>Pagination aktif tiap 10 data.</span>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                {{-- Form untuk Bulk Delete --}}
                <form id="formBulkDelete" action="{{ $canBulkDelete ? route('admin.sop.bulkDelete') : '#' }}" method="POST">
                    @csrf
                    @if($canBulkDelete)
                        @method('DELETE')
                    @endif
                    <div class="table-shell">
                    <table class="table custom-table" id="sopTable">
                        <thead>
                            <tr>
                                @if($canBulkDelete)
                                    <th style="width: 40px;">
                                        <input type="checkbox" class="form-check-input form-check-input-custom" id="selectAll">
                                    </th>
                                @endif
                                <th>No</th>
                                <th>Nama Subjek</th>
                                <th>Nama SOP</th>
                                <th>Nomor SOP</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Monitoring</th>
                                <th class="text-center">Evaluasi</th>
                                <th class="text-center">Revisi</th>
                                <th>Tim Kerja</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allSop as $index => $item)
                            @php($latestMonitoring = $item->latestMonitoring)
                            @php($latestEvaluasi = $item->latestEvaluasi)
                            <tr>
                                @if($canBulkDelete)
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $item->id_sop }}" class="form-check-input form-check-input-custom sop-checkbox">
                                    </td>
                                @endif
                                <td><span class="row-number-pill">{{ $allSop->firstItem() + $index }}</span></td>
                                <td class="target-subjek">
                                    @php($subjekItem = $item->subjek instanceof \Illuminate\Support\Collection ? $item->subjek->first() : $item->subjek)
                                    <span class="badge-subjek">{{ $subjekItem->nama_subjek ?? 'Tanpa Subjek' }}</span>
                                    <div class="tahun-label">Tahun: {{ date('Y', strtotime($item->tahun)) }}</div>
                                </td>
                                <td class="target-nama">
                                    <div class="fw-bold">
                                        <button type="button"
                                           class="nama-sop-link btn-sop-history border-0"
                                           data-history-url="{{ route($prefix . '.sop.history', $item->id_sop) }}"
                                           title="Lihat rincian SOP">
                                            <span>{{ $item->nama_sop }}</span>
                                            <span class="click-indicator">
                                                <i class="bi bi-arrow-up-right"></i>
                                            </span>
                                        </button>
                                    </div>
                                    @if($item->monitorings_count > 0 || $item->evaluasis_count > 0)
                                        <div class="sop-meta-tags">
                                            @if($item->monitorings_count > 0)
                                                <span class="sop-meta-icon monitoring"
                                                      data-bs-toggle="tooltip"
                                                      data-bs-placement="top"
                                                      title="SOP ini sudah di monitoring">
                                                    <i class="bi bi-graph-up-arrow"></i>
                                                </span>
                                            @endif
                                            @if($item->evaluasis_count > 0)
                                                <span class="sop-meta-icon evaluasi"
                                                      data-bs-toggle="tooltip"
                                                      data-bs-placement="top"
                                                      title="SOP ini sudah di evaluasi">
                                                    <i class="bi bi-ui-checks-grid"></i>
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="target-nomor"><span class="number-badge">{{ $item->nomor_sop }}</span></td>

                                <td class="text-center">
                                    @if($item->status === 'aktif')
                                        <span class="badge bg-success status-badge">Aktif</span>
                                    @elseif($item->status === 'kadaluarsa')
                                        <span class="badge bg-danger status-badge">Kadaluarsa</span>
                                    @elseif(blank($item->status))
                                        <span class="badge bg-light text-dark border status-badge">-</span>
                                    @else
                                        <span class="badge bg-secondary status-badge">Nonaktif</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($canManage && $item->status === 'aktif')
                                        <button type="button"
                                                class="monev-pill {{ $item->monitorings_count > 0 ? 'is-done' : 'is-empty' }} btn-open-sop-action border-0"
                                                data-id="{{ $item->id_sop }}"
                                                data-nama="{{ $item->nama_sop }}"
                                                data-nomor="{{ $item->nomor_sop }}"
                                                data-revisi="{{ $item->revisi_ke }}"
                                                data-monitored="{{ $item->monitorings_count > 0 ? '1' : '0' }}"
                                                data-monitoring-id="{{ $latestMonitoring?->id_monitoring }}"
                                                data-monitoring-prosedur="{{ $latestMonitoring?->prosedur }}"
                                                data-monitoring-kriteria="{{ $latestMonitoring?->kriteria_penilaian }}"
                                                data-monitoring-hasil="{{ $latestMonitoring?->hasil_monitoring }}"
                                                data-monitoring-tindakan="{{ $latestMonitoring?->tindakan }}"
                                                data-monitoring-catatan="{{ $latestMonitoring?->catatan }}"
                                                data-evaluasi-id="{{ $latestEvaluasi?->id_evaluasi }}"
                                                data-evaluasi-hasil="{{ $latestEvaluasi?->hasil_evaluasi }}"
                                                data-evaluasi-catatan="{{ $latestEvaluasi?->catatan }}"
                                                data-evaluasi-kriteria='@json($latestEvaluasi?->kriteria_evaluasi ?? [])'
                                                title="Buka form monitoring SOP">
                                            <i class="bi {{ $item->monitorings_count > 0 ? 'bi-check2-circle' : 'bi-x-circle' }}"></i>
                                            <span>{{ $item->monitorings_count > 0 ? 'Sudah' : 'Belum' }}</span>
                                        </button>
                                    @elseif($item->monitorings_count > 0)
                                        <span class="monev-pill is-done" title="Sudah dimonitoring">
                                            <i class="bi bi-check2-circle"></i>
                                            <span>Sudah</span>
                                        </span>
                                    @else
                                        <span class="monev-pill is-empty" title="Belum dimonitoring">
                                            <i class="bi bi-x-circle"></i>
                                            <span>Belum</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($latestEvaluasi)
                                        <span class="monev-pill is-done" title="Sudah dievaluasi">
                                            <i class="bi bi-check2-circle"></i>
                                            <span>Sudah</span>
                                        </span>
                                    @else
                                        <span class="monev-pill is-empty" title="Belum dievaluasi">
                                            <i class="bi bi-x-circle"></i>
                                            <span>Belum</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="revision-badge">
                                        {{ (int) $item->revisi_ke === 0 ? 'Versi Awal' : 'Revisi ke-' . $item->revisi_ke }}
                                    </div>
                                </td>

                                <td class="target-tim"><span class="tim-label">{{ $subjekItem->timkerja->nama_timkerja ?? 'Internal' }}</span></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        @if($canManage && $item->status === 'aktif')
                                            <button type="button" class="action-combo-btn btn-open-sop-action"
                                                    data-id="{{ $item->id_sop }}"
                                                    data-nama="{{ $item->nama_sop }}"
                                                    data-nomor="{{ $item->nomor_sop }}"
                                                    data-revisi="{{ $item->revisi_ke }}"
                                                    data-monitored="{{ $item->monitorings_count > 0 ? '1' : '0' }}"
                                                    data-monitoring-id="{{ $latestMonitoring?->id_monitoring }}"
                                                    data-monitoring-prosedur="{{ $latestMonitoring?->prosedur }}"
                                                    data-monitoring-kriteria="{{ $latestMonitoring?->kriteria_penilaian }}"
                                                    data-monitoring-hasil="{{ $latestMonitoring?->hasil_monitoring }}"
                                                    data-monitoring-tindakan="{{ $latestMonitoring?->tindakan }}"
                                                    data-monitoring-catatan="{{ $latestMonitoring?->catatan }}"
                                                    data-evaluasi-id="{{ $latestEvaluasi?->id_evaluasi }}"
                                                    data-evaluasi-hasil="{{ $latestEvaluasi?->hasil_evaluasi }}"
                                                    data-evaluasi-catatan="{{ $latestEvaluasi?->catatan }}"
                                                    data-evaluasi-kriteria='@json($latestEvaluasi?->kriteria_evaluasi ?? [])'
                                                title="Kelola SOP">
                                                <i class="bi bi-ui-radios-grid"></i>
                                                <span>Revisi SOP</span>
                                            </button>
                                        @endif
                                        <a href="{{ route('view.pdf', basename($item->link_sop)) }}"
                                           target="_blank"
                                           class="btn-action text-danger border-danger-subtle"
                                           title="Buka PDF"
                                           style="background-color: #fff5f5;">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        </a>
                                        @if($canManage)
                                            <a href="{{ route($prefix . '.sop.edit', $item->id_sop) }}" class="btn-action text-warning" title="Edit Data"><i class="bi bi-pencil-square"></i></a>
                                        @endif
                                        @if($canBulkDelete)
                                            <button type="button" class="btn-action text-danger btn-delete-single" data-id="{{ $item->id_sop }}" title="Hapus"><i class="bi bi-trash3"></i></button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="noData">
                                <td colspan="{{ $canBulkDelete ? 11 : 10 }}">
                                    <div class="empty-table-state">
                                        <i class="bi bi-folder2-open"></i>
                                        <h6 class="text-muted mb-1">Data SOP belum tersedia</h6>
                                        <div class="small text-secondary">Coba ubah filter atau tambahkan SOP baru.</div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </form>
            </div>
            <div class="pagination-wrapper d-flex justify-content-between align-items-center">
                <div class="text-muted small fw-bold">Menampilkan {{ $allSop->count() }} data pada halaman ini</div>
                <div>{{ $allSop->appends(request()->input())->links('pagination::bootstrap-5') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL FILTER --}}
<div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Filter Data SOP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route($prefix . '.sop.index') }}" method="GET">
                <div class="modal-body px-4 py-4">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark">Berdasarkan Subjek</label>
                        <div class="filter-dropdown" data-filter-dropdown data-filter-type="subjek">
                            <input type="hidden" name="nama_subjek" value="{{ $selectedSubjekName }}">
                            <button type="button" class="filter-dropdown-toggle" data-filter-toggle>
                                <span data-filter-label>{{ $selectedSubjekName ?: 'Semua Subjek' }}</span>
                                <i class="bi bi-caret-down-fill"></i>
                            </button>
                            <div class="filter-dropdown-menu">
                                <input type="text" class="filter-dropdown-search" placeholder="Cari subjek..." data-filter-search>
                                <div class="filter-dropdown-list" data-filter-list>
                                    <button type="button" class="filter-dropdown-item {{ $selectedSubjekName === '' ? 'active' : '' }}" data-value="" data-label="Semua Subjek" data-teams='@json([])'>Semua Subjek</button>
                                    @foreach($filterSubjekOptions as $s)
                                        <button type="button"
                                                class="filter-dropdown-item {{ $selectedSubjekName === $s['nama_subjek'] ? 'active' : '' }}"
                                                data-value="{{ $s['nama_subjek'] }}"
                                                data-label="{{ $s['nama_subjek'] }}"
                                                data-teams='@json($s['teams'])'>{{ $s['nama_subjek'] }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Berdasarkan Tim Kerja</label>
                        <div class="filter-dropdown" data-filter-dropdown data-filter-type="timkerja">
                            <input type="hidden" name="id_unit" value="{{ $selectedUnitId }}">
                            <button type="button" class="filter-dropdown-toggle" data-filter-toggle {{ !$selectedSubjekHasTeams ? 'disabled' : '' }}>
                                <span data-filter-label>{{ !$selectedSubjekHasTeams ? 'Tidak ada tim kerja' : $selectedUnitLabel }}</span>
                                <i class="bi bi-caret-down-fill"></i>
                            </button>
                            <div class="filter-dropdown-menu">
                                <input type="text" class="filter-dropdown-search" placeholder="Cari tim kerja..." data-filter-search>
                                <div class="filter-dropdown-list" data-filter-list>
                                    @if(!$selectedSubjekHasTeams)
                                        <div class="filter-dropdown-empty">Tidak ada tim kerja untuk subjek ini.</div>
                                    @else
                                        <button type="button" class="filter-dropdown-item {{ !$selectedUnitId ? 'active' : '' }}" data-value="" data-label="Semua Tim Kerja">Semua Tim Kerja</button>
                                        @foreach($filterUnits as $u)
                                            <button type="button" class="filter-dropdown-item {{ $selectedUnitId == $u['id_timkerja'] ? 'active' : '' }}" data-value="{{ $u['id_timkerja'] }}" data-label="{{ $u['nama_timkerja'] }}">{{ $u['nama_timkerja'] }}</button>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 gap-2">
                    <button type="button" class="btn btn-light fw-bold py-2 flex-grow-1" style="border-radius: 12px;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold py-2 flex-grow-1" style="background: #0d47a1; border-radius: 12px;">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL RINCIAN SOP --}}
<div class="modal fade" id="modalSopHistory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 pt-4 px-4">
                <div>
                    <h5 class="fw-bold mb-1">Rincian SOP</h5>
                    <div class="text-muted small">SOP terbaru dan riwayat revisi terdahulu</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="history-panel mb-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                        <div>
                            <div class="text-muted small mb-1">SOP Aktif Terbaru</div>
                            <h4 class="fw-bold mb-2" id="historyLatestName">-</h4>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary-subtle text-primary px-3 py-2" id="historyLatestNumber">-</span>
                                <span class="badge bg-success px-3 py-2" id="historyLatestStatus">Aktif</span>
                                <span class="badge bg-light text-dark border px-3 py-2" id="historyLatestRevision">-</span>
                            </div>
                        </div>

                        <a href="#" target="_blank" id="historyLatestView" class="btn btn-danger fw-bold px-4 py-2">
                            <i class="bi bi-file-earmark-pdf-fill me-2"></i>Lihat SOP
                        </a>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="history-stat">
                            <div class="text-muted small">Subjek</div>
                            <div class="fw-bold mt-1" id="historyLatestSubjek">-</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="history-stat">
                            <div class="text-muted small">Tim Kerja</div>
                            <div class="fw-bold mt-1" id="historyLatestTimkerja">-</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="history-stat">
                            <div class="text-muted small">Tahun</div>
                            <div class="fw-bold mt-1" id="historyLatestYear">-</div>
                        </div>
                    </div>
                </div>

                <div class="history-panel">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                        <div>
                            <div class="fw-bold">Revisi Terdahulu</div>
                            <div class="text-muted small">Pilih revisi lama yang ingin dilihat</div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <select id="historyRevisionSelect" class="form-select" style="min-width: 240px;">
                                <option value="">Tidak ada revisi terdahulu</option>
                            </select>
                            <a href="#" target="_blank" id="historyRevisionView" class="btn btn-outline-primary fw-bold disabled" aria-disabled="true">
                                <i class="bi bi-eye-fill me-2"></i>Lihat
                            </a>
                        </div>
                    </div>
                    <div class="text-muted small" id="historyRevisionDescription">Pilih salah satu revisi terdahulu dari dropdown untuk melihat file SOP sebelumnya.</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($canManage)
    <div class="modal fade" id="modalSopAction" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <div>
                        <h5 class="fw-bold mb-1">Aksi SOP</h5>
                        <div class="text-muted small">Form monitoring, evaluasi, dan revisi dirapikan dalam satu panel kerja.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <div class="sop-action-summary">
                        <div class="sop-action-summary-head">
                            <div>
                                <div class="sop-action-kicker">Panel Dokumen SOP</div>
                                <div class="fw-bold fs-5" id="sopActionName">-</div>
                                <div class="sop-action-meta-row">
                                    <span class="sop-action-meta-chip number">
                                        <i class="bi bi-hash"></i>
                                        <span id="sopActionNumber">-</span>
                                    </span>
                                    <span class="sop-action-meta-chip revision">
                                        <i class="bi bi-arrow-repeat"></i>
                                        <span id="sopActionRevision">Versi Awal</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-pills sop-action-tabs mb-3 gap-2" id="sopActionTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="monitoring-tab" data-bs-toggle="pill" data-bs-target="#monitoring-pane" type="button" role="tab">Monitoring</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="evaluasi-tab" data-bs-toggle="pill" data-bs-target="#evaluasi-pane" type="button" role="tab">Evaluasi</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="revisi-tab" data-bs-toggle="pill" data-bs-target="#revisi-pane" type="button" role="tab">Revisi</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="monitoring-pane" role="tabpanel">
                            <div class="sop-action-panel">
                                <form method="POST" id="sopMonitoringForm">
                                    @csrf
                                    <input type="hidden" name="_method" id="sopMonitoringMethod" value="POST">
                                    <input type="hidden" name="id_sop" id="sopMonitoringSopId">
                                    <input type="hidden" name="redirect_route" value="sop.index">

                                    <div class="sop-form-shell">
                                        <div class="sop-form-section-title">
                                            <span class="icon"><i class="bi bi-clipboard2-pulse"></i></span>
                                            <span>Informasi Monitoring</span>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Status Monitoring</label>
                                                <input type="text" id="sopMonitoringState" class="form-control" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Penilaian</label>
                                                <select name="kriteria_penilaian" id="sopMonitoringKriteria" class="form-select" required>
                                                    <option value="">Pilih penilaian</option>
                                                    <option value="Berjalan dengan baik">Berjalan dengan baik</option>
                                                    <option value="Tidak berjalan dengan baik">Tidak berjalan dengan baik</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Prosedur</label>
                                                <textarea name="prosedur" id="sopMonitoringProsedur" class="form-control" rows="3" required></textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Hasil Monitoring</label>
                                                <textarea name="hasil_monitoring" id="sopMonitoringHasil" class="form-control" rows="3" required></textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Tindakan</label>
                                                <textarea name="tindakan" id="sopMonitoringTindakan" class="form-control" rows="3" required></textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Catatan Tambahan</label>
                                                <textarea name="catatan" id="sopMonitoringCatatan" class="form-control" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-note-soft mt-3">Jika SOP sudah pernah dimonitoring, form ini otomatis menampilkan data monitoring terakhir agar bisa langsung diedit tanpa memilih SOP lagi.</div>

                                    <div class="sop-panel-actions">
                                        <button type="button" class="btn btn-sop-soft" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-sop-primary" id="sopMonitoringSubmit">Simpan Monitoring</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="evaluasi-pane" role="tabpanel">
                            <div class="sop-action-panel">
                                <form method="POST" id="sopEvaluasiForm">
                                    @csrf
                                    <input type="hidden" name="_method" id="sopEvaluasiMethod" value="POST">
                                    <input type="hidden" name="id_sop" id="sopEvaluasiSopId">
                                    <input type="hidden" name="redirect_route" value="sop.index">

                                    <div class="sop-form-shell">
                                        <div class="sop-form-section-title">
                                            <span class="icon"><i class="bi bi-ui-checks-grid"></i></span>
                                            <span>Informasi Evaluasi</span>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Status Evaluasi</label>
                                                <input type="text" id="sopEvaluasiState" class="form-control" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Ringkasan SOP</label>
                                                <input type="text" id="sopEvaluasiTarget" class="form-control" readonly>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Kriteria Evaluasi</label>
                                                <div class="row g-2" id="sopEvaluasiCriteriaList">
                                                    @foreach($evaluasiCriteriaOptions as $index => $kriteria)
                                                        <div class="col-md-6">
                                                            <label class="d-flex align-items-start gap-2 border rounded-4 p-3 h-100 bg-white">
                                                                <input type="checkbox"
                                                                       class="form-check-input mt-1 sop-evaluasi-criteria"
                                                                       name="kriteria_evaluasi[]"
                                                                       value="{{ $kriteria }}">
                                                                <span>{{ $kriteria }}</span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="form-note-soft mt-2">Evaluasi hanya tersedia untuk SOP yang sudah pernah dimonitoring.</div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Hasil Evaluasi</label>
                                                <textarea name="hasil_evaluasi" id="sopEvaluasiHasil" class="form-control" rows="3" required></textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Catatan</label>
                                                <textarea name="catatan" id="sopEvaluasiCatatan" class="form-control" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="sop-panel-actions">
                                        <button type="button" class="btn btn-sop-soft" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-sop-primary" id="sopEvaluasiSubmit">Simpan Evaluasi</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="revisi-pane" role="tabpanel">
                            <div class="sop-action-panel">
                                <form action="{{ route($prefix . '.sop.revisi') }}" method="POST" enctype="multipart/form-data" id="sopRevisionForm">
                                    @csrf
                                    <input type="hidden" name="id_sop_induk" id="sopRevisionId">

                                    <div class="sop-form-shell">
                                        <div class="sop-form-section-title">
                                            <span class="icon"><i class="bi bi-pencil-square"></i></span>
                                            <span>Form Revisi SOP</span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">SOP yang Akan Direvisi</label>
                                            <input type="text" id="sopRevisionName" class="form-control bg-light fw-bold" readonly>
                                            <div id="sopRevisionInfo" class="text-muted mt-1 small"></div>
                                        </div>
                                        <div class="mb-3 sop-file-drop">
                                            <label class="form-label">Upload File PDF Versi Baru</label>
                                            <input type="file" name="link_sop" class="form-control" accept=".pdf" required>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label">Keterangan Revisi</label>
                                            <textarea name="keterangan_revisi" class="form-control" rows="3" placeholder="Jelaskan ringkasan perubahan..." required></textarea>
                                        </div>
                                    </div>

                                    <div class="form-note-soft mt-3">Revisi hanya bisa disimpan jika SOP sudah pernah dimonitoring. Validasi ini tetap dijaga di backend.</div>

                                    <div class="sop-panel-actions">
                                        <button type="button" class="btn btn-sop-soft" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-sop-warning">Simpan Revisi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        const totalColumnCount = {{ $canBulkDelete ? 11 : 10 }};
        const allTimOptions = @json(collect($units)->map(fn ($unit) => ['id_timkerja' => (int) $unit->id_timkerja, 'nama_timkerja' => $unit->nama_timkerja])->values()->all());
        const subjekDropdown = $('[data-filter-type="subjek"]');
        const timDropdown = $('[data-filter-type="timkerja"]');

        function renderTeamItems(teamOptions, selectedValue = '') {
            const list = timDropdown.find('[data-filter-list]');
            const toggle = timDropdown.find('[data-filter-toggle]');
            const normalizedSelected = String(selectedValue ?? '');
            const options = Array.isArray(teamOptions) ? teamOptions : [];

            if (options.length === 0 && String(subjekDropdown.find('input[type="hidden"]').val() || '') !== '') {
                list.html('<div class="filter-dropdown-empty">Tidak ada tim kerja untuk subjek ini.</div>');
                toggle.prop('disabled', true);
                return;
            }

            toggle.prop('disabled', false);
            let html = `<button type="button" class="filter-dropdown-item ${normalizedSelected === '' ? 'active' : ''}" data-value="" data-label="Semua Tim Kerja">Semua Tim Kerja</button>`;

            options.forEach((team) => {
                const value = String(team.id_timkerja ?? '');
                const label = String(team.nama_timkerja ?? '');
                const activeClass = normalizedSelected === value ? 'active' : '';
                html += `<button type="button" class="filter-dropdown-item ${activeClass}" data-value="${value}" data-label="${label}">${label}</button>`;
            });

            list.html(html);
        }

        function getDropdownItemLabel(item, fallbackLabel = '') {
            const attrLabel = String(item.attr('data-label') || '').trim();
            const textLabel = String(item.text() || '').trim();
            return attrLabel || textLabel || fallbackLabel;
        }

        function getTeamsFromSubjekItem(subjekItem) {
            if (!subjekItem || !subjekItem.length) {
                return null;
            }

            const rawTeams = subjekItem.attr('data-teams');
            if (!rawTeams) {
                return null;
            }

            try {
                const parsedTeams = JSON.parse(rawTeams);
                return Array.isArray(parsedTeams) ? parsedTeams : null;
            } catch (error) {
                return null;
            }
        }

        function findSelectedSubjekItem(selectedSubjek) {
            return subjekDropdown.find('.filter-dropdown-item').filter(function() {
                return String($(this).data('value') || '') === String(selectedSubjek || '');
            }).first();
        }

        function syncTimDropdown(selectedSubjek, preferredTeamValue = null, subjekItem = null) {
            const hiddenInput = timDropdown.find('input[type="hidden"]');
            const label = timDropdown.find('[data-filter-label]');
            const search = timDropdown.find('[data-filter-search]');
            const selectedSubjekItem = subjekItem && subjekItem.length ? subjekItem : findSelectedSubjekItem(selectedSubjek);
            const teamsFromSelectedSubjek = getTeamsFromSubjekItem(selectedSubjekItem);
            const availableTeams = selectedSubjek ? (teamsFromSelectedSubjek || []) : allTimOptions;
            const preferredValue = preferredTeamValue !== null ? String(preferredTeamValue) : String(hiddenInput.val() || '');
            const matchedTeam = availableTeams.find((team) => String(team.id_timkerja) === preferredValue);
            const nextValue = matchedTeam ? String(matchedTeam.id_timkerja) : '';

            renderTeamItems(availableTeams, nextValue);
            hiddenInput.val(nextValue);
            if (selectedSubjek && availableTeams.length === 0) {
                label.text('Tidak ada tim kerja');
            } else {
                label.text(matchedTeam ? matchedTeam.nama_timkerja : 'Semua Tim Kerja');
            }
            search.val('');
        }

        function bindDropdown() {
            const dropdown = $(this);
            const toggle = dropdown.find('[data-filter-toggle]');
            const search = dropdown.find('[data-filter-search]');
            const hiddenInput = dropdown.find('input[type="hidden"]');
            const label = dropdown.find('[data-filter-label]');
            const list = dropdown.find('[data-filter-list]');

            toggle.on('click', function() {
                if (toggle.is(':disabled')) {
                    return;
                }

                $('[data-filter-dropdown]').not(dropdown).removeClass('open');
                dropdown.toggleClass('open');
                if (dropdown.hasClass('open')) {
                    search.trigger('focus');
                }
            });

            search.on('input', function() {
                const keyword = ($(this).val() || '').trim().toLowerCase();
                let visibleCount = 0;
                const items = dropdown.find('.filter-dropdown-item');

                items.each(function() {
                    const item = $(this);
                    const text = String(item.data('label') || '').toLowerCase();
                    const matched = text.includes(keyword);
                    item.toggle(matched);
                    if (matched) {
                        visibleCount++;
                    }
                });

                list.find('.filter-dropdown-empty').remove();
                if (visibleCount === 0) {
                    list.append('<div class="filter-dropdown-empty">Data tidak ditemukan</div>');
                }
            });

            list.on('click', '.filter-dropdown-item', function() {
                const item = $(this);
                const itemLabel = getDropdownItemLabel(item, dropdown.data('filter-type') === 'subjek' ? 'Semua Subjek' : 'Semua Tim Kerja');
                hiddenInput.val(item.data('value'));
                label.text(itemLabel);
                dropdown.find('.filter-dropdown-item').removeClass('active');
                item.addClass('active');
                dropdown.removeClass('open');
                search.val('');
                dropdown.find('.filter-dropdown-item').show();
                list.find('.filter-dropdown-empty').remove();

                if (dropdown.data('filter-type') === 'subjek') {
                    syncTimDropdown(String(item.data('value') || ''), '', item);
                }
            });
        }

        $('[data-filter-dropdown]').each(bindDropdown);
        syncTimDropdown(String(subjekDropdown.find('input[type="hidden"]').val() || ''), String(timDropdown.find('input[type="hidden"]').val() || ''));

        $(document).on('click', function(event) {
            if (!$(event.target).closest('[data-filter-dropdown]').length) {
                $('[data-filter-dropdown]').removeClass('open');
            }
        });

        // --- FITUR LIVE SEARCH (MODIFIKASI UTAMA) ---
        $("#searchTable").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            var visibleRows = 0;

            $("#sopTable tbody tr").filter(function() {
                // Mencari di kolom Subjek, Nama SOP, Nomor SOP, dan Tim Kerja
                var match = $(this).text().toLowerCase().indexOf(value) > -1;
                $(this).toggle(match);
                if(match) visibleRows++;
            });

            // Handle jika data tidak ditemukan saat filter
            if (visibleRows === 0 && value !== "") {
                if ($("#noDataSearch").length === 0) {
                    $("#sopTable tbody").append(`
                        <tr id="noDataSearch">
                            <td colspan="${totalColumnCount}">
                                <div class="empty-table-state">
                                    <i class="bi bi-search"></i>
                                    <h6 class="text-muted mb-1">Pencarian tidak ditemukan</h6>
                                    <div class="small text-secondary">Coba kata kunci lain yang lebih singkat atau umum.</div>
                                </div>
                            </td>
                        </tr>
                    `);
                }
            } else {
                $("#noDataSearch").remove();
            }
        });

        const sopHistoryModal = new bootstrap.Modal(document.getElementById('modalSopHistory'));
        const historyRevisionSelect = $('#historyRevisionSelect');
        const historyRevisionView = $('#historyRevisionView');

        function setHistoryViewButton(url) {
            if (url) {
                historyRevisionView.removeClass('disabled').attr('href', url).attr('aria-disabled', 'false');
            } else {
                historyRevisionView.addClass('disabled').attr('href', '#').attr('aria-disabled', 'true');
            }
        }

        $(document).on('click', '.btn-sop-history', function () {
            const historyUrl = $(this).data('history-url');

            $('#historyLatestName').text('Memuat...');
            $('#historyLatestNumber').text('-');
            $('#historyLatestStatus').text('-');
            $('#historyLatestRevision').text('-');
            $('#historyLatestSubjek').text('-');
            $('#historyLatestTimkerja').text('-');
            $('#historyLatestYear').text('-');
            $('#historyLatestView').attr('href', '#');
            historyRevisionSelect.html('<option value="">Memuat revisi...</option>');
            setHistoryViewButton(null);
            $('#historyRevisionDescription').text('Memuat rincian revisi SOP...');

            sopHistoryModal.show();

            $.get(historyUrl, function (response) {
                const latest = response.latest;
                const history = response.history || [];
                const oldRevisions = history.filter((item, index) => index > 0);

                $('#historyLatestName').text(latest?.nama_sop ?? '-');
                $('#historyLatestNumber').text(latest?.nomor_sop ?? '-');
                $('#historyLatestStatus').text(latest?.status_label ?? '-');
                $('#historyLatestRevision').text(latest?.revisi_label ?? '-');
                $('#historyLatestSubjek').text(latest?.subjek ?? '-');
                $('#historyLatestTimkerja').text(latest?.timkerja ?? '-');
                $('#historyLatestYear').text(latest?.tahun ?? '-');
                $('#historyLatestView').attr('href', latest?.view_url ?? '#');

                if (oldRevisions.length === 0) {
                    historyRevisionSelect.html('<option value="">Belum ada revisi terdahulu</option>');
                    setHistoryViewButton(null);
                    $('#historyRevisionDescription').text('SOP ini belum memiliki revisi terdahulu yang bisa ditampilkan.');
                    return;
                }

                const options = oldRevisions.map((item) => {
                    const label = `${item.revisi_label} • ${item.status_label}`;
                    const url = item.view_url ?? '';
                    const desc = item.keterangan ? `Keterangan: ${item.keterangan}` : `Nomor SOP: ${item.nomor_sop}`;
                    return `<option value="${url}" data-description="${$('<div>').text(desc).html()}">${label}</option>`;
                });

                historyRevisionSelect.html(`
    <option value="" disabled selected hidden>Pilih Versi Revisi</option>
    ${options.join('')}
`);
                $('#historyRevisionDescription').text('Pilih salah satu revisi terdahulu dari dropdown untuk melihat file SOP sebelumnya.');
            }).fail(function () {
                historyRevisionSelect.html('<option value="">Gagal memuat revisi</option>');
                setHistoryViewButton(null);
                $('#historyRevisionDescription').text('Rincian SOP gagal dimuat. Coba lagi.');
            });
        });

        historyRevisionSelect.on('change', function () {
            const selected = $(this).find('option:selected');
            const url = $(this).val();
            const description = selected.data('description');

            setHistoryViewButton(url || null);
            $('#historyRevisionDescription').text(description || 'Pilih salah satu revisi terdahulu dari dropdown untuk melihat file SOP sebelumnya.');
        });

        @if($canManage)
            const sopActionModalEl = document.getElementById('modalSopAction');
            const sopActionModal = sopActionModalEl ? new bootstrap.Modal(sopActionModalEl) : null;
            const monitoringTabTrigger = document.getElementById('monitoring-tab');
            const evaluasiUrlBase = @json(url($prefix . '/evaluasi'));

            sopActionModalEl?.addEventListener('shown.bs.modal', function () {
                document.querySelector('.modal-backdrop')?.classList.add('sop-sidebar-backdrop');
            });

            sopActionModalEl?.addEventListener('hidden.bs.modal', function () {
                document.querySelector('.modal-backdrop')?.classList.remove('sop-sidebar-backdrop');
            });

            $('#sopEvaluasiForm').on('submit', function(event) {
                const hasCheckedCriteria = $('.sop-evaluasi-criteria:checked').length > 0;

                if (!hasCheckedCriteria) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kriteria belum dipilih',
                        text: 'Pilih minimal satu kriteria evaluasi terlebih dahulu.',
                        confirmButtonText: 'OK'
                    });
                }
            });

            $('.btn-open-sop-action').on('click', function() {
                const button = $(this);
                const sopId = button.data('id');
                const monitoringId = button.data('monitoring-id');
                const evaluasiId = button.data('evaluasi-id');
                const hasMonitoring = String(button.data('monitored')) === '1';
                const hasEvaluasi = Boolean(evaluasiId);
                const revisionNumber = Number(button.data('revisi') || 0);
                const monitoringUrlBase = @json(url($prefix . '/monitoring'));
                const evaluasiCriteria = (() => {
                    try {
                        const raw = button.attr('data-evaluasi-kriteria') || '[]';
                        return JSON.parse(raw);
                    } catch (error) {
                        return [];
                    }
                })();

                $('#sopActionName').text(button.data('nama') || '-');
                $('#sopActionNumber').text(button.data('nomor') || '-');
                $('#sopActionRevision').text(revisionNumber > 0 ? `Revisi ke-${revisionNumber}` : 'Versi Awal');

                $('#sopMonitoringSopId').val(sopId);
                $('#sopMonitoringProsedur').val(button.data('monitoring-prosedur') || '');
                $('#sopMonitoringKriteria').val(button.data('monitoring-kriteria') || '');
                $('#sopMonitoringHasil').val(button.data('monitoring-hasil') || '');
                $('#sopMonitoringTindakan').val(button.data('monitoring-tindakan') || '');
                $('#sopMonitoringCatatan').val(button.data('monitoring-catatan') || '');

                if (hasMonitoring && monitoringId) {
                    $('#sopMonitoringMethod').val('PUT');
                    $('#sopMonitoringForm').attr('action', `${monitoringUrlBase}/${monitoringId}`);
                    $('#sopMonitoringState').val('Sudah dimonitoring - mode edit otomatis');
                    $('#sopMonitoringSubmit').text('Simpan Perubahan Monitoring');
                } else {
                    $('#sopMonitoringMethod').val('POST');
                    $('#sopMonitoringForm').attr('action', monitoringUrlBase);
                    $('#sopMonitoringState').val('Belum dimonitoring - mode tambah');
                    $('#sopMonitoringSubmit').text('Simpan Monitoring');
                }

                $('#sopEvaluasiSopId').val(sopId);
                $('#sopEvaluasiTarget').val(button.data('nama') || '-');
                $('#sopEvaluasiHasil').val(button.data('evaluasi-hasil') || '');
                $('#sopEvaluasiCatatan').val(button.data('evaluasi-catatan') || '');
                $('.sop-evaluasi-criteria').prop('checked', false);
                evaluasiCriteria.forEach((item) => {
                    $('.sop-evaluasi-criteria').filter(function() {
                        return $(this).val() === item;
                    }).prop('checked', true);
                });

                if (hasEvaluasi) {
                    $('#sopEvaluasiMethod').val('PUT');
                    $('#sopEvaluasiForm').attr('action', `${evaluasiUrlBase}/${evaluasiId}`);
                    $('#sopEvaluasiState').val('Sudah dievaluasi - mode edit otomatis');
                    $('#sopEvaluasiSubmit').text('Simpan Perubahan Evaluasi');
                } else {
                    $('#sopEvaluasiMethod').val('POST');
                    $('#sopEvaluasiForm').attr('action', evaluasiUrlBase);
                    $('#sopEvaluasiState').val(hasMonitoring ? 'Belum dievaluasi - mode tambah' : 'Menunggu monitoring terlebih dahulu');
                    $('#sopEvaluasiSubmit').text('Simpan Evaluasi');
                }

                const evaluasiDisabled = !hasMonitoring;
                $('#sopEvaluasiForm')
                    .find('textarea, input[type="checkbox"], button[type="submit"]')
                    .prop('disabled', evaluasiDisabled);
                $('#sopEvaluasiSopId, #sopEvaluasiMethod')
                    .prop('disabled', false);

                $('#sopRevisionId').val(sopId);
                $('#sopRevisionName').val(button.data('nama') || '-');
                $('#sopRevisionInfo').text(`Posisi saat ini: ${revisionNumber > 0 ? `Revisi ke-${revisionNumber}` : 'Versi Awal'}`);

                if (monitoringTabTrigger) {
                    bootstrap.Tab.getOrCreateInstance(monitoringTabTrigger).show();
                }

                sopActionModal?.show();
            });
        @endif

        @if($canBulkDelete)
            // --- LOGIKA BULK DELETE (CEKLIS) ---
            const $selectAll = $('#selectAll');
            const $btnBulkDelete = $('#btnBulkDelete');
            const $checkCount = $('#checkCount');

            function updateBulkButton() {
                const count = $('.sop-checkbox:checked').length;
                $checkCount.text(count);
                if (count > 0) {
                    $btnBulkDelete.fadeIn();
                } else {
                    $btnBulkDelete.fadeOut();
                }
            }

            $selectAll.on('change', function() {
                $('.sop-checkbox:visible').prop('checked', this.checked);
                updateBulkButton();
            });

            $(document).on('change', '.sop-checkbox', function() {
                updateBulkButton();
            });

            $btnBulkDelete.on('click', function() {
                Swal.fire({
                    title: 'Hapus Masal?',
                    text: `Anda akan menghapus ${$('.sop-checkbox:checked').length} data sekaligus.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus Semua',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#formBulkDelete').submit();
                    }
                });
            });

            // Delete Confirmation (Single Delete)
            $('.btn-delete-single').on('click', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Dokumen?',
                    text: "Data SOP akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = $(`<form action="{{ url($prefix . '/sop') }}/${id}" method="POST">
                            @csrf @method('DELETE')
                        </form>`).appendTo('body');
                        form.submit();
                    }
                });
            });
        @endif

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session("success") }}',
                confirmButtonText: 'OK',
                background: '#ffffff',
                color: '#0f172a'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: '{{ session("error") }}',
                confirmButtonText: 'OK'
            });
        @endif

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
