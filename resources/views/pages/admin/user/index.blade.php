@extends('layouts.sidebarmenu')

@section('content')

<style>
.app-modal {
    display: none;
    position: fixed;
    z-index: 1055; /* penting */
}

.modal-backdrop {
    z-index: 1050 !important;
}
.modal.show {
    display: block;
    z-index: 1055;
}
.badge-soft{
    padding:6px 12px;
    border-radius:8px;
    font-weight:700;
    font-size:11px;
}
.audit-text{
    font-size:10px;
    line-height:1.45;
    color:#94a3b8;
}
.btn-icon{
    width:34px;
    height:34px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:8px;
    border:1px solid #e2e8f0;
    background:#fff;
    transition:.2s;
}
.btn-icon:hover{
    background:#f8fafc;
    color:#2563eb;
}
.request-card {
    border: 1px solid #dbe5f1;
    border-radius: 18px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    padding: 16px;
    height: 100%;
}
</style>

<div class="container-fluid app-page-shell py-4">

    {{-- HEADER --}}
    <div class="app-page-header">

        <div>
            <h1 class="app-page-title">Manajemen User</h1>
            <p class="app-page-subtitle">Kelola akun login dan hak akses sistem dengan layout yang lebih konsisten, teks yang jelas, dan tabel yang rapi.</p>
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold">Manajemen User</li>
                </ol>
            </nav>
        </div>

        <button type="button"
                class="btn btn-primary px-4 fw-bold"
                data-app-modal-open="modalTambah"
                onclick="return openAppModal('modalTambah')">

            <i class="bi bi-plus-lg me-2"></i>
            Tambah User
        </button>

    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4">
            {{ session('success') }}
        </div>
    @endif

    @if(($accountRecoveryNotifications ?? collect())->isNotEmpty())
        <div class="app-table-card mb-4">
            <div class="app-table-toolbar">
                <div class="soft-note">Permintaan lupa username/password dari halaman login masuk ke sini. Admin hanya bisa mengubah field yang diminta pada notifikasi. Setelah diproses, notifikasi akan hilang dari daftar.</div>
            </div>
            <div class="app-table-wrap">
                <div class="row g-3">
                    @foreach($accountRecoveryNotifications as $notification)
                        @php($meta = $notification->metadata ?? [])
                        <div class="col-12 col-lg-6">
                            <div class="request-card">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $meta['nama'] ?? ($notification->user->nama ?? 'Pengguna') }}</div>
                                        <div class="small text-muted">NIP: {{ $meta['nip'] ?? ($notification->user->nip ?? '-') }}</div>
                                    </div>
                                    <span class="badge bg-warning text-dark text-uppercase">{{ ($meta['jenis_permohonan'] ?? 'akun') === 'username' ? 'Lupa Username' : 'Lupa Password' }}</span>
                                </div>
                                <div class="small mb-2">{{ $notification->pesan }}</div>
                                <div class="small text-muted mb-3">{{ $meta['catatan'] ?? 'Tanpa catatan tambahan.' }}</div>
                                @if($notification->user)
                                    <form action="{{ route('admin.user.resetCredentials', $notification->user->id_user) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="notification_id" value="{{ $notification->id_notifikasi }}">
                                        @if(($meta['jenis_permohonan'] ?? null) === 'username')
                                            <label class="small fw-bold mb-2">Username Baru</label>
                                            <input type="text" name="new_username" value="{{ $notification->user->username }}" class="form-control form-control-sm mb-2" required>
                                            <button type="submit" class="btn btn-primary btn-sm fw-bold">Ubah Username</button>
                                        @else
                                            <label class="small fw-bold mb-2">Password Baru</label>
                                            <input type="password" name="new_password" class="form-control form-control-sm mb-2" minlength="6" required>
                                            <input type="password" name="new_password_confirmation" class="form-control form-control-sm mb-2" minlength="6" placeholder="Konfirmasi password baru" required>
                                            <button type="submit" class="btn btn-primary btn-sm fw-bold">Ubah Password</button>
                                        @endif
                                    </form>
                                @else
                                    <div class="text-danger small fw-bold">User belum cocok dengan data nama/NIP yang dikirim.</div>
                                    <form action="{{ route('admin.notifications.accountRecovery.dismiss', $notification->id_notifikasi) }}" method="POST" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm fw-bold">Hapus Notifikasi Ini</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- TABLE --}}
    <div class="app-table-card">
        <div class="app-table-toolbar">
            <div class="soft-note">Daftar user dibuat lebih rapi dengan batas kolom yang jelas supaya nama, role, dan log audit lebih mudah dibaca.</div>
        </div>
        <div class="app-table-wrap">

        <div class="table-responsive">

            <table class="table app-table-modern mb-0">

                <thead>
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Nama & NIP</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Tim Kerja</th>
                        <th>Subjek</th>
                        <th>Log Audit</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($users as $index => $u)
<tr>

    <td class="text-center fw-bold text-muted">
        {{ $index + 1 }}
    </td>

    <td>
        <div class="fw-bold text-dark">
            {{ $u->nama }}
        </div>

        <div class="text-muted small">
            NIP. {{ $u->nip }}
        </div>
    </td>

    <td>
        <span class="badge bg-light text-primary border">
            {{ $u->username }}
        </span>
    </td>

    <td>

        @if($u->role == 'admin')
            <span class="badge-soft bg-danger text-white">
                ADMIN
            </span>

        @elseif($u->role == 'operator')
            <span class="badge-soft bg-primary text-white">
                OPERATOR
            </span>

        @else
            <span class="badge-soft bg-secondary text-white">
                VIEWER
            </span>
        @endif

    </td>

    <td>
        {{ $u->timkerja->nama_timkerja ?? '-' }}
    </td>

    <td>
        {{ optional($u->timkerja)->subjek?->pluck('nama_subjek')->filter()->join(', ') ?: '-' }}
    </td>

    <td>
        <div class="audit-text">

            <b>Create:</b>
            {{ $u->creator->nama ?? '-' }}

            <br>

            {{ $u->created_date ?? '-' }}

            <hr class="my-1">

            <b>Modified:</b>
            {{ $u->editor->nama ?? '-' }}

            <br>

            {{ $u->modified_date ?? '-' }}

        </div>
    </td>

    <td class="text-center">

        <div class="d-flex justify-content-center gap-2">

            {{-- EDIT --}}
            <button type="button"
                    class="btn-icon"
                    data-app-modal-open="modalEdit{{ $u->id_user }}"
                    onclick="return openAppModal('modalEdit{{ $u->id_user }}')">
                <i class="bi bi-pencil"></i>
            </button>

            {{-- DELETE --}}
            <form action="{{ route('admin.user.destroy',$u->id_user) }}"
                  method="POST"
                  class="form-delete d-inline">

                @csrf
                @method('DELETE')

                <button type="button"
                        class="btn-icon text-danger btn-delete">
                    <i class="bi bi-trash"></i>
                </button>

            </form>

        </div>

    </td>

</tr>

@empty

<tr>
    <td colspan="8" class="text-center py-5 text-muted">
        Data user belum tersedia.
    </td>
</tr>

@endforelse

                </tbody>

            </table>
{{-- MODAL EDIT --}}
@foreach($users as $u)
<div class="modal fade app-modal"
     id="modalEdit{{ $u->id_user }}"
     tabindex="-1"
     data-app-modal>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header border-0 pt-4 px-4">

                <h5 class="fw-bold">
                    Edit Data User
                </h5>

                <button type="button"
                        class="btn-close"
                        data-app-modal-close>
                </button>

            </div>

            <form action="{{ route('admin.user.update',$u->id_user) }}"
                  method="POST"
                  data-user-form="edit">

                @csrf
                @method('PUT')

                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="small fw-bold">
                            Nama Lengkap
                        </label>

                        <input type="text"
                               name="nama"
                               value="{{ $u->nama }}"
                               class="form-control bg-light border-0 py-2"
                               required>
                    </div>

                    <div class="row">

                        <div class="col-6 mb-3">
                            <label class="small fw-bold">NIP</label>

                            <input type="text"
                                   name="nip"
                                   value="{{ $u->nip }}"
                                   class="form-control bg-light border-0 py-2"
                                   required>
                        </div>

                        <div class="col-6 mb-3">
                            <label class="small fw-bold">Username</label>

                            <input type="text"
                                   value="{{ $u->username }}"
                                   class="form-control bg-light border-0 py-2"
                                   readonly>
                            <small class="text-muted d-block mt-2">Username hanya bisa diubah lewat notifikasi lupa akun.</small>
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">
                            Password
                        </label>

                        <input type="text"
                               value="Disembunyikan"
                               class="form-control bg-light border-0 py-2"
                               readonly>
                        <small class="text-muted d-block mt-2">Password hanya bisa diubah lewat notifikasi lupa akun.</small>
                    </div>

                    <div class="row">

                        <div class="col-6 mb-3">

                            <label class="small fw-bold">
                                Role
                            </label>

                            <select name="role"
                                    data-role-select
                                    class="form-select bg-light border-0 py-2">

                                <option value="admin" {{ $u->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="operator" {{ $u->role == 'operator' ? 'selected' : '' }}>Operator</option>
                                <option value="viewer" {{ $u->role == 'viewer' ? 'selected' : '' }}>Viewer</option>

                            </select>

                        </div>

                        <div class="col-6 mb-3">

                            <label class="small fw-bold">
                                Tim Kerja
                            </label>

                            <select name="id_timkerja"
                                    data-timkerja-select
                                    class="form-select bg-light border-0 py-2">

                                <option value="">-- Pilih --</option>

                                @foreach($timkerja as $t)
                                    <option value="{{ $t->id_timkerja }}"
                                        {{ $u->id_timkerja == $t->id_timkerja ? 'selected' : '' }}>
                                        {{ $t->nama_timkerja }}
                                    </option>
                                @endforeach

                            </select>

                            <small class="text-muted d-block mt-2" data-timkerja-help>
                                Tim kerja wajib dipilih untuk operator dan viewer.
                            </small>

                        </div>

                    </div>

                </div>

                <div class="modal-footer border-0 p-4 pt-0">

                    <button type="submit"
                            class="btn btn-primary w-100 py-3 fw-bold rounded-3">
                        UPDATE DATA
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
@endforeach
        </div>
        </div>
    </div>

</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade app-modal" id="modalTambah" tabindex="-1" data-app-modal>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header border-0 pt-4 px-4">

                <h5 class="fw-bold">
                    Tambah User Baru
                </h5>

                <button type="button"
                        class="btn-close"
                        data-app-modal-close>
                </button>

            </div>

            <form action="{{ route('admin.user.store') }}"
                  method="POST"
                  data-user-form="create">

                @csrf

                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="small fw-bold">Nama Lengkap</label>

                        <input type="text"
                               name="nama"
                               value="{{ old('nama') }}"
                               class="form-control bg-light border-0 py-2"
                               required>
                    </div>

                    <div class="row">

                        <div class="col-6 mb-3">
                            <label class="small fw-bold">NIP</label>

                            <input type="text"
                                   name="nip"
                                   value="{{ old('nip') }}"
                                   class="form-control bg-light border-0 py-2"
                                   required>
                        </div>

                        <div class="col-6 mb-3">
                            <label class="small fw-bold">Username</label>

                            <input type="text"
                                   name="username"
                                   value="{{ old('username') }}"
                                   class="form-control bg-light border-0 py-2"
                                   required>
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Password</label>

                        <input type="password"
                               name="password"
                               minlength="6"
                               class="form-control bg-light border-0 py-2"
                               required>
                    </div>

                    <div class="row">

                        <div class="col-6 mb-3">

                            <label class="small fw-bold">Role</label>

                            <select name="role"
                                    data-role-select
                                    class="form-select bg-light border-0 py-2">

                                <option value="admin" {{ old('role', 'viewer') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="operator" {{ old('role') == 'operator' ? 'selected' : '' }}>Operator</option>
                                <option value="viewer" {{ old('role', 'viewer') == 'viewer' ? 'selected' : '' }}>Viewer</option>

                            </select>

                        </div>

                        <div class="col-6 mb-3">

                            <label class="small fw-bold">
                                Tim Kerja
                            </label>

                            <select name="id_timkerja"
                                    data-timkerja-select
                                    class="form-select bg-light border-0 py-2">

                                <option value="">-- Pilih --</option>

                                @foreach($timkerja as $t)
                                    <option value="{{ $t->id_timkerja }}" {{ (string) old('id_timkerja') === (string) $t->id_timkerja ? 'selected' : '' }}>
                                        {{ $t->nama_timkerja }}
                                    </option>
                                @endforeach

                            </select>

                            <small class="text-muted d-block mt-2" data-timkerja-help>
                                Tim kerja wajib dipilih untuk operator dan viewer.
                            </small>

                        </div>

                    </div>

                </div>

                <div class="modal-footer border-0 p-4 pt-0">

                    <button type="submit"
                            class="btn btn-primary w-100 py-3 fw-bold rounded-3">
                        SIMPAN USER
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let appModalBackdrop = null;

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

document.addEventListener('DOMContentLoaded', function () {
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

    @if($errors->any())
        Swal.fire({
            title: 'Data user belum valid',
            html: `{!! collect($errors->all())->map(fn ($error) => '<div>' . e($error) . '</div>')->implode
            ('') !!}`,
            icon: 'warning',
            confirmButtonText: 'OK'
        });

        setTimeout(() => {
            if (document.getElementById('modalTambah').classList.contains('is-open')) {
                openAppModal('modalTambah');
            } else {
                const openModal = document.querySelector('[data-app-modal].is-open');
                if (!openModal) {
                    document.querySelectorAll('[data-app-modal]').forEach(modal => {
                        if (modal.id.startsWith('modalEdit')) {
                            openAppModal(modal.id);
                        }
                    });
                }
            }
        }, 100);
    @endif

    document.querySelectorAll('[data-user-form]').forEach(form => {
        const roleSelect = form.querySelector('[data-role-select]');
        const timkerjaSelect = form.querySelector('[data-timkerja-select]');
        const timkerjaHelp = form.querySelector('[data-timkerja-help]');

       const syncTimkerjaState = (showPopup = false) => {
    const role = roleSelect.value;

    const isAdmin = role === 'admin';
    const needsTimkerja = role === 'operator' || role === 'viewer';

    timkerjaSelect.disabled = !needsTimkerja;
    timkerjaSelect.required = needsTimkerja;

    if (isAdmin) {
        timkerjaHelp.textContent = 'Role admin tidak perlu tim kerja.';
    } else {
        timkerjaHelp.textContent = 'Tim kerja wajib untuk operator & viewer.';
    }
};
        syncTimkerjaState();

        roleSelect?.addEventListener('change', function () {
            syncTimkerjaState(true);
        });

        form.addEventListener('submit', function (event) {
            const nama = form.querySelector('[name="nama"]')?.value.trim() || '';
            const nip = form.querySelector('[name="nip"]')?.value.trim() || '';
            const passwordInput = form.querySelector('[name="password"]');
            const password = passwordInput?.value || '';
            const role = roleSelect?.value || '';
            const timkerja = timkerjaSelect?.value || '';
            const formType = form.dataset.userForm;

            if (!nama || !nip) {
                event.preventDefault();

                Swal.fire({
                    title: 'Data belum lengkap',
                    text: formType === 'create' ? 'Nama, username, dan NIP wajib diisi.' : 'Nama dan NIP wajib diisi.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });

                return;
            }

            if (formType === 'create' && password.length < 6) {
                event.preventDefault();

                Swal.fire({
                    title: 'Password terlalu pendek',
                    text: 'Password minimal 6 karakter.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });

                passwordInput?.focus();
                return;
            }

            if ((role === 'operator' || role === 'viewer') && !timkerja) {
                event.preventDefault();

                Swal.fire({
                    title: 'Tim kerja belum dipilih',
                    text: 'Untuk role operator dan viewer, tim kerja wajib diisi.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });

                timkerjaSelect?.focus();
                return;
            }

        });
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(){
            let form = this.closest('form');

            Swal.fire({
                title:'Hapus user?',
                text:'Data akan dihapus permanen',
                icon:'warning',
                showCancelButton:true,
                confirmButtonText:'Ya',
                cancelButtonText:'Batal'
            }).then((result)=>{
                if(result.isConfirmed){
                    form.submit();
                }
            });
        });
    });
});

</script>
@endsection
