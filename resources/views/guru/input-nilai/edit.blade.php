@extends('guru.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="mb-0">Input Nilai: {{ $student->name }}</h4>
                        <small>NISN: {{ $student->nisn ?? '-' }} | Kelas: {{ $student->classRoom->name ?? '-' }} | {{ $academicYear->year }} - {{ $academicYear->semester }}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if ($isLocked)
                            <span class="badge bg-success fs-6">TERKUNCI</span>
                            <span class="badge bg-primary fs-6">FINAL</span>
                        @else
                            <span class="badge bg-secondary fs-6">DRAFT</span>
                        @endif
                        <a href="{{ route('guru.report.academic.pdf', $student->id) }}" class="btn btn-primary btn-sm" target="_blank">
                            Cetak PDF
                        </a>
                        <a href="{{ route('guru.report.academic.word', $student->id) }}" class="btn btn-outline-primary btn-sm">
                            Unduh Word
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mb-3">
                            <strong>Periksa kembali isian Anda:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
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
                                @if ($grades->isEmpty())
                                    <div class="alert alert-warning">
                                        Belum ada mata pelajaran yang di-assign ke kelas ini.
                                        Hubungi admin untuk mengatur mapel pada menu <strong>Kelas</strong>.
                                    </div>
                                @else
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
                                @endif

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
                                            Simpan Draft
                                        </button>
                                        <button type="submit" form="submitForm" class="btn btn-success btn-submit">
                                            Submit &amp; Kunci
                                        </button>
                                        <a href="{{ route('guru.input-nilai.index') }}" class="btn btn-outline-secondary">Kembali</a>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        Nilai sudah terkunci dan tidak dapat diubah.
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

                                @if ($indicatorsByDomain->isEmpty())
                                    <div class="alert alert-warning">
                                        Belum ada indikator kompetensi yang di-assign ke kelas ini.
                                        Hubungi admin untuk mengatur indikator pada menu <strong>Kelas</strong>.
                                    </div>
                                @endif

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
                                            Simpan Draft
                                        </button>
                                        <button type="submit" form="submitForm" class="btn btn-success btn-submit">
                                            Submit &amp; Kunci
                                        </button>
                                        <a href="{{ route('guru.input-nilai.index') }}" class="btn btn-outline-secondary">Kembali</a>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        Nilai sudah terkunci dan tidak dapat diubah.
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>

                    @if (! $isLocked)
                        <form id="submitForm" action="{{ route('guru.input-nilai.submit', $student->id) }}" method="POST">
                            @csrf
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Konversi otomatis nilai angka -> teks (huruf) saat guru mengetik.
        var convertScore = async function (input) {
            var target = document.getElementById(input.dataset.scoreTextTarget);

            if (!target) {
                return;
            }

            var raw = input.value.trim();

            if (raw === '') {
                target.value = '';
                return;
            }

            var score = Number(raw);

            if (!Number.isInteger(score) || score < 0 || score > 100) {
                target.value = '';
                return;
            }

            try {
                var response = await fetch('{{ url('/guru/grade-conversion') }}/' + score, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    target.value = '';
                    return;
                }

                var data = await response.json();
                target.value = data.text || '';
            } catch (error) {
                console.error('Gagal konversi nilai:', error);
                target.value = '';
            }
        };

        document.querySelectorAll('.score-input').forEach(function (input) {
            input.addEventListener('input', function () {
                convertScore(input);
            });

            // Isi otomatis nilai huruf saat halaman dibuka.
            if (input.value.trim() !== '') {
                convertScore(input);
            }
        });

        // Konfirmasi sebelum submit & kunci.
        document.querySelectorAll('.btn-submit').forEach(function (button) {
            button.addEventListener('click', function (event) {
                var confirmed = window.confirm(
                    'Setelah submit, nilai akan TERKUNCI dan tidak bisa diubah lagi. ' +
                    'Pastikan draft sudah disimpan. Lanjutkan?'
                );

                if (!confirmed) {
                    event.preventDefault();
                }
            });
        });
    });
</script>
@endpush