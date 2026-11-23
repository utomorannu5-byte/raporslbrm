@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Edit Konversi Nilai</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('admin.grade-conversions.update', $conversion->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="min_score" class="form-label small">Min Score</label>
                            <input type="number" name="min_score" id="min_score" class="form-control @error('min_score') is-invalid @enderror"
                                value="{{ old('min_score', $conversion->min_score) }}" min="0" max="100" required>
                            @error('min_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="max_score" class="form-label small">Max Score</label>
                            <input type="number" name="max_score" id="max_score" class="form-control @error('max_score') is-invalid @enderror"
                                value="{{ old('max_score', $conversion->max_score) }}" min="0" max="100" required>
                            @error('max_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="text_label" class="form-label small">Label Teks</label>
                            <input type="text" name="text_label" id="text_label" class="form-control @error('text_label') is-invalid @enderror"
                                value="{{ old('text_label', $conversion->text_label) }}" maxlength="50" required>
                            @error('text_label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                            <a href="{{ route('admin.grade-conversions.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection