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
                        <h5>Tambah Tahun Ajaran Baru</h5>
                        <form action="{{ route('admin.academic-years.store') }}" method="POST" class="mb-3">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label for="year" class="form-label small">Tahun Ajaran</label>
                                    <input type="text" name="year" id="year" class="form-control @error('year') is-invalid @enderror"
                                        value="{{ old('year') }}" required placeholder="Contoh: 2025/2026">
                                    @error('year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="semester" class="form-label small">Semester</label>
                                    <select name="semester" id="semester" class="form-control @error('semester') is-invalid @enderror" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                        <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input @error('is_active') is-invalid @enderror"
                                            value="1" {{ old('is_active') ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="is_active">Aktifkan</label>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success w-100">Simpan Tahun Ajaran</button>
                                </div>
                            </div>
                        </form>
                    </div>

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
