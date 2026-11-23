@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Edit Siswa: {{ $student->name }}</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif

                    <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="class_id" class="form-label small">Kelas</label>
                            <select name="class_id" id="class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }} ({{ $class->academicYear->year ?? '-' }} {{ $class->academicYear->semester ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nisn" class="form-label small">NISN (Opsional)</label>
                            <input type="text" name="nisn" id="nisn" class="form-control @error('nisn') is-invalid @enderror"
                                value="{{ old('nisn', $student->nisn) }}" maxlength="20">
                            @error('nisn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label small">Nama Siswa</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $student->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="parent_name" class="form-label small">Nama Orang Tua</label>
                            <input type="text" name="parent_name" id="parent_name" class="form-control @error('parent_name') is-invalid @enderror"
                                value="{{ old('parent_name', $student->parent_name) }}" required>
                            @error('parent_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection