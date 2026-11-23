@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0">Konversi Nilai</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                    @endif

                    <h5>Daftar Konversi Nilai (Urutan: Min Score ASC)</h5>
                    <div class="table-responsive">
                        <table class="table table-striped small">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Min Score</th>
                                    <th>Max Score</th>
                                    <th>Label Teks</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($conversions as $index => $conversion)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $conversion->min_score }}</td>
                                    <td>{{ $conversion->max_score }}</td>
                                    <td>{{ $conversion->text_label }}</td>
                                    <td>
                                        <a href="{{ route('admin.grade-conversions.edit', $conversion->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mt-4">Tambah Konversi Baru</h5>
                    <p class="text-muted small">Rentang nilai tidak boleh tumpang tindih (overlap) dengan data yang sudah ada.</p>
                    <form action="{{ route('admin.grade-conversions.store') }}" method="POST" class="mt-3">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="min_score" class="form-label small">Min Score</label>
                                <input type="number" name="min_score" id="min_score" class="form-control" min="0" max="100" required>
                            </div>
                            <div class="col-md-4">
                                <label for="max_score" class="form-label small">Max Score</label>
                                <input type="number" name="max_score" id="max_score" class="form-control" min="0" max="100" required>
                            </div>
                            <div class="col-md-4">
                                <label for="text_label" class="form-label small">Label Teks</label>
                                <input type="text" name="text_label" id="text_label" class="form-control" maxlength="50" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">Simpan Konversi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection