@extends('guru.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Input Nilai: {{ $student->name }}</h4>
                        <small>NISN: {{ $student->nisn ?? '-' }} | Kelas: {{ $student->classRoom->name ?? '-' }} | {{ $academicYear->year }} - {{ $academicYear->semester }}</small>
                    </div>
                    <div>
                        @if ($isLocked)
                            <span class="badge bg-success fs-6">TERKUNCI</span>
                        @else
                            <span class="badge bg-secondary fs-6">DRAFT</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                    @endif

                    <ul class="nav nav-tabs mb-4" id="inputNilaiTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="akademik-tab" data-bs-toggle="tab" data-bs-target="#akademik" type="button" role="tab">
                                Akademik
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="kompetensi-tab" data-bs-toggle="tab" data-bs-target="#kompetensi" type="button" role="tab">
                                Kompetensi
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="inputNilaiTabContent">
                        <!-- Tab Akademik -->
                        <div class="tab-pane fade show active" id="akademik" role="tabpanel">
                            <form action="{{ route('guru.input-nilai.update', $student->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <h5 class="mb-3">Nilai Akademik</h5>
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered table-striped small">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40%">Mata Pelajaran</th>
                                                <th style="width: 10%">KKM</th>
                                                <th style="width: 20%">Nilai Angka</th>
                                                <th style="width: 30%">Nilai Huruf</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($grades as $grade)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $grade->subject->name }}</strong>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="grades[{{ $grade->subject_id }}][subject_id]" value="{{ $grade->subject_id }}">
                                                        <input type="number" class="form-control form-control-sm" value="{{ $grade->kkm }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               name="grades[{{ $grade->subject_id }}][score_number]"
                                                               class="form-control form-control-sm score-input"
                                                               min="0" max="100"
                                                               value="{{ $grade->score_number ?? '' }}"
                                                               @if ($isLocked) disabled @endif
                                                               data-score-text-target="score-text-{{ $grade->subject_id }}"
                                                               placeholder="0-100">
                                                    </td>
                                                    <td>
                                                        <input type="text"
                                                               class="form-control form-control-sm score-text"
                                                               id="score-text-{{ $grade->subject_id }}"
                                                               value="{{ $grade->score_text ?? '' }}"
                                                               readonly>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <hr>

                                <h5 class="mb-3">Kepribadian</h5>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label small">Sikap</label>
                                        <select name="sikap" class="form-control form-control-sm" @if($isLocked) disabled @endif>
                                            <option value="">-- Pilih --</option>
                                            <option value="A" {{ $attitudes->sikap === 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ $attitudes->sikap === 'B' ? 'selected' : '' }}>B</option>
                                            <option value="C" {{ $attitudes->sikap === 'C' ? 'selected' : '' }}>C</option>
                                            <option value="D" {{ $attitudes->sikap === 'D' ? 'selected' : '' }}>D</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Kerajinan</label>
                                        <select name="kerajinan" class="form-control form-control-sm" @if($isLocked) disabled @endif>
                                            <option value="">-- Pilih --</option>
                                            <option value="A" {{ $attitudes->kerajinan === 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ $attitudes->kerajinan === 'B' ? 'selected' : '' }}>B</option>
                                            <option value="C" {{ $attitudes->kerajinan === 'C' ? 'selected' : '' }}>C</option>
                                            <option value="D" {{ $attitudes->kerajinan === 'D' ? 'selected' : '' }}>D</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Kebersihan</label>
                                        <select name="kebersihan" class="form-control form-control-sm" @if($isLocked) disabled @endif>
                                            <option value="">-- Pilih --</option>
                                            <option value="A" {{ $attitudes->kebersihan === 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ $attitudes->kebersihan === 'B' ? 'selected' : '' }}>B</option>
                                            <option value="C" {{ $attitudes->kebersihan === 'C' ? 'selected' : '' }}>C</option>
                                            <option value="D" {{ $attitudes->kebersihan === 'D' ? 'selected' : '' }}>D</option>
                                        </select>
                                    </div>
                                </div>

                                <h5 class="mb-3">Absensi</h5>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label small">Sakit</label>
                                        <input type="number" name="sick" class="form-control form-control-sm" min="0" value="{{ $attitudes->sick ?? '' }}" @if($isLocked) disabled @endif>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Izin</label>
                                        <input type="number" name="permission" class="form-control form-control-sm" min="0" value="{{ $attitudes->permission ?? '' }}" @if($isLocked) disabled @endif>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Tanpa Keterangan</label>
                                        <input type="number" name="absent" class="form-control form-control-sm" min="0" value="{{ $attitudes->absent ?? '' }}" @if($isLocked) disabled @endif>
                                    </div>
                                </div>

                                @if (!$isLocked)
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Simpan Draft
                                        </button>
                                        <button type="button" class="btn btn-success" disabled title="Fitur Submit & Kunci akan tersedia di tahap berikutnya">
                                            <i class="bi bi-lock"></i> Submit & Kunci
                                        </button>
                                        <a href="{{ route('guru.input-nilai.index') }}" class="btn btn-outline-secondary">Kembali</a>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="bi bi-lock-fill"></i> Nilai sudah terkunci dan tidak dapat diubah.
                                    </div>
                                @endif
                            </form>
                        </div>

                        <!-- Tab Kompetensi -->
                        <div class="tab-pane fade" id="kompetensi" role="tabpanel">
                            <form action="{{ route('guru.input-nilai.update', $student->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <h5 class="mb-3">Nilai Kompetensi</h5>
                                <p class="text-muted small mb-4">Pilih satu tingkat kemampuan untuk setiap indikator: <strong>M</strong> = Mandiri, <strong>AV</strong> = Dengan Bantuan Verbal, <strong>AF</strong> = Dengan Bantuan Fisik, <strong>AP</strong> = Dengan Bantuan Penuh. Kosongkan jika belum dinilai.</p>

                                @foreach ($indicatorsByDomain as $domainId => $data)
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <strong>{{ $data['domain']->name }}</strong>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped small mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width: 60%">Indikator</th>
                                                            <th style="width: 10%" class="text-center">M</th>
                                                            <th style="width: 10%" class="text-center">AV</th>
                                                            <th style="width: 10%" class="text-center">AF</th>
                                                            <th style="width: 10%" class="text-center">AP</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($data['indicators'] as $indicator)
                                                            @php
                                                                $compGrade = $competencyGrades->get($indicator->id);
                                                                $currentValue = $compGrade->value ?? '';
                                                                $isCompLocked = $compGrade->is_submitted ?? false;
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    <strong>{{ $indicator->name }}</strong>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="competencies[{{ $indicator->id }}]"
                                                                               value="M"
                                                                               {{ $currentValue === 'M' ? 'checked' : '' }}
                                                                               @if($isLocked || $isCompLocked) disabled @endif>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="competencies[{{ $indicator->id }}]"
                                                                               value="AV"
                                                                               {{ $currentValue === 'AV' ? 'checked' : '' }}
                                                                               @if($isLocked || $isCompLocked) disabled @endif>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="competencies[{{ $indicator->id }}]"
                                                                               value="AF"
                                                                               {{ $currentValue === 'AF' ? 'checked' : '' }}
                                                                               @if($isLocked || $isCompLocked) disabled @endif>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="competencies[{{ $indicator->id }}]"
                                                                               value="AP"
                                                                               {{ $currentValue === 'AP' ? 'checked' : '' }}
                                                                               @if($isLocked || $isCompLocked) disabled @endif>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <hr>

                                @if (!$isLocked)
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Simpan Draft
                                        </button>
                                        <a href="{{ route('guru.input-nilai.index') }}" class="btn btn-outline-secondary">Kembali</a>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="bi bi-lock-fill"></i> Nilai sudah terkunci dan tidak dapat diubah.
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Alpine.js component for auto-converting score to text
    document.addEventListener('alpine:init', () => {
        Alpine.data('gradeConverter', () => ({
            async convertScore(score, targetId) {
                if (score === '' || score === null) {
                    document.getElementById(targetId).value = '';
                    return;
                }
                try {
                    const response = await fetch(`/guru/grade-conversion/${score}`);
                    const data = await response.json();
                    document.getElementById(targetId).value = data.text;
                } catch (e) {
                    console.error('Gagal konversi nilai:', e);
                }
            }
        }));
    });
</script>
@endpush