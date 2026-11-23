@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Edit Kelas: {{ $class->name }}</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif

                    <form action="{{ route('admin.classes.update', $class->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="name" class="form-label small">Nama Kelas</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $class->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="academic_year_id" class="form-label small">Tahun Ajaran</label>
                                <select name="academic_year_id" id="academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Tahun Ajaran --</option>
                                    @foreach ($academicYears as $ay)
                                        <option value="{{ $ay->id }}" {{ old('academic_year_id', $class->academic_year_id) == $ay->id ? 'selected' : '' }}>
                                            {{ $ay->year }} - {{ $ay->semester }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="teacher_id" class="form-label small">Wali Kelas</label>
                                <select name="teacher_id" id="teacher_id" class="form-control @error('teacher_id') is-invalid @enderror">
                                    <option value="">-- Tidak Ada --</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_id', $class->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('teacher_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <h5>Assign Mata Pelajaran</h5>
                        <div class="row g-2 mb-4">
                            @foreach ($subjects as $subject)
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="subject_ids[]"
                                        value="{{ $subject->id }}"
                                        id="subject_{{ $subject->id }}"
                                        {{ $class->subjects->contains($subject->id) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="subject_{{ $subject->id }}">
                                        {{ $subject->name }} (KKM: {{ $subject->default_kkm }})
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <hr>

                        <h5>Assign Indikator Kompetensi</h5>
                        @foreach ($domains as $domain)
                        <div class="mb-3">
                            <h6 class="text-primary">{{ $domain->name }}</h6>
                            <div class="row g-2">
                                @foreach ($domain->indicators as $indicator)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="indicator_ids[]"
                                            value="{{ $indicator->id }}"
                                            id="indicator_{{ $indicator->id }}"
                                            {{ $class->competencyIndicators->contains($indicator->id) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="indicator_{{ $indicator->id }}">
                                            {{ $indicator->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                            <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection