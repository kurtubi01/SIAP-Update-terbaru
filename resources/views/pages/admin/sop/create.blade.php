@extends('layouts.sidebarmenu')

@section('content')
@php($prefix = strtolower(Auth::user()->role) === 'admin' ? 'admin' : 'operator')
@php($subjekOptions = $subjek->map(function ($item) {
    return [
        'id' => (int) $item->id_subjek,
        'nama_subjek' => $item->nama_subjek,
        'timkerja_id' => $item->id_timkerja ? (int) $item->id_timkerja : null,
        'timkerja_label' => $item->timkerja->nama_timkerja ?? 'Tanpa Tim Kerja',
    ];
})->values())
@php($subjekGroups = $subjekOptions
    ->groupBy(fn ($item) => mb_strtolower(trim((string) $item['nama_subjek'])))
    ->map(function ($items) {
        $first = $items->first();

        return [
            'label' => $first['nama_subjek'],
            'items' => $items->values()->all(),
        ];
    })
    ->values())
@php($selectedOldSubjek = $subjekOptions->firstWhere('id', (int) old('id_subjek')))

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    .main-content-area {
        background-color: #f8fafc;
        min-height: 100vh;
        padding: 2rem;
    }

    .card-premium {
        border: none;
        border-radius: 22px;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .card-header-bps {
        background: #0d47a1;
        color: white;
        padding: 1.5rem;
        border: none;
    }

    .form-label {
        font-weight: 700;
        color: #334155;
        font-size: 0.94rem;
    }

    .btn-save {
        background: #0d47a1;
        border: none;
        border-radius: 12px;
        padding: 12px 30px;
        font-weight: 700;
        transition: 0.3s;
    }

    .btn-save:hover {
        background: #0a3d8d;
        transform: translateY(-1px);
    }

    .form-control,
    .form-control-lg {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 12px 15px;
        transition: all 0.2s;
    }

    .form-control:focus,
    .form-control-lg:focus {
        border-color: #0d47a1;
        box-shadow: 0 0 0 0.25rem rgba(13, 71, 161, 0.1);
    }

    .search-select {
        position: relative;
    }

    .search-select-input {
        padding-left: 44px;
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
        z-index: 20;
        background: #ffffff;
        border: 1px solid #dbe5f1;
        border-radius: 16px;
        box-shadow: 0 20px 36px rgba(15, 23, 42, 0.12);
        overflow: hidden;
        display: none;
    }

    .search-select-menu.is-open {
        display: block;
    }

    .search-select-list {
        max-height: 270px;
        overflow-y: auto;
    }

    .search-select-item {
        padding: 12px 16px;
        border-bottom: 1px solid #eff4f9;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .search-select-item:last-child {
        border-bottom: none;
    }

    .search-select-item:hover,
    .search-select-item.active {
        background: #eff6ff;
    }

    .search-select-title {
        color: #0f172a;
        font-weight: 700;
        line-height: 1.4;
    }

    .search-select-meta {
        color: #64748b;
        font-size: 0.82rem;
        margin-top: 2px;
    }

    .search-select-empty {
        padding: 14px 16px;
        color: #64748b;
        font-size: 0.9rem;
    }

    .field-note {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 8px;
    }

    .selected-file-note {
        margin-top: 18px;
        border: 1px solid #dbe5f1;
        border-radius: 16px;
        background: #f8fbff;
        padding: 16px;
        display: none;
        color: #475569;
        font-size: 0.92rem;
        font-weight: 600;
        word-break: break-word;
    }

    .selected-file-note.is-visible {
        display: block;
    }
</style>

<div class="main-content-area app-page-shell">
    <div class="container-fluid">
        <div class="app-page-header mb-4">
            <div>
                <h1 class="app-page-title">Tambah SOP Baru</h1>
                <p class="app-page-subtitle">Gunakan pencarian subjek dan tim kerja agar input SOP lebih cepat, rapi, dan tetap sesuai data bawaan sistem.</p>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route($prefix . '.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route($prefix . '.sop.index') }}" class="text-decoration-none text-muted">Data SOP</a></li>
                        <li class="breadcrumb-item active text-primary fw-bold">Tambah SOP</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card card-premium">
            <div class="card-header-bps d-flex align-items-center">
                <div class="bg-white rounded-circle p-2 me-3 d-inline-flex">
                    <i class="bi bi-plus-lg text-primary"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">Tambah SOP Baru</h5>
                    <small class="opacity-75">Pilih subjek dulu, lalu cari tim kerja yang memang terkait dengan subjek tersebut.</small>
                </div>
            </div>

            <div class="card-body p-4 p-lg-5">
                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route($prefix . '.sop.store') }}" method="POST" enctype="multipart/form-data" id="formSop">
                    @csrf
                    <input type="hidden" name="id_subjek" id="selectedSubjekId" value="{{ old('id_subjek') }}">

                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap SOP</label>
                            <input type="text" name="nama_sop" value="{{ old('nama_sop') }}"
                                   class="form-control form-control-lg"
                                   placeholder="Contoh: SOP Pelayanan Statistik">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-primary">Subjek</label>
                            <div class="search-select" id="subjekSearchSelect">
                                <i class="bi bi-search search-select-icon"></i>
                                <input type="text"
                                       id="subjekSearchInput"
                                       class="form-control search-select-input"
                                       placeholder="Ketik nama subjek..."
                                       autocomplete="off"
                                       value="{{ $selectedOldSubjek['nama_subjek'] ?? '' }}"
                                       >
                                <div class="search-select-menu" id="subjekSearchMenu">
                                    <div class="search-select-list" id="subjekSearchList"></div>
                                </div>
                            </div>
                            <div class="field-note">Saat mengetik 1 huruf, nama subjek yang cocok langsung muncul di bawah.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-primary">Tim Kerja</label>
                            <div class="search-select" id="timkerjaSearchSelect">
                                <i class="bi bi-search search-select-icon"></i>
                                <input type="text"
                                       id="timkerjaSearchInput"
                                       class="form-control search-select-input"
                                       placeholder="Pilih subjek terlebih dahulu"
                                       autocomplete="off"
                                       value="{{ $selectedOldSubjek['timkerja_label'] ?? '' }}"
                                       {{ $selectedOldSubjek ? '' : 'disabled' }}>
                                <div class="search-select-menu" id="timkerjaSearchMenu">
                                    <div class="search-select-list" id="timkerjaSearchList"></div>
                                </div>
                            </div>
                            <div class="field-note">Tim kerja tidak otomatis terisi. Silakan pilih tim kerja yang memang ada di subjek tadi.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor SOP</label>
                            <input type="text" name="nomor_sop" value="{{ old('nomor_sop') }}"
                                   class="form-control" placeholder="B/123/BPS/2026">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tahun Terbit</label>
                            <input type="number" name="tahun" class="form-control"
                                   value="{{ old('tahun', date('Y')) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Dokumen SOP (PDF)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-file-earmark-pdf text-danger"></i></span>
                                <input type="file" name="link_sop" class="form-control" accept=".pdf" id="fileSop" required>
                            </div>
                            <div class="field-note">Pilih satu file PDF untuk satu data SOP. Jika ingin menambah SOP lain, simpan dulu data ini lalu buat SOP baru lagi.</div>
                            <div id="selectedFileNote" class="selected-file-note"></div>
                        </div>
                    </div>

                    <hr class="my-4 text-secondary opacity-25">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route($prefix . '.sop.index') }}" class="btn btn-outline-secondary px-4 border-0">Batal</a>
                        <button type="submit" class="btn btn-primary btn-save shadow-sm">
                            <i class="bi bi-cloud-arrow-up me-2"></i>Simpan Data SOP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        const subjekGroups = @json($subjekGroups);
        const selectedSubjekId = $('#selectedSubjekId');
        const subjekInput = $('#subjekSearchInput');
        const subjekMenu = $('#subjekSearchMenu');
        const subjekList = $('#subjekSearchList');
        const timkerjaInput = $('#timkerjaSearchInput');
        const timkerjaMenu = $('#timkerjaSearchMenu');
        const timkerjaList = $('#timkerjaSearchList');
        const fileInput = $('#fileSop');
        const namaSopInput = $('input[name="nama_sop"]');
        const tahunInput = $('input[name="tahun"]');
        const selectedFileNote = $('#selectedFileNote');
        let activeSubjekGroup = null;

        function openMenu(menu) {
            menu.addClass('is-open');
        }

        function closeMenu(menu) {
            menu.removeClass('is-open');
        }

        function renderEmptyState(target, message) {
            target.html('<div class="search-select-empty">' + message + '</div>');
        }

        function renderSubjekOptions(keyword = '') {
            const normalized = keyword.trim().toLowerCase();
            const filtered = subjekGroups.filter(group => group.label.toLowerCase().includes(normalized));

            if (!filtered.length) {
                renderEmptyState(subjekList, 'Subjek tidak ditemukan.');
                return;
            }

            subjekList.html(filtered.map(group => `
                <div class="search-select-item" data-role="subjek-option" data-label="${group.label}">
                    <div class="search-select-title">${group.label}</div>
                    <div class="search-select-meta">${group.items.length} pilihan tim kerja tersedia</div>
                </div>
            `).join(''));
        }

        function renderTimkerjaOptions(keyword = '') {
            if (!activeSubjekGroup) {
                renderEmptyState(timkerjaList, 'Pilih subjek terlebih dahulu.');
                return;
            }

            const normalized = keyword.trim().toLowerCase();
            const filtered = activeSubjekGroup.items.filter(item => item.timkerja_label.toLowerCase().includes(normalized));

            if (!filtered.length) {
                renderEmptyState(timkerjaList, 'Tim kerja tidak ditemukan untuk subjek ini.');
                return;
            }

            timkerjaList.html(filtered.map(item => `
                <div class="search-select-item" data-role="timkerja-option" data-id="${item.id}">
                    <div class="search-select-title">${item.timkerja_label}</div>
                    <div class="search-select-meta">Subjek ${activeSubjekGroup.label}</div>
                </div>
            `).join(''));
        }

        function resolveSubjekGroupByLabel(label) {
            return subjekGroups.find(group => group.label.toLowerCase() === String(label || '').trim().toLowerCase()) || null;
        }

        function resolveSubjekItemById(id) {
            for (const group of subjekGroups) {
                const item = group.items.find(option => Number(option.id) === Number(id));
                if (item) {
                    return { group, item };
                }
            }

            return null;
        }

        function extractSopDataFromFilename(filename) {
            const baseName = String(filename || '').replace(/\.pdf$/i, '');
            const normalized = baseName.replace(/[_-]+/g, ' ').replace(/\s+/g, ' ').trim();
            return {
                nama_sop: normalized,
                tahun: extractYearFromText(normalized)
            };
        }

        function extractYearFromText(value) {
            const matches = String(value || '').match(/\b(19|20)\d{2}\b/g);
            return matches?.length ? matches[matches.length - 1] : String(new Date().getFullYear());
        }

        function syncSelectedFile(files) {
            if (!files.length) {
                selectedFileNote.removeClass('is-visible').text('');
                return;
            }

            const parsed = extractSopDataFromFilename(files[0].name);
            selectedFileNote
                .addClass('is-visible')
                .text('File terpilih: ' + files[0].name);

            if (!String(namaSopInput.val() || '').trim()) {
                namaSopInput.val(parsed.nama_sop);
            }

            if (!String(tahunInput.val() || '').trim()) {
                tahunInput.val(parsed.tahun);
            }
        }

        function setActiveSubjek(group) {
            activeSubjekGroup = group;
            subjekInput.val(group ? group.label : '');
            timkerjaInput.val('');
            selectedSubjekId.val('');

            if (group) {
                timkerjaInput.prop('disabled', false).attr('placeholder', 'Ketik nama tim kerja...');
                if (group.items.length === 1) {
                    timkerjaInput.val(group.items[0].timkerja_label);
                    selectedSubjekId.val(group.items[0].id);
                }
            } else {
                timkerjaInput.prop('disabled', true).attr('placeholder', 'Pilih subjek terlebih dahulu');
            }
        }

        subjekInput.on('focus input', function() {
            renderSubjekOptions($(this).val());
            openMenu(subjekMenu);
        });

        timkerjaInput.on('focus input', function() {
            renderTimkerjaOptions($(this).val());
            openMenu(timkerjaMenu);
        });

        $(document).on('click', '[data-role="subjek-option"]', function() {
            const group = resolveSubjekGroupByLabel($(this).data('label'));
            setActiveSubjek(group);
            closeMenu(subjekMenu);
            timkerjaInput.trigger('focus');
        });

        $(document).on('click', '[data-role="timkerja-option"]', function() {
            const selectedId = $(this).data('id');
            const item = activeSubjekGroup?.items.find(option => Number(option.id) === Number(selectedId));

            if (!item) {
                return;
            }

            timkerjaInput.val(item.timkerja_label);
            selectedSubjekId.val(item.id);
            closeMenu(timkerjaMenu);
        });

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#subjekSearchSelect').length) {
                closeMenu(subjekMenu);
            }

            if (!$(event.target).closest('#timkerjaSearchSelect').length) {
                closeMenu(timkerjaMenu);
            }

        });

        subjekInput.on('blur', function() {
            setTimeout(function() {
                const matchedGroup = resolveSubjekGroupByLabel(subjekInput.val());
                if (!matchedGroup) {
                    setActiveSubjek(null);
                    subjekInput.val('');
                }
            }, 150);
        });

        timkerjaInput.on('blur', function() {
            setTimeout(function() {
                if (!activeSubjekGroup) {
                    timkerjaInput.val('');
                    return;
                }

                const matchedItem = activeSubjekGroup.items.find(item =>
                    item.timkerja_label.toLowerCase() === String(timkerjaInput.val() || '').trim().toLowerCase()
                );

                if (matchedItem) {
                    selectedSubjekId.val(matchedItem.id);
                    timkerjaInput.val(matchedItem.timkerja_label);
                } else if (activeSubjekGroup.items.length === 1) {
                    selectedSubjekId.val(activeSubjekGroup.items[0].id);
                    timkerjaInput.val(activeSubjekGroup.items[0].timkerja_label);
                } else {
                    selectedSubjekId.val('');
                    timkerjaInput.val('');
                }
            }, 150);
        });

        const oldSelection = resolveSubjekItemById(selectedSubjekId.val());
        if (oldSelection) {
            activeSubjekGroup = oldSelection.group;
            subjekInput.val(oldSelection.group.label);
            timkerjaInput.prop('disabled', false).attr('placeholder', 'Ketik nama tim kerja...');
            timkerjaInput.val(oldSelection.item.timkerja_label);
        } else {
            setActiveSubjek(null);
        }

        fileInput.on('change', function() {
            const files = Array.from(this.files || []);
            if (files.length > 1) {
                this.value = '';
                selectedFileNote.removeClass('is-visible').text('');
                Swal.fire({
                    icon: 'warning',
                    title: 'Satu SOP per penyimpanan',
                    text: 'Silakan pilih satu file PDF saja untuk setiap proses tambah SOP.',
                    confirmButtonColor: '#0d47a1'
                });
                return;
            }

            syncSelectedFile(files);
        });

        syncSelectedFile(Array.from(fileInput[0]?.files || []));

        $('#formSop').on('submit', function(e) {
            const selectedFiles = Array.from(fileInput[0]?.files || []);

            if (selectedFiles.length !== 1) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Dokumen SOP belum dipilih',
                    text: 'Silakan pilih tepat satu file PDF sebelum menyimpan.',
                    confirmButtonColor: '#0d47a1'
                });
                return;
            }

            if (!String(namaSopInput.val() || '').trim()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Nama SOP belum diisi',
                    text: 'Mohon lengkapi nama SOP sebelum menyimpan.',
                    confirmButtonColor: '#0d47a1'
                });
                return;
            }

            if (!subjekInput.val().trim()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Subjek belum dipilih',
                    text: 'Mohon cari dan pilih subjek terlebih dahulu.',
                    confirmButtonColor: '#0d47a1'
                });
                return;
            }

            if (!selectedSubjekId.val() && activeSubjekGroup && activeSubjekGroup.items.length > 1) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Tim kerja belum dipilih',
                    text: 'Subjek ini punya lebih dari satu tim kerja. Silakan pilih tim kerja yang sesuai.',
                    confirmButtonColor: '#0d47a1'
                });
                return;
            }

            if (!selectedSubjekId.val() && activeSubjekGroup && activeSubjekGroup.items.length === 1) {
                selectedSubjekId.val(activeSubjekGroup.items[0].id);
            }

            if (!selectedSubjekId.val()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Subjek atau tim kerja belum lengkap',
                    text: 'Silakan pastikan subjek dan tim kerja sudah sesuai sebelum menyimpan.',
                    confirmButtonColor: '#0d47a1'
                });
                return;
            }

            if (!String($('input[name="nomor_sop"]').val() || '').trim() || !String($('input[name="tahun"]').val() || '').trim()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Data SOP belum lengkap',
                    text: 'Nomor SOP dan tahun terbit wajib diisi sebelum menyimpan.',
                    confirmButtonColor: '#0d47a1'
                });
                return;
            }
        });

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'SOP belum berhasil disimpan',
                text: @json($errors->first()),
                confirmButtonColor: '#0d47a1'
            });
        @endif
    });
</script>
@endsection
