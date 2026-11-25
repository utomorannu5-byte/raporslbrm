@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Tambah Tahun Ajaran</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif

                    <form action="{{ route('admin.academic-years.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="year" class="form-label small">Tahun Ajaran</label>
                            <input type="text" name="year" id="year" class="form-control @error('year') is-invalid @enderror"
                                value="{{ old('year') }}" required placeholder="Contoh: 2025/2026">
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Format: XXXX/XXXX (contoh: 2025/2026)</div>
                        </div>

                        <div class="mb-3">
                            <label for="semester" class="form-label small">Semester</label>
                            <select name="semester" id="semester" class="form-control @error('semester') is-invalid @enderror" required>
                                <option value="">-- Pilih Semester --</option>
                                <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                            @error('semester')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input @error('is_active') is-invalid @enderror"
                                value="1" {{ old('is_active') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="is_active">Set sebagai Tahun Ajaran Aktif</label>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Jika dicentang, tahun ajaran lain akan otomatis non-aktif.</div>
                        </form-check>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-success">Simpan Tahun Ajaran</button>
                            <a href="{{ route('admin.academic-years.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>@endsection