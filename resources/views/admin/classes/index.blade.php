@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Kelas</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif

                    <h5>Daftar Kelas</h5>
                    <div class="table-responsive">
                        <table class="table table-striped small">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Kelas</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Wali Kelas</th>
                                    <th>Jml Siswa</th>
                                    <th>Jml Mapel</th>
                                    <th>Jml Indikator</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classes as $index => $class)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $class->name }}</td>
                                    <td>{{ $class->academicYear->year ?? '-' }} - {{ $class->academicYear->semester ?? '-' }}</td>
                                    <td>{{ $class->teacher->name ?? '-' }}</td>
                                    <td>{{ $class->students_count ?? $class->students->count() }}</td>
                                    <td>{{ $class->subjects_count ?? $class->subjects->count() }}</td>
                                    <td>{{ $class->competency_indicators_count ?? $class->competencyIndicators->count() }}</td>
                                    <td>
                                        <a href="{{ route('admin.classes.edit', $class->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mt-4">Tambah Kelas Baru</h5>
                    <form action="{{ route('admin.classes.store') }}" method="POST" class="mt-3">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="name" class="form-label small">Nama Kelas</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="academic_year_id" class="form-label small">Tahun Ajaran</label>
                                <select name="academic_year_id" id="academic_year_id" class="form-control" required>
                                    <option value="">-- Pilih Tahun Ajaran --</option>
                                    @foreach (\App\Models\AcademicYear::orderBy('year', 'desc')->get() as $ay)
                                        <option value="{{ $ay->id }}">{{ $ay->year }} - {{ $ay->semester }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="teacher_id" class="form-label small">Wali Kelas</label>
                                <select name="teacher_id" id="teacher_id" class="form-control">
                                    <option value="">-- Tidak Ada --</option>
                                    @foreach (\App\Models\User::role('guru')->get() as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">Simpan Kelas</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection