@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Edit Tahun Ajaran</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif

                    <form action="{{ route('admin.academic-years.update', $academicYear->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="year" class="form-label small">Tahun Ajaran</label>
                            <input type="text" name="year" id="year" class="form-control @error('year') is-invalid @enderror"
                                value="{{ old('year', $academicYear->year ) }}" required>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="semester" class="form-label small">Semester</label>
                            <select name="semester" id="semester" class="form-control @error('semester') is-invalid @enderror" required>
                                <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                            @error('semester')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input @error('is_active') is-invalid @enderror"
                                {{ old('is_active') && $academicYear->is_active ? 'checked' : (old('is_active') ? 'checked' : '') }}>
                            <label class="form-check-label small" for="is_active">Tahun Ajaran Aktif</label>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </form-check>
                        {{-- Jika ini tahun ajaran yang aktif, cekbox otomatis centang --}}

                        <button type="submit" class="btn btn-primary mt-3">Simpan Perubahan</button>
                        <a href="{{ route('admin.academic-years') }}" class="btn btn-outline-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>