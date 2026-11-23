@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Indikator Kompetensi</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif

                    <h5>Daftar Indikator Kompetensi (Group by Domain)</h5>
                    @foreach ($domains as $domain)
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2">{{ $domain->name }} ({{ $domain->indicators->count() }} indikator)</h6>
                        <div class="table-responsive">
                            <table class="table table-striped small mt-2">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th>Nama Indikator</th>
                                        <th style="width: 10%">Urutan</th>
                                        <th style="width: 15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($domain->indicators as $index => $indicator)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $indicator->name }}</td>
                                        <td>{{ $indicator->sort_order }}</td>
                                        <td>
                                            <a href="{{ route('admin.competency-indicators.edit', $indicator->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach

                    <h5 class="mt-4">Tambah Indikator Baru</h5>
                    <form action="{{ route('admin.competency-indicators.store') }}" method="POST" class="mt-3">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="domain_id" class="form-label small">Domain Kompetensi</label>
                                <select name="domain_id" id="domain_id" class="form-control" required>
                                    <option value="">-- Pilih Domain --</option>
                                    @foreach (\App\Models\CompetencyDomain::orderBy('sort_order')->get() as $domain)
                                        <option value="{{ $domain->id }}">{{ $domain->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label small">Nama Indikator</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label for="sort_order" class="form-label small">Urutan</label>
                                <input type="number" name="sort_order" id="sort_order" class="form-control" min="0" value="0">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">Simpan Indikator</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection