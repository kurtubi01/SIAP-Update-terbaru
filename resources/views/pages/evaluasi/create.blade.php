@extends('layouts.sidebarmenu')

@section('content')
@php($prefix = strtolower(Auth::user()->role) === 'admin' ? 'admin' : 'operator')
@php($pageMode = $pageMode ?? 'create')
@php($isEdit = $pageMode === 'edit')
@php($formAction = $isEdit ? route($prefix . '.evaluasi.update', $evaluasi->id_evaluasi) : route($prefix . '.evaluasi.store'))
@php($selectedSop = $selectedSop ?? null)
@php($lockedSop = $isEdit || $selectedSop)

<style>
.evaluasi-shell {
    font-family: 'Inter', 'Nunito', sans-serif;
}

.evaluasi-card {
    border: 1px solid #dbe5f1;
    border-radius: 24px;
    background: #ffffff;
    box-shadow: 0 24px 52px rgba(15, 23, 42, 0.08);
    overflow: hidden;
}

.evaluasi-card .card-body {
    padding: 1.9rem;
}

.form-block {
    padding: 1.1rem 1.2rem;
    border: 1px solid #e4ecf5;
    border-radius: 18px;
    background: #fcfdff;
    margin-bottom: 1.25rem;
}

.evaluasi-card .form-label {
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 0.7rem;
}

.evaluasi-card .form-select,
.evaluasi-card .form-control {
    border-radius: 14px;
    border: 1px solid #d7e3f0;
    padding: 0.9rem 1rem;
    box-shadow: none;
    color: #334155;
}

.evaluasi-card .form-select:focus,
.evaluasi-card .form-control:focus {
    border-color: #8db8ff;
    box-shadow: 0 0 0 0.22rem rgba(59, 130, 246, 0.12);
}

.evaluasi-sheet {
    border: 1px solid #dbe5f1;
    border-radius: 24px;
    background: linear-gradient(180deg, #fffefb 0%, #ffffff 100%);
    overflow: hidden;
}

.evaluasi-sheet-header {
    padding: 1.6rem 1.4rem 1.2rem;
    text-align: center;
    border-bottom: 1px solid #e5ecf5;
    background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
}

.evaluasi-sheet-header h5 {
    font-weight: 800;
    margin-bottom: 0.35rem;
    color: #0f172a;
}

.evaluasi-sheet-header p {
    margin-bottom: 0;
    color: #64748b;
    font-size: 0.9rem;
}

.evaluasi-sheet-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.evaluasi-sheet-table th,
.evaluasi-sheet-table td {
    border: 1px solid #d9e4ef;
    padding: 0.75rem 0.6rem;
    vertical-align: middle;
}

.evaluasi-sheet-table thead th {
    background: #edf4ff;
    color: #17335c;
    font-size: 0.78rem;
    font-weight: 800;
    text-align: center;
}

.evaluasi-sheet-table .col-no {
    width: 58px;
    text-align: center;
}

.evaluasi-sheet-table .criteria-col {
    width: 34%;
    font-weight: 700;
    color: #0f172a;
    background: #fbfdff;
}

.scale-cell {
    text-align: center;
    background: #ffffff;
}

.scale-input {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #2563eb;
}

.scale-note {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    min-width: 38px;
    height: 26px;
    color: #64748b;
    font-weight: 700;
    font-size: 0.82rem;
}

.header-code {
    display: block;
    margin-top: 0.28rem;
    color: #64748b;
    font-size: 0.76rem;
    font-weight: 700;
}

.criteria-text {
    line-height: 1.55;
}

.empty-score {
    color: #cbd5e1;
}

.legend-box {
    padding: 1rem 1.15rem;
    background: #fcfdff;
    border-top: 1px solid #e5ecf5;
    color: #475569;
    line-height: 1.75;
    font-size: 0.9rem;
}

.sign-box {
    margin-top: 1rem;
    display: flex;
    justify-content: flex-end;
}

.sign-card {
    width: min(100%, 300px);
    border: 1px dashed #cbd5e1;
    border-radius: 18px;
    padding: 1rem 1.1rem;
    color: #475569;
    background: #ffffff;
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
    .evaluasi-sheet-table {
        min-width: 980px;
    }
}

@media (max-width: 768px) {
    .evaluasi-card .card-body {
        padding: 1rem;
    }
}
</style>

<div class="container-fluid evaluasi-shell py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1">{{ $isEdit ? 'Edit Evaluasi SOP' : 'Tambah Evaluasi SOP' }}</h4>
            <p class="text-muted mb-0">Tampilan evaluasi disusun mengikuti pola lembar evaluasi pada dokumen contoh agar lebih formal dan rapi.</p>
        </div>

        <a href="{{ route($prefix . '.evaluasi.index') }}" class="btn page-action-btn btn-back">
            <i class="bi bi-arrow-left"></i>Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="evaluasi-card">
        <div class="card-body">
            <form method="POST" action="{{ $formAction }}" id="evaluasiForm">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif
                <input type="hidden" name="redirect_route" value="evaluasi.index">

                <div class="form-block">
                    <label class="form-label">SOP</label>
                    @if($lockedSop)
                        @php($activeSop = $selectedSop ?: $evaluasi?->sop)
                        <input type="hidden" name="id_sop" value="{{ old('id_sop', $activeSop->id_sop ?? '') }}">
                        <div class="locked-sop-card">
                            <div>
                                <div class="locked-sop-name">{{ $activeSop->nama_sop ?? '-' }}</div>
                                <div class="locked-sop-meta">Nomor SOP: {{ $activeSop->nomor_sop ?? '-' }}</div>
                            </div>
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Otomatis terpilih</span>
                        </div>
                    @else
                        <select name="id_sop" class="form-select" required {{ $sops->isEmpty() ? 'disabled' : '' }}>
                            <option value="">Pilih SOP yang sudah dimonitoring</option>
                            @foreach($sops as $sop)
                                <option value="{{ $sop->id_sop }}" {{ (string) old('id_sop', $evaluasi->id_sop ?? '') === (string) $sop->id_sop ? 'selected' : '' }}>
                                    {{ $sop->nama_sop }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    <div class="text-muted small mt-2">
                        {{ $sops->isEmpty() ? 'Belum ada SOP yang sudah dimonitoring, jadi evaluasi belum bisa dibuat.' : 'Daftar SOP pada evaluasi dibatasi hanya untuk SOP yang sudah memiliki data monitoring.' }}
                    </div>
                </div>

                <div class="table-responsive evaluasi-sheet">
                    <div class="evaluasi-sheet-header">
                        <h5>LEMBAR EVALUASI SOP AP</h5>
                        <p>Badan Pusat Statistik Provinsi Banten</p>
                    </div>

                    <table class="evaluasi-sheet-table">
                        <thead>
                            <tr>
                                <th class="col-no" rowspan="2">No</th>
                                <th rowspan="2">Penilaian</th>
                                <th colspan="6">SOP AP (Nomor)</th>
                            </tr>
                            <tr>
                                @for ($score = 1; $score <= 6; $score++)
                                    <th>
                                        <span>{{ $score }}</span>
                                        <span class="header-code">({{ $score + 2 }})</span>
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kriteriaOptions as $index => $kriteria)
                                @php($selected = in_array($kriteria, old('kriteria_evaluasi', $evaluasi->kriteria_evaluasi ?? []), true))
                                @php($selectedScore = old('score_map.' . $index, $selected ? (string) ($index + 1) : ''))
                                <tr>
                                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                    <td class="criteria-col">
                                        <div class="criteria-text">{{ $kriteria }}</div>
                                    </td>
                                    @for ($score = 1; $score <= 6; $score++)
                                        <td class="scale-cell">
                                            <input type="radio"
                                                   name="score_map[{{ $index }}]"
                                                   class="scale-input evaluasi-score"
                                                   data-kriteria="{{ $kriteria }}"
                                                   value="{{ $score }}"
                                                   {{ (string) $selectedScore === (string) $score ? 'checked' : '' }}>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="legend-box">
                        <div><strong>Keterangan:</strong> nomor di atas mengikuti kolom SOP AP, sedangkan angka dalam kurung menandai nomor penilaian pada dokumen acuan.</div>
                        <div><strong>Petunjuk:</strong> pilih satu kotak pada setiap baris penilaian yang ingin dimasukkan ke evaluasi.</div>
                    </div>
                </div>

                <div id="evaluasiCriteriaHidden"></div>

                <div class="mt-4">
                    <label class="form-label fw-semibold">Hasil Evaluasi</label>
                    <textarea name="hasil_evaluasi" class="form-control" rows="4" required>{{ old('hasil_evaluasi', $evaluasi->hasil_evaluasi ?? '') }}</textarea>
                </div>

                <div class="mt-4">
                    <label class="form-label fw-semibold">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $evaluasi->catatan ?? '') }}</textarea>
                </div>

                <div class="sign-box">
                    <div class="sign-card">
                        <div class="fw-semibold">Tempat & Tanggal</div>
                        <div>Ketua Tim,</div>
                        <div class="mt-4">Ttd.</div>
                        <div class="mt-3 fw-semibold">NAMA JELAS</div>
                    </div>
                </div>

                <div class="action-row">
                    <a href="{{ route($prefix . '.evaluasi.index') }}" class="btn page-action-btn btn-cancel">Batal</a>
                    <button type="submit" class="btn page-action-btn btn-submit" {{ $sops->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-check2-circle"></i>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Evaluasi' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('evaluasiForm');
    const scores = document.querySelectorAll('.evaluasi-score');
    const hiddenContainer = document.getElementById('evaluasiCriteriaHidden');

    function syncHiddenCriteria() {
        hiddenContainer.innerHTML = '';

        const selectedCriteria = Array.from(scores)
            .filter((input) => input.checked)
            .map((input) => input.dataset.kriteria);

        selectedCriteria.forEach((kriteria) => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'kriteria_evaluasi[]';
            hiddenInput.value = kriteria;
            hiddenContainer.appendChild(hiddenInput);
        });

        return selectedCriteria.length > 0;
    }

    form?.addEventListener('submit', function (event) {
        const hasChecked = syncHiddenCriteria();

        if (!hasChecked) {
            event.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Kriteria belum dipilih',
                text: 'Pilih minimal satu kriteria evaluasi terlebih dahulu.',
                confirmButtonText: 'OK'
            });
        }
    });

    scores.forEach((input) => {
        input.addEventListener('change', syncHiddenCriteria);
    });

    syncHiddenCriteria();
});
</script>
@endsection
