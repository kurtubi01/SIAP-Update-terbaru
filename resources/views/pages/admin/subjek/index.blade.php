@extends('layouts.sidebarmenu')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@php($timkerjaSearchData = $timkerja->map(fn ($item) => ['id' => (int) $item->id_timkerja, 'label' => $item->nama_timkerja])->values())
<style>
.app-modal {
    display:none;
}
.app-modal.is-open {
    display:block;
}
.search-select {
    position: relative;
}
.search-select-input {
    padding-left: 42px;
}
.search-select-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    z-index: 2;
}
.search-select-menu {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #dbe5f1;
    border-radius: 14px;
    box-shadow: 0 16px 30px rgba(15, 23, 42, 0.12);
    overflow: hidden;
    z-index: 20;
    display: none;
}
.search-select-menu.is-open {
    display: block;
}
.search-select-list {
    max-height: 220px;
    overflow-y: auto;
}
.search-select-item {
    padding: 11px 14px;
    cursor: pointer;
    border-bottom: 1px solid #eff4f9;
}
.search-select-item:last-child {
    border-bottom: none;
}
.search-select-item:hover {
    background: #eff6ff;
}
.search-select-title {
    font-weight: 700;
    color: #0f172a;
}
.search-select-empty {
    padding: 12px 14px;
    color: #64748b;
}

[data-theme="dark"] #searchTable,
[data-theme="dark"] .search-select-menu,
[data-theme="dark"] .search-select-input {
    background: #111827 !important;
    color: #e5edf7 !important;
    border-color: #334155 !important;
}

[data-theme="dark"] #searchTable::placeholder,
[data-theme="dark"] .search-select-input::placeholder {
    color: #94a3b8 !important;
}

[data-theme="dark"] .search-select-title {
    color: #e5edf7 !important;
}

[data-theme="dark"] .search-select-item {
    border-color: #2d3a4f !important;
}

[data-theme="dark"] .search-select-item:hover {
    background: #1f2a3d !important;
}

[data-theme="dark"] .bg-info-subtle {
    background: #19364a !important;
}

[data-theme="dark"] .text-info {
    color: #7dd3fc !important;
}

[data-theme="dark"] .bg-success-subtle {
    background: #183127 !important;
}

[data-theme="dark"] .bg-danger-subtle {
    background: #3a1f24 !important;
}

[data-theme="dark"] .btn.btn-light,
[data-theme="dark"] .btn-group .btn {
    background: #172033 !important;
    border-color: #334155 !important;
    color: #e5edf7 !important;
}

[data-theme="dark"] .btn.btn-light:hover,
[data-theme="dark"] .btn-group .btn:hover {
    background: #1f2a3d !important;
}

[data-theme="dark"] .modal-content,
[data-theme="dark"] .modal-header,
[data-theme="dark"] .modal-footer {
    background: #111827 !important;
    color: #e5edf7 !important;
    border-color: #334155 !important;
}

