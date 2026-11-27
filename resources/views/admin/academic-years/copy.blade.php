@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="bi bi-copy"></i> Copy Data Tahun Ajaran</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                    @endif

                    <div class="alert alert-warning mb-4">
                        <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Peringatan Penting</h6>
                        <ul class="mb-0 small">
                            <li>Fitur ini akan menduplikasi <strong>kelas, siswa, assign mapel & indikator</strong> dari tahun ajaran asal ke tahun ajaran tujuan.</li>
                            <li><strong>NILAI TIDAK AKAN DICOPY</strong> — academic_grades, competency_grades, dan student_attitudes akan kosong (harus diisi ulang).</li>
                            <li>Tahun ajaran tujuan <strong>harus belum memiliki kelas</strong>. Jika sudah ada kelas, hapus terlebih dahulu.</li>
                            <li>Operasi ini menggunakan database transaction — jika gagal di tengah jalan, semua perubahan di-rollback.</li>
                        </ul>
                    </div>

                    <form action="{{ route('admin.academic-years.copy') }}" method="POST" onsubmit="return confirm('Yakin ingin copy data? Operasi ini akan menduplikasi kelas, siswa, dan assign mapel/indikator ke tahun ajaran tujuan. NILAI TIDAK DICOPY.');">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="from_year_id" class="form-label small">Dari Tahun Ajaran (Sumber)</label>
                                <select name="from_year_id" id="from_year_id" class="form-control @error('from_year_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Tahun Ajaran Asal --</option>
                                    @foreach ($academicYears as $ay)
                                        <option value="{{ $ay->id }}" {{ old('from_year_id') == $ay->id ? 'selected' : '' }}>
                                            {{ $ay->year }} - {{ $ay->semester }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('from_year_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="to_year_id" class="form-label small">Ke Tahun Ajaran (Tujuan)</label>
                                <select name="to_year_id" id="to_year_id" class="form-control @error('to_year_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Tahun Ajaran Tujuan --</option>
                                    @foreach ($academicYears as $ay)
                                        <option value="{{ $ay->id }}" {{ old('to_year_id') == $ay->id ? 'selected' : '' }}>
                                            {{ $ay->year }} - {{ $ay->semester }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_year_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.academic-years.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-copy"></i> Copy Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection