@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Domain Kompetensi</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif

                    <h5>Daftar Domain Kompetensi</h5>
                    <div class="table-responsive">
                        <table class="table table-striped small">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Domain</th>
                                    <th>Urutan</th>
                                    <th>Jml Indikator</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($domains as $index => $domain)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $domain->name }}</td>
                                    <td>{{ $domain->sort_order }}</td>
                                    <td>{{ $domain->indicators_count }}</td>
                                    <td>
                                        <a href="{{ route('admin.competency-domains.edit', $domain->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mt-4">Tambah Domain Baru</h5>
                    <form action="{{ route('admin.competency-domains.store') }}" method="POST" class="mt-3">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label small">Nama Domain</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="sort_order" class="form-label small">Urutan</label>
                                <input type="number" name="sort_order" id="sort_order" class="form-control" min="0" value="0">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">Simpan Domain</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection