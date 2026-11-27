<x-app-layout title="Dashboard Guru">
    <div class="page-heading mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard Guru</h1>
            <p class="text-muted mb-0">Selamat datang, {{ $teacher->name }}. Pantau progres pengisian nilai siswa di kelas Anda.</p>
        </div>
        @if ($activeYear)
            <div class="topbar-school mt-3">
                <span class="topbar-school-dot"></span>
                Tahun Ajaran: <strong class="ms-1">{{ $activeYear->year }} - {{ $activeYear->semester }}</strong>
            </div>
        @else
            <div class="topbar-school mt-3 text-warning">
                <span class="topbar-school-dot"></span>
                <strong>Tidak ada tahun ajaran aktif.</strong>
            </div>
        @endif
    </div>

    @if (!$hasClass)
        <div class="surface-card">
            <div class="surface-card-body text-center py-5">
                <div class="placeholder-icon mb-3">
                    <x-icon name="person-badge" style="width: 3rem; height: 3rem;" class="text-muted" />
                </div>
                <h4 class="fw-semibold text-dark mb-2">Anda belum ditugaskan sebagai wali kelas</h3>
                <p class="text-muted mb-4">Hubungi Admin untuk ditugaskan sebagai wali kelas agar dapat mengakses fitur input nilai dan melihat dashboard.</p>
                <a href="{{ route('guru.input-nilai.index') }}" class="btn btn-outline-primary" disabled>
                    <x-icon name="pencil" style="width: 1rem; height: 1rem;" class="me-1" /> Input Nilai
                </a>
            </div>
    @else
        {{-- ============ STAT CARDS ============ --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="surface-card stat-card h-100">
                    <div class="stat-icon teal">
                        <x-icon name="students" />
                    </div>
                    <div>
                        <div class="stat-label">Total Siswa di Kelas</div>
                        <p class="stat-value">{{ number_format($totalStudents) }}</p>
                        <p class="stat-caption text-muted">{{ $classStats->count() }} kelas diampu</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="surface-card stat-card h-100">
                    <div class="stat-icon indigo">
                        <x-icon name="checklist" />
                    </div>
                    <div>
                        <div class="stat-label">Sudah Submit Lengkap</div>
                        <p class="stat-value">{{ number_format($totalSubmitted) }}</p>
                        <p class="stat-caption text-muted">Nilai akademik & kompetensi</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="surface-card stat-card h-100">
                    <div class="stat-icon rose">
                        <x-icon name="clock" />
                    </div>
                    <div>
                        <div class="stat-label">Belum Submit</div>
                        <p class="stat-value">{{ number_format($totalNotSubmitted) }}</p>
                        <p class="stat-caption text-muted">Perlu dilengkapi</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ PROGRESS PER KELAS ============ --}}
        <div class="row">
            <div class="col-12">
                <div class="surface-card">
                    <div class="surface-card-body">
                        <div class="card-heading mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <h2 class="h5 mb-1">Progres Kelengkapan Nilai per Kelas</h2>
                                <p class="text-muted small mb-0">Tahun Ajaran {{ $activeYear->year ?? '-' }} - Semester {{ $activeYear->semester ?? '-' }}</p>
                            </div>
                            <a href="{{ route('guru.input-nilai.index') }}" class="btn btn-primary">
                                <x-icon name="pencil" style="width: 1rem; height: 1rem;" class="me-1" /> Input Nilai Sekarang
                            </a>
                        </div>

                        @foreach ($classStats as $stat)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span class="fw-medium">{{ $stat['class']->name }}</span>
                                    <span>{{ $stat['progress'] }}%</span>
                                </div>
                                <div class="progress" style="height: 22px;" role="progressbar" aria-valuenow="{{ $stat['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar {{ $stat['progress'] == 100 ? 'bg-success' : ($stat['progress'] > 50 ? 'bg-info' : 'bg-warning') }} d-flex align-items-center justify-content-center"
                                         style="width: {{ $stat['progress'] }}%; font-size: 0.8rem;">
                                        {{ $stat['submitted'] }} / {{ $stat['total_students'] }} siswa submit
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Overall Progress --}}
                        @if ($totalStudents > 0)
                            <hr class="my-3" />
                            <div>
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span class="fw-bold">Total Keseluruhan</span>
                                    <span class="fw-bold">{{ $totalStudents > 0 ? round(($totalSubmitted / $totalStudents) * 100) : 0 }}%</span>
                                </div>
                                <div class="progress" style="height: 24px;" role="progressbar" aria-valuenow="{{ $totalStudents > 0 ? round(($totalSubmitted / $totalStudents) * 100) : 0 }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-primary d-flex align-items-center justify-content-center" style="width: {{ $totalStudents > 0 ? round(($totalSubmitted / $totalStudents) * 100) : 0 }}%; font-size: 0.85rem;">
                                        {{ $totalSubmitted }} dari {{ $totalStudents }} siswa sudah submit lengkap
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>