@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Siswa</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif

                    <h5>Daftar Siswa</h5>
                    <form action="{{ route('admin.students.index') }}" method="GET" class="mb-3">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="class_id" class="form-label small">Filter Kelas</label>
                                <select name="class_id" id="class_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">-- Semua Kelas --</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }} ({{ $class->academicYear->year ?? '-' }} {{ $class->academicYear->semester ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped small">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Nama Orang Tua</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $student->nisn ?? '-' }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->classRoom->name ?? '-' }}</td>
                                    <td>{{ $student->classRoom->academicYear->year ?? '-' }} - {{ $student->classRoom->academicYear->semester ?? '-' }}</td>
                                    <td>{{ $student->parent_name }}</td>
                                    <td>
                                        <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mt-4">Tambah Siswa Baru</h5>
                    <form action="{{ route('admin.students.store') }}" method="POST" class="mt-3">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="class_id" class="form-label small">Kelas</label>
                                <select name="class_id" id="class_id" class="form-control" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->academicYear->year ?? '-' }} {{ $class->academicYear->semester ?? '-' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="nisn" class="form-label small">NISN (Opsional)</label>
                                <input type="text" name="nisn" id="nisn" class="form-control" maxlength="20">
                            </div>
                            <div class="col-md-4">
                                <label for="name" class="form-label small">Nama Siswa</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label for="parent_name" class="form-label small">Nama Orang Tua</label>
                                <input type="text" name="parent_name" id="parent_name" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">Simpan Siswa</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection