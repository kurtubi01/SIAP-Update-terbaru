@extends('layouts.sidebarmenu')

@section('content')
    @php($prefix = strtolower(Auth::user()->role) === 'admin' ? 'admin' : 'operator')
    @php($pageMode = $pageMode ?? 'create')
    @php($isEdit = $pageMode === 'edit')
    @php($formAction = $isEdit ? route($prefix . '.monitoring.update', $monitoring->id_monitoring) : route($prefix . '.monitoring.store'))
    @php($selectedSop = $selectedSop ?? null)
    @php($lockedSop = $isEdit || $selectedSop)

    <style>
        .monitoring-shell {
            font-family: 'Inter', 'Nunito', sans-serif;
        }

        .monitoring-toolbar {
            margin-bottom: 1.5rem;
        }

        .monitoring-card {
            background: #ffffff;
            border: 1px solid rgba(216, 228, 240, 0.95);
            border-radius: 24px;
            box-shadow: 0 24px 52px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .monitoring-card .card-body {
            padding: 1.9rem;
        }

        .form-block {
            padding: 1.1rem 1.2rem;
            border: 1px solid #e4ecf5;
            border-radius: 18px;
            background: #fcfdff;
            margin-bottom: 1.25rem;
        }

        .monitoring-card .form-label {
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.7rem;
        }

        .monitoring-card .form-select,
        .monitoring-card .form-control {
            border-radius: 14px;
            border: 1px solid #d7e3f0;
            padding: 0.9rem 1rem;
            box-shadow: none;
            color: #334155;
            background: #ffffff;
        }

        .monitoring-card .form-select:focus,
        .monitoring-card .form-control:focus {
            border-color: #8db8ff;
            box-shadow: 0 0 0 0.22rem rgba(59, 130, 246, 0.12);
        }

        .monitoring-table-wrap {
            border-radius: 22px;
            overflow: hidden;
            border: 1px solid #d8e4f0;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .monitoring-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
            font-size: 0.93rem;
        }

        .monitoring-table th,
        .monitoring-table td {
            border-right: 1px solid #d8e4f0;
            border-bottom: 1px solid #d8e4f0;
            padding: 1rem 0.9rem;
            vertical-align: top;
            color: #1e293b;
            background: #ffffff;
        }

        .monitoring-table th:last-child,
        .monitoring-table td:last-child {
            border-right: 0;
        }

        .monitoring-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .monitoring-table thead th {
            background: linear-gradient(180deg, #eaf3ff 0%, #f8fbff 100%);
            text-align: center;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            color: #16325c;
        }

        .monitoring-table .col-no {
            width: 70px;
            text-align: center;
        }

        .monitoring-table .col-procedure {
            width: 24%;
        }

        .monitoring-table .col-check {
            width: 22%;
        }

        .monitoring-table .col-result {
            width: 24%;
        }

        .monitoring-table .col-action {
            width: 24%;
        }

        .row-number {
            display: inline-flex;
            width: 36px;
            height: 36px;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 800;
        }

        .cell-label {
            display: block;
            margin-bottom: 0.55rem;
            font-size: 0.77rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            color: #64748b;
            text-transform: uppercase;
        }

        .choice-stack {
            display: grid;
            gap: 0.85rem;
        }

        .choice-item {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.9rem 0.95rem;
            border-radius: 16px;
            border: 1px solid #d7e3f0;
            background: #f8fbff;
            font-weight: 700;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .choice-item:hover {
            border-color: #93c5fd;
            background: #eef6ff;
        }

        .choice-item input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .choice-item.is-active {
            border-color: #2563eb;
            background: #eaf2ff;
            box-shadow: 0 12px 22px rgba(37, 99, 235, 0.08);
        }

        .choice-box {
            width: 20px;
            height: 20px;
            border: 2px solid #94a3b8;
            border-radius: 999px;
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .choice-box::after {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #2563eb;
            transform: scale(0);
            transition: transform 0.2s ease;
        }

        .choice-item.is-active .choice-box {
            border-color: #2563eb;
            background: #ffffff;
        }

        .choice-item.is-active .choice-box::after {
            transform: scale(1);
        }

        .cell-textarea {
            min-height: 190px;
            resize: vertical;
            background: #fcfdff;
        }

        .support-note {
            margin-top: 1rem;
            border-radius: 18px;
            background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);
            border: 1px solid #dbeafe;
            padding: 1rem 1.1rem;
            color: #475569;
            font-size: 0.88rem;
            line-height: 1.7;
        }

        .action-row {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.35rem;
            flex-wrap: wrap;
            padding-top: 1rem;
            border-top: 1px solid #e7eef7;
        }

        .btn-soft {
            border-radius: 14px;
            padding: 0.85rem 1.45rem;
            font-weight: 800;
        }

        .page-action-btn {
            border-radius: 14px;
            padding: 0.85rem 1.45rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            transition: all 0.2s ease;
        }

        .page-action-btn.btn-back {
            border: 1px solid #d7e2ef;
            background: #ffffff;
            color: #475569;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.05);
        }

        .page-action-btn.btn-back:hover {
            background: #f8fbff;
            color: #0f172a;
            border-color: #bfd3ea;
            transform: translateY(-1px);
        }

        .page-action-btn.btn-cancel {
            border: 1px solid #d7e2ef;
            background: #f8fafc;
            color: #475569;
        }

        .page-action-btn.btn-cancel:hover {
            background: #eef2f7;
            color: #0f172a;
        }

        .page-action-btn.btn-submit {
            border: 0;
            color: #ffffff;
            background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.18);
        }

        .page-action-btn.btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(37, 99, 235, 0.22);
        }

        .locked-sop-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
            padding: 1rem 1.1rem;
        }

        .locked-sop-name {
            font-weight: 800;
            color: #0f172a;
        }

        .locked-sop-meta {
            color: #64748b;
            font-size: 0.88rem;
            margin-top: 0.2rem;
        }

        @media (max-width: 1200px) {
            .monitoring-table {
                min-width: 1080px;
            }
        }

        @media (max-width: 768px) {
            .monitoring-card .card-body {
                padding: 1rem;
            }
        }
    </style>

    <div class="container-fluid monitoring-shell py-2">
        <div class="monitoring-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="fw-bold mb-1">{{ $isEdit ? 'Edit Monitoring SOP' : 'Tambah Monitoring SOP' }}</h4>
                <p class="text-muted mb-0">Form monitoring dibuat lebih rapi dengan tabel berwarna lembut agar mudah dibaca petugas.</p>
            </div>

            <a href="{{ route($prefix . '.monitoring.index') }}" class="btn page-action-btn btn-back">
                <i class="bi bi-arrow-left"></i>Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="monitoring-card">
            <div class="card-body">
                <form method="POST" action="{{ $formAction }}" id="monitoringForm">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif
                    <input type="hidden" name="redirect_route" value="monitoring.index">

                    <div class="form-block">
                        <label class="form-label">SOP</label>
                        @if($lockedSop)
                            @php($activeSop = $selectedSop ?: $monitoring?->sop)
                            <input type="hidden" name="id_sop" value="{{ old('id_sop', $activeSop->id_sop ?? '') }}">
                            <div class="locked-sop-card">
                                <div>
                                    <div class="locked-sop-name">{{ $activeSop->nama_sop ?? '-' }}</div>
                                    <div class="locked-sop-meta">
                                        Nomor SOP: {{ $activeSop->nomor_sop ?? '-' }}
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Otomatis terpilih</span>
                            </div>
                        @else
                            <select name="id_sop" class="form-select" required>
                                <option value="">Pilih SOP</option>
                                @foreach ($sops as $sop)
                                    <option value="{{ $sop->id_sop }}"
                                        {{ (string) old('id_sop', $monitoring->id_sop ?? '') === (string) $sop->id_sop ? 'selected' : '' }}>
                                        {{ $sop->nama_sop }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <input type="hidden"
                        name="kriteria_penilaian"
                        id="kriteria_penilaian"
                        value="{{ old('kriteria_penilaian', $monitoring->kriteria_penilaian ?? '') }}">

                    <div class="table-responsive monitoring-table-wrap">
                        <table class="monitoring-table">
                            <thead>
                                <tr>
                                    <th class="col-no">No</th>
                                    <th class="col-procedure">Prosedur</th>
                                    <th class="col-check">Penilaian terhadap penerapan</th>
                                    <th class="col-result">Catatan hasil</th>
                                    <th class="col-action">Tindakan yang harus diambil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        <span class="row-number">1</span>
                                    </td>
                                    <td>
                                        <textarea id="prosedur" name="prosedur" class="form-control cell-textarea" rows="7" required>{{ old('prosedur', $monitoring->prosedur ?? '') }}</textarea>
                                    </td>
                                    <td>
                                        <label class="cell-label">Pilihan Penilaian</label>
                                        <div class="choice-stack">
                                            @foreach (['Berjalan dengan baik', 'Tidak berjalan dengan baik'] as $choice)
                                                @php($selectedChoice = old('kriteria_penilaian', $monitoring->kriteria_penilaian ?? ''))
                                                <label class="choice-item {{ $selectedChoice === $choice ? 'is-active' : '' }}">
                                                    <input type="radio"
                                                        class="monitoring-choice"
                                                        name="monitoring_choice"
                                                        value="{{ $choice }}"
                                                        {{ $selectedChoice === $choice ? 'checked' : '' }}>
                                                    <span class="choice-box"></span>
                                                    <span>{{ $choice }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <textarea id="hasil_monitoring" name="hasil_monitoring" class="form-control cell-textarea" rows="7" required>{{ old('hasil_monitoring', $monitoring->hasil_monitoring ?? '') }}</textarea>
                                    </td>
                                    <td>
                                        <textarea id="tindakan" name="tindakan" class="form-control cell-textarea" rows="7" required>{{ old('tindakan', $monitoring->tindakan ?? '') }}</textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <label class="form-label" for="catatan">Catatan Tambahan</label>
                        <textarea id="catatan" name="catatan" class="form-control" rows="3">{{ old('catatan', $monitoring->catatan ?? '') }}</textarea>
                    </div>

                    <div class="support-note">
                        Isi kolom prosedur dengan langkah yang sedang dipantau, lalu tulis hasil observasi pada catatan hasil dan tindakan lanjutan agar lembar monitoring lebih mudah ditinjau ulang.
                    </div>

                    <div class="action-row">
                        <a href="{{ route($prefix . '.monitoring.index') }}"
                            class="btn page-action-btn btn-cancel">Batal</a>
                        <button type="submit" class="btn page-action-btn btn-submit">
                            <i class="bi bi-check2-circle"></i>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Monitoring' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hiddenInput = document.getElementById('kriteria_penilaian');
            const choices = document.querySelectorAll('.monitoring-choice');
            const form = document.getElementById('monitoringForm');

            function syncChoice() {
                const selected = Array.from(choices).find((choice) => choice.checked);
                hiddenInput.value = selected ? selected.value : '';

                choices.forEach((choice) => {
                    choice.closest('.choice-item')?.classList.toggle('is-active', choice.checked);
                });
            }

            choices.forEach((choice) => {
                choice.addEventListener('change', syncChoice);
            });

            form?.addEventListener('submit', function(event) {
                syncChoice();

                if (!hiddenInput.value) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Penilaian belum dipilih',
                        text: 'Pilih penilaian monitoring terlebih dahulu.',
                        confirmButtonText: 'OK'
                    });
                }
            });

            syncChoice();
        });
    </script>
@endsection
