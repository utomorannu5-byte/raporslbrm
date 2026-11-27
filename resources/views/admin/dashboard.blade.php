<x-app-layout title="Dashboard Admin">
    <div class="page-heading mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard Admin</h1>
            <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}. Ringkasan pengelolaan data dan kelengkapan rapor sekolah.</p>
        </div>
        @if ($activeYear)
            <div class="topbar-school mt-3">
                <span class="topbar-school-dot"></span>
                Tahun Ajaran: <strong class="ms-1">{{ $activeYear->year }} - {{ $activeYear->semester }}</strong>
            </div>
        @else
            <div class="topbar-school mt-3 text-warning">
                <span class="topbar-school-dot"></span>
                <strong>Tidak ada tahun ajaran aktif.</strong> Silakan atur di menu Tahun Ajaran.
            </div>
        @endif
    </div>

    {{-- ============ STAT CARDS ============ --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="surface-card stat-card h-100">
                <div class="stat-icon teal">
                    <x-icon name="students" />
                </div>
                <div>
                    <div class="stat-label">Total Siswa</div>
                    <p class="stat-value">{{ number_format($totalStudents) }}</p>
                    <p class="stat-caption text-muted">Seluruh jenjang</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="surface-card stat-card h-100">
                <div class="stat-icon indigo">
                    <x-icon name="users" />
                </div>
                <div>
                    <div class="stat-label">Total Guru</div>
                    <p class="stat-value">{{ number_format($totalTeachers) }}</p>
                    <p class="stat-caption text-muted">Aktif</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="surface-card stat-card h-100">
                <div class="stat-icon amber">
                    <x-icon name="school" />
                </div>
                <div>
                    <div class="stat-label">Total Kelas</div>
                    <p class="stat-value">{{ number_format($totalClasses) }}</p>
                    <p class="stat-caption text-muted">{{ $activeYear ? $activeYear->year . ' - ' . $activeYear->semester : 'Belum ada TA aktif' }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="surface-card stat-card h-100">
                <div class="stat-icon green">
                    <x-icon name="book" />
                </div>
                <div>
                    <div class="stat-label">Total Mapel</div>
                    <p class="stat-value">{{ number_format($totalSubjects) }}</p>
                    <p class="stat-caption text-muted">Akademik</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ STATUS KELENGKAPAN PER KELAS ============ --}}
    <div class="row">
        <div class="col-12">
            <div class="surface-card">
                <div class="surface-card-body">
                    <div class="card-heading mb-3">
                        <div>
                            <h2 class="h5 mb-1">Status Kelengkapan Submit per Kelas</h2>
                            <p class="text-muted small mb-0">Tahun Ajaran {{ $activeYear->year ?? '-' }} - Semester {{ $activeYear->semester ?? '-' }}</p>
                        </div>
                        <a href="{{ route('admin.classes.index') }}" class="btn btn-sm btn-outline-primary">
                            <x-icon name="plus" style="width: 1rem; height: 1rem;" class="me-1" /> Kelola Kelas
                        </a>
                    </div>

                    @if ($classStats->isEmpty())
                        <div class="placeholder-block text-center py-5">
                            <div class="placeholder-icon">
                                <x-icon name="archive" style="width: 2.5rem; height: 2.5rem;" class="text-muted" />
                            </div>
                            <div class="fw-semibold text-dark mt-2 mb-1">Belum ada data kelas</div>
                            <div class="small text-muted">Tambahkan kelas melalui menu <strong>Kelas</strong> untuk melihat statistik kelengkapan.</div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Kelas</th>
                                        <th scope="col">Wali Kelas</th>
                                        <th scope="col" class="text-center">Total Siswa</th>
                                        <th scope="col" class="text-center">Sudah Submit</th>
                                        <th scope="col" class="text-center">Belum Submit</th>
                                        <th scope="col" style="width: 220px;">Progress</th>
                                        <th scope="col" class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($classStats as $stat)
                                        <tr>
                                            <td>
                                                <strong>{{ $stat['class']->name }}</strong>
                                            </td>
                                            <td>{{ $stat['class']->teacher->name ?? '<span class="text-muted">Belum ditugaskan</span>' }}</td>
                                            <td class="text-center">{{ $stat['total_students'] }}</td>
                                            <td class="text-center text-success">{{ $stat['submitted'] }}</td>
                                            <td class="text-center text-danger">{{ $stat['not_submitted'] }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height: 18px;" role="progressbar" aria-valuenow="{{ $stat['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar {{ $stat['progress'] == 100 ? 'bg-success' : ($stat['progress'] > 50 ? 'bg-info' : 'bg-warning') }}" style="width: {{ $stat['progress'] }}%"></div>
                                                    </div>
                                                    <span class="fw-medium text-nowrap">{{ $stat['progress'] }}%</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if ($stat['progress'] == 100)
                                                    <span class="badge bg-success rounded-pill">Lengkap</span>
                                                @elseif ($stat['progress'] > 0)
                                                    <span class="badge bg-info rounded-pill">Proses</span>
                                                @else
                                                    <span class="badge bg-secondary rounded-pill">Belum Mulai</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>