[data-theme="dark"] .btn-close {
    filter: invert(1) grayscale(1);
}
</style>
<div class="container-fluid px-4 py-4">

    {{-- HEADER --}}
    <div class="app-page-header">
        <div>
            <h1 class="app-page-title">Manajemen Subjek</h1>
            <p class="app-page-subtitle">Kelola data subjek dengan tampilan yang lebih konsisten. Setiap subjek wajib terhubung ke tim kerja agar alur SOP lebih tertata.</p>
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold">Manajemen Subjek</li>
                </ol>
            </nav>
        </div>

        <button type="button"
                class="btn btn-primary rounded-3 shadow-sm px-4 py-2 fw-bold d-flex align-items-center"
                data-app-modal-open="modalTambahSubjek"
                onclick="return openAppModal('modalTambahSubjek')">
            <i class="bi bi-plus-circle me-2"></i>
            Tambah Subjek
        </button>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div id="alert-berhasil"
             class="alert alert-success border-0 shadow-sm rounded-4 alert-dismissible fade show mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                <div>
                    <strong>Berhasil!</strong>
                    {{ session('success') }}
                </div>
            </div>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                <div>
                    <strong>Gagal!</strong>
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                <div>
                    <strong>Validasi gagal.</strong>
                    {{ $errors->first() }}
                </div>
            </div>
        </div>
    @endif

    {{-- TABLE --}}
    <div class="app-table-card">

        <div class="app-table-toolbar">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="soft-note">
                    Data subjek dirapikan dengan warna kolom yang lebih lembut agar nyaman dilihat pegawai BPS Provinsi Banten.
                </div>

                <input type="text"
                       id="searchTable"
                       class="form-control rounded-3"
                       placeholder="Cari subjek..."
                       style="max-width:250px;">
            </div>
        </div>

        <div class="app-table-wrap">
            <div class="table-responsive">
                <table class="table app-table-modern align-middle mb-0" id="tableSubjek">

                    <thead>
                        <tr>
                            <th width="70" class="px-4 py-3">No</th>
                            <th class="py-3">Tim Kerja</th>
                            <th class="py-3">Nama Subjek</th>
                            <th class="py-3">Deskripsi</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center" width="150">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($subjek as $s)
                        <tr>

                            <td class="px-4 fw-bold text-muted">
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill">
                                    {{ $s->timkerja->nama_timkerja ?? '-' }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-bold text-dark">
                                    {{ $s->nama_subjek }}
                                </div>
                            </td>

                            <td class="text-muted small">
                                {{ $s->deskripsi ?: '-' }}
                            </td>

                            <td>
                                @if($s->status == 'aktif')
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">

                                <div class="btn-group shadow-sm">

                                    {{-- EDIT --}}
                                    <button type="button"
                                            class="btn btn-sm btn-light border"
                                            data-app-modal-open="modalEdit{{ $s->id_subjek }}"
                                            onclick="return openAppModal('modalEdit{{ $s->id_subjek }}')">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </button>

                                    {{-- DELETE --}}
                                    <form action="{{ route('admin.subjek.destroy', $s->id_subjek) }}"
                                          method="POST"
                                          class="d-inline form-hapus">

                                        @csrf
                                        @method('DELETE')

                                        <button type="button"
                                                class="btn btn-sm btn-light border btn-delete">
                                            <i class="bi bi-trash text-danger"></i>
                                        </button>
                                    </form>

                                </div>

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                Data subjek tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse

                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade app-modal" id="modalTambahSubjek" tabindex="-1" data-app-modal>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <form action="{{ route('admin.subjek.store') }}" method="POST">
                @csrf

                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">Tambah Subjek</h5>
                    <button type="button" class="btn-close" data-app-modal-close></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Subjek</label>
                        <input type="text"
                               name="nama_subjek"
                               class="form-control rounded-3"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tim Kerja</label>
                        <input type="hidden" name="id_timkerja" class="timkerja-hidden-input" required>
                        <div class="search-select">
                            <i class="bi bi-search search-select-icon"></i>
                            <input type="text"
                                   class="form-control rounded-3 search-select-input timkerja-search-input"
                                   placeholder="Ketik nama tim kerja...">
                            <div class="search-select-menu">
                                <div class="search-select-list"></div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">Wajib pilih tim kerja yang bertanggung jawab atas subjek ini.</small>
                    </div>



                    <div>
                        <label class="form-label fw-bold small">Deskripsi</label>
                        <textarea name="deskripsi"
                                  class="form-control rounded-3"
                                  rows="3"></textarea>
                    </div>

                </div>

                <div class="modal-footer border-0">
                    <button type="button"
                            class="btn btn-light fw-bold rounded-3"
                            data-app-modal-close>
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary px-4 fw-bold rounded-3">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
@foreach($subjek as $s)
<div class="modal fade app-modal" id="modalEdit{{ $s->id_subjek }}" tabindex="-1" data-app-modal>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <form action="{{ route('admin.subjek.update', $s->id_subjek) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">Edit Subjek</h5>
                    <button type="button" class="btn-close" data-app-modal-close></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tim Kerja</label>
                        <input type="hidden" name="id_timkerja" class="timkerja-hidden-input" value="{{ $s->id_timkerja }}" required>
                        <div class="search-select">
                            <i class="bi bi-search search-select-icon"></i>
                            <input type="text"
                                   class="form-control rounded-3 search-select-input timkerja-search-input"
                                   placeholder="Ketik nama tim kerja..."
                                   value="{{ $s->timkerja->nama_timkerja ?? '' }}">
                            <div class="search-select-menu">
                                <div class="search-select-list"></div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">Wajib pilih tim kerja yang bertanggung jawab atas subjek ini.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Subjek</label>
                        <input type="text"
                               name="nama_subjek"
                               class="form-control rounded-3"
                               value="{{ $s->nama_subjek }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Deskripsi</label>
                        <textarea name="deskripsi"
                                  class="form-control rounded-3"
                                  rows="3">{{ $s->deskripsi }}</textarea>
                    </div>

                    <div>
                        <label class="form-label fw-bold small">Status</label>
                        <select name="status" class="form-select rounded-3">
                            <option value="aktif" {{ $s->status == 'aktif' ? 'selected' : '' }}>
                                Aktif
                            </option>
                            <option value="nonaktif" {{ $s->status == 'nonaktif' ? 'selected' : '' }}>
                                Nonaktif
                            </option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer border-0">
                    <button type="button"
                            class="btn btn-light rounded-3 fw-bold"
                            data-app-modal-close>
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary px-4 rounded-3 fw-bold">
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach

<script>
let appModalBackdrop = null;
const timkerjaSearchData = @json($timkerjaSearchData);

function ensureAppModalBackdrop() {
    if (!appModalBackdrop) {
        appModalBackdrop = document.createElement('div');
        appModalBackdrop.className = 'modal-backdrop fade';
        appModalBackdrop.addEventListener('click', function () {
            document.querySelectorAll('[data-app-modal].is-open').forEach(function (modal) {
                closeAppModal(modal.id);
            });
        });
    }

    if (!document.body.contains(appModalBackdrop)) {
        document.body.appendChild(appModalBackdrop);
    }
}

function openAppModal(modalId) {
    const modalElement = document.getElementById(modalId);

    if (!modalElement) {
        return false;
    }

    ensureAppModalBackdrop();
    modalElement.style.display = 'block';
    modalElement.classList.add('show', 'is-open');
    modalElement.removeAttribute('aria-hidden');
    modalElement.setAttribute('aria-modal', 'true');
    document.body.classList.add('modal-open');

    setTimeout(function () {
        if (appModalBackdrop) {
            appModalBackdrop.classList.add('show');
        }
    }, 10);

    return false;
}

function closeAppModal(modalId) {
    const modalElement = document.getElementById(modalId);

    if (!modalElement) {
        return false;
    }

    modalElement.classList.remove('show', 'is-open');
    modalElement.style.display = 'none';
    modalElement.setAttribute('aria-hidden', 'true');
    modalElement.removeAttribute('aria-modal');

    if (!document.querySelector('[data-app-modal].is-open')) {
        document.body.classList.remove('modal-open');

        if (appModalBackdrop) {
            appModalBackdrop.classList.remove('show');
            appModalBackdrop.remove();
        }
    }

    return false;
}

document.addEventListener('DOMContentLoaded', function(){
    function renderTimkerjaSearch(modal) {
        const input = modal.querySelector('.timkerja-search-input');
        const hiddenInput = modal.querySelector('.timkerja-hidden-input');
        const menu = modal.querySelector('.search-select-menu');
        const list = modal.querySelector('.search-select-list');

        if (!input || !hiddenInput || !menu || !list) {
            return;
        }

        const draw = (keyword = '') => {
            const normalized = String(keyword || '').trim().toLowerCase();
            const filtered = timkerjaSearchData.filter(item => item.label.toLowerCase().includes(normalized));

            if (!filtered.length) {
                list.innerHTML = '<div class="search-select-empty">Tim kerja tidak ditemukan.</div>';
                return;
            }

            list.innerHTML = filtered.map(item => `
                <div class="search-select-item" data-id="${item.id}" data-label="${item.label}">
                    <div class="search-select-title">${item.label}</div>
                </div>
            `).join('');
        };

        input.addEventListener('focus', function() {
            draw(input.value);
            menu.classList.add('is-open');
        });

        input.addEventListener('input', function() {
            hiddenInput.value = '';
            draw(input.value);
            menu.classList.add('is-open');
        });

        list.addEventListener('click', function(event) {
            const option = event.target.closest('.search-select-item');
            if (!option) {
                return;
            }

            hiddenInput.value = option.dataset.id;
            input.value = option.dataset.label;
            menu.classList.remove('is-open');
        });

        input.addEventListener('blur', function() {
            setTimeout(() => {
                const matched = timkerjaSearchData.find(item => item.label.toLowerCase() === input.value.trim().toLowerCase());
                if (matched) {
                    hiddenInput.value = matched.id;
                    input.value = matched.label;
                } else if (!input.value.trim()) {
                    hiddenInput.value = '';
                } else {
                    hiddenInput.value = '';
                }
                menu.classList.remove('is-open');
            }, 150);
        });
    }

    document.querySelectorAll('[data-app-modal]').forEach(renderTimkerjaSearch);

    document.querySelectorAll('[data-app-modal-close]').forEach(button => {
        button.addEventListener('click', function () {
            const modal = this.closest('[data-app-modal]');

            if (modal) {
                closeAppModal(modal.id);
            }
        });
    });

    document.querySelectorAll('[data-app-modal]').forEach(modal => {
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeAppModal(modal.id);
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('[data-app-modal].is-open').forEach(function (modal) {
                closeAppModal(modal.id);
            });
        }
    });

    const alertElement = document.getElementById('alert-berhasil');

    if(alertElement){
        setTimeout(function(){
            const bsAlert = new bootstrap.Alert(alertElement);
            bsAlert.close();
        }, 2000);
    }

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(){

            const form = this.closest('.form-hapus');

            Swal.fire({
                title: 'Hapus Subjek?',
                text: 'Data akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if(result.isConfirmed){
                    form.submit();
                }
            });

        });
    });

    document.getElementById('searchTable').addEventListener('keyup', function(){

        let keyword = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tableSubjek tbody tr');

        rows.forEach(row => {
            row.style.display =
                row.innerText.toLowerCase().includes(keyword)
                ? ''
                : 'none';
        });

    });

});
</script>
@endsection
