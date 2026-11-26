@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Arsip Rapor</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped small">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Tipe Rapor</th>
                                    <th>Format</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Unduh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($archives as $index => $archive)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $archive->student->name }}</td>
                                        <td>{{ $archive->student->classRoom->name ?? '-' }}</td>
                                        <td>{{ ucfirst($archive->type) }}</td>
                                        <td>{{ strtoupper($archive->format) }}</td>
                                        <td>{{ \Illuminate\Support\Carbon\Carbon::parse($archive->created_at)->locale('id')->format('d F Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.report-archives.download', $archive->id) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-download"></i>
                                            </a>
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
</div>

<a href="{{ route('admin.report-archives.download.all') }}" class="btn btn-sm btn-outline-secondary mt-3">
    <i class="bi bi-download"></i> Unduh Semua Arsip (ZIP)
</a>

{{ $archives->links() }}
@endsection