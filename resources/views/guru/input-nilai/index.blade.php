@extends('guru.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Input Nilai Siswa</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                    @endif

                    @if ($academicYear)
                        <div class="alert alert-info mb-3">
                            Tahun Ajaran Aktif: <strong>{{ $academicYear->year }} - {{ $academicYear->semester }}</strong>
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                            <strong>Peringatan:</strong> Tidak ada tahun ajaran aktif. Silakan hubungi admin.
                        </div>
                    @endif

                    <h5>Daftar Siswa Kelas Anda</h5>
                    <div class="table-responsive">
                        <table class="table table-striped small">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                    <th>Rapor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($studentsWithStatus->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Anda belum ditugaskan sebagai wali kelas manapun.
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($studentsWithStatus as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item['student']->nisn ?? '-' }}</td>
                                            <td>{{ $item['student']->name }}</td>
                                            <td>{{ $item['student']->classRoom->name ?? '-' }}</td>
                                            <td>
                                                @if ($item['status'] === 'Terkunci')
                                                    <span class="badge bg-success">Terkunci</span>
                                                @elseif ($item['status'] === 'Belum Lengkap')
                                                    <span class="badge bg-warning text-dark">Belum Lengkap</span>
                                                @elseif ($item['status'] === 'Belum Diisi')
                                                    <span class="badge bg-secondary">Belum Diisi</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $item['status'] }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('guru.input-nilai.edit', $item['student']->id) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   @if ($item['status'] === 'Tahun Ajaran Belum Aktif') disabled @endif>
                                                   Input Nilai
                                                </a>
                                            </td>
                                            <td class="text-nowrap">
                                                <a href="{{ route('guru.report.academic.pdf', $item['student']->id) }}"
                                                   class="btn btn-sm btn-primary"
                                                   title="Cetak Rapor PDF"
                                                   target="_blank">PDF</a>
                                                <a href="{{ route('guru.report.academic.word', $item['student']->id) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Unduh Rapor Word">Word</a>
                                                @unless ($item['status'] === 'Tahun Ajaran Belum Aktif')
                                                    <a href="{{ route('guru.report.competency.pdf', $item['student']->id) }}"
                                                       class="btn btn-sm btn-success"
                                                       title="Cetak Rapor Kompetensi">Kompetensi PDF</a>
                                                    <a href="{{ route('guru.report.competency.word', $item['student']->id) }}"
                                                       class="btn btn-sm btn-outline-success"
                                                       title="Unduh Rapor Kompetensi">Kompetensi Word</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
