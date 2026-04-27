@extends('layouts.sidebarmenu')

@section('content')
@php($prefix = strtolower(Auth::user()->role ?? 'admin'))

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
    .import-page {
        padding: 2rem;
        background: #f8fafc;
        min-height: 100vh;
    }

    .import-card {
        border: 1px solid #dbe5f1;
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .import-header {
        padding: 1.6rem 1.8rem;
        background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%);
        color: #ffffff;
    }

    .import-header h4 {
        margin: 0;
        font-weight: 800;
    }

    .import-body {
        padding: 1.8rem;
    }

    .import-hint {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 18px;
        padding: 1rem 1.1rem;
        color: #1e3a8a;
    }

    .import-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
        margin-top: 1rem;
    }

    .import-summary-card {
        border: 1px solid #dbe5f1;
        border-radius: 18px;
        background: #ffffff;
        padding: 1rem;
    }

    .import-summary-card .label {
        font-size: 0.84rem;
        color: #64748b;
        margin-bottom: 6px;
    }

    .import-summary-card .value {
        font-size: 1.8rem;
        font-weight: 800;
        color: #0f172a;
    }

    .failed-list {
        margin-top: 1rem;
        border: 1px solid #fecaca;
        background: #fff7f7;
        border-radius: 18px;
        padding: 1rem 1.1rem;
    }

    .failed-list ul {
        margin: 0;
        padding-left: 1.1rem;
    }

    .form-label {
        font-weight: 700;
        color: #334155;
    }

    .form-control {
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        padding: 0.8rem 0.95rem;
    }

    .btn-import {
        border: 0;
        border-radius: 14px;
        background: #0d47a1;
        color: #ffffff;
        font-weight: 700;
        padding: 0.85rem 1.4rem;
    }

    .format-box {
        background: #f8fafc;
        border: 1px dashed #94a3b8;
        border-radius: 18px;
        padding: 1rem 1.1rem;
        margin-top: 1rem;
    }

    .format-box code {
        color: #0f172a;
    }
</style>

<div class="import-page">
    <div class="container-fluid">
        <div class="mb-4">
            <h1 class="h3 fw-bold mb-1">Import Massal SOP</h1>
            <p class="text-muted mb-0">Upload 1 file CSV. Sistem akan mencocokkan PDF berdasarkan kolom <code>nama_file</code> dari folder <code>storage/app/public/uploads/sop</code>, atau dari file PDF tambahan jika Anda unggah di sini.</p>
        </div>

        <div class="import-card">
            <div class="import-header">
                <h4>Import CSV SOP</h4>
                <small>Baris yang gagal akan dilewati, import tetap lanjut ke data berikutnya.</small>
            </div>

            <div class="import-body">
                @if(session('import_summary'))
                    @php($summary = session('import_summary'))
                    <div class="import-hint mb-4">
                        <strong>Hasil import terakhir:</strong>
                        <div class="import-summary-grid">
                            <div class="import-summary-card">
                                <div class="label">Berhasil</div>
                                <div class="value text-success">{{ $summary['success_count'] ?? 0 }}</div>
                            </div>
                            <div class="import-summary-card">
                                <div class="label">Gagal</div>
                                <div class="value text-danger">{{ $summary['failed_count'] ?? 0 }}</div>
                            </div>
                            <div class="import-summary-card">
                                <div class="label">Baru Ditambahkan</div>
                                <div class="value text-primary">{{ $summary['created_count'] ?? 0 }}</div>
                            </div>
                            <div class="import-summary-card">
                                <div class="label">Diperbarui</div>
                                <div class="value text-warning">{{ $summary['updated_count'] ?? 0 }}</div>
                            </div>
                        </div>

                        @if(!empty($summary['failed_rows']))
                            <div class="failed-list">
                                <strong class="d-block mb-2">Detail baris gagal</strong>
                                <ul>
                                    @foreach($summary['failed_rows'] as $failedRow)
                                        <li>Baris {{ $failedRow['row'] ?? '-' }}: {{ $failedRow['reason'] ?? 'Gagal diproses.' }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger rounded-4 border-0 shadow-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route($prefix . '.sop.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        <div class="col-12">
                            <label for="excel_file" class="form-label">File CSV</label>
                            <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".csv,.txt" required>
                            <small class="text-muted">Format yang didukung: <code>.csv</code> dengan pemisah <code>;</code> atau <code>,</code>.</small>
                        </div>

                        <div class="col-12">
                            <label for="pdf_files" class="form-label">File PDF SOP Tambahan (Opsional)</label>
                            <input type="file" name="pdf_files[]" id="pdf_files" class="form-control" accept=".pdf" multiple>
                            <small class="text-muted">Kosongkan jika file PDF SOP sudah ada di folder <code>storage/app/public/uploads/sop</code>.</small>
                        </div>
                    </div>

                    <div class="format-box">
                        <strong class="d-block mb-2">Format CSV wajib</strong>
                        <div><code>nama_sop | nomor_sop | tahun | subjek | nama_file</code></div>
                        <div class="mt-2 text-muted">Contoh <code>nama_file</code>: <code>SOP_0001_MONITORING DAN EVALUASI CAPAIAN TARGET RENSTRA.pdf</code></div>
                        <div class="mt-2 text-muted">Jika nama subjek unik, isi saja misalnya <code>Distribusi</code>. Jika subjek sama tapi tim kerja berbeda, isi format <code>Umum | Keuangan</code> atau <code>Umum | SDM Hukum</code>.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route($prefix . '.sop.index') }}" class="btn btn-outline-secondary rounded-4 px-4">Kembali</a>
                        <button type="submit" class="btn-import">Proses Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Import selesai',
            text: @json(session('success')),
            confirmButtonColor: '#0d47a1'
        });
    @endif
</script>
@endsection
