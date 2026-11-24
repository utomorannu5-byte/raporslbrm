@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Tahun Ajaran</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif

                    <div class="mb-3">
                        <h5>Set Tahun Ajaran Aktif</h5>
                        <p class="text-muted small">Sistem hanya boleh memiliki 1 tahun ajaran aktif.</p>

                        <div class="row g-3">
                            @foreach ($academicYears as $ay)
                            <div class="col-md-4">
                                <div class="card @ay->is_active ? 'bg-success text-white' : 'bg-light'">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $ay->year }} - {{ $ay->semester }}</h6>
                                        <p class="card-text">
                                            @if ($ay->is_active)
                                                <span class="badge bg-white text-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Non-Aktif</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="card-footer">
                                        @if (!$ay->is_active)
                                            <a href="{{ route('admin.academic-years.toggle', $ay->id) }}" class="btn btn-sm btn-success w-100">
                                                <i class="fas fa-check"></i> Set Aktif
                                            </a>
                                        @else
                                            <span class="btn btn-sm btn-secondary w-100 disabled" title="Tidak bisa non-aktif jika satu-satunya aktif">
                                                <i class="fas fa-check"></i> Aktif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>Data Tahun Ajaran</h5>
                        <table class="table table-striped small">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tahun</th>
                                    <th>Semester</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($academicYears as $index => $ay)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $ay->year }}</td>
                                    <td>{{ $ay->semester }}</td>
                                    <td>
                                        @if ($ay->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.academic-years.edit', $ay->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>@endsection
