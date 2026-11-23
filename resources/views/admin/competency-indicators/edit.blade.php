@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Edit Indikator Kompetensi</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif

                    <form action="{{ route('admin.competency-indicators.update', $indicator->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="domain_id" class="form-label small">Domain Kompetensi</label>
                            <select name="domain_id" id="domain_id" class="form-control @error('domain_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Domain --</option>
                                @foreach ($domains as $domain)
                                    <option value="{{ $domain->id }}" {{ old('domain_id', $indicator->domain_id) == $domain->id ? 'selected' : '' }}>
                                        {{ $domain->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('domain_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label small">Nama Indikator</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $indicator->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label small">Urutan</label>
                            <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                                value="{{ old('sort_order', $indicator->sort_order) }}" min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                            <a href="{{ route('admin.competency-indicators.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection