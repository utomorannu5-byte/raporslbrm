<x-app-layout title="Dashboard Guru">
    <div class="page-heading">
        <div>
            <h1>Dashboard Guru</h1>
            <p>Pantau progres pengisian nilai siswa di kelas Anda.</p>
        </div>
        <div class="topbar-school">
            <span class="topbar-school-dot"></span>
            Tahun Ajaran: <strong class="ms-1">Belum diatur</strong>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="surface-card stat-card">
                <div class="stat-icon teal">
                    <x-icon name="students" />
                </div>
                <div>
                    <div class="stat-label">Siswa di Kelas</div>
                    <p class="stat-value">&mdash;</p>
                    <p class="stat-caption">Menunggu penugasan kelas</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="surface-card stat-card">
                <div class="stat-icon indigo">
                    <x-icon name="checklist" />
                </div>
                <div>
                    <div class="stat-label">Sudah Submit</div>
                    <p class="stat-value">&mdash;</p>
                    <p class="stat-caption">Menunggu modul input nilai</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="surface-card stat-card">
                <div class="stat-icon rose">
                    <x-icon name="clock" />
                </div>
                <div>
                    <div class="stat-label">Belum Submit</div>
                    <p class="stat-value">&mdash;</p>
                    <p class="stat-caption">Menunggu modul input nilai</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-0">
        <div class="col-xl-8">
            <div class="surface-card">
                <div class="surface-card-body">
                    <div class="card-heading">
                        <div>
                            <h2>Progres Kelengkapan Nilai</h2>
                            <p>Persentase siswa yang sudah submit akan dihitung otomatis.</p>
                        </div>
                        <span class="badge rounded-pill role-badge">Segera</span>
                    </div>

                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Progres</span>
                        <span>0%</span>
                    </div>
                    <div class="progress" role="progressbar" aria-label="Progres kelengkapan nilai" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>

                    <div class="placeholder-block mt-4">
                        <div>
                            <div class="placeholder-icon">
                                <x-icon name="pencil" style="width: 1.35rem; height: 1.35rem;" />
                            </div>
                            <div class="fw-semibold text-dark mb-1">Input nilai belum tersedia</div>
                            <div class="small">Modul input nilai akademik dan kompetensi akan hadir pada tahap berikutnya.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="surface-card">
                <div class="surface-card-body">
                    <div class="card-heading">
                        <div>
                            <h2>Akses Cepat</h2>
                            <p>Menu operasional guru.</p>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <span class="quick-link disabled" aria-disabled="true">
                            <span class="quick-link-icon"><x-icon name="pencil" style="width: 1rem; height: 1rem;" /></span>
                            <span class="flex-grow-1">Input Nilai Siswa</span>
                            <span class="soon-badge">Soon</span>
                        </span>
                        <span class="quick-link disabled" aria-disabled="true">
                            <span class="quick-link-icon"><x-icon name="printer" style="width: 1rem; height: 1rem;" /></span>
                            <span class="flex-grow-1">Cetak Rapor</span>
                            <span class="soon-badge">Soon</span>
                        </span>
                        <span class="quick-link disabled" aria-disabled="true">
                            <span class="quick-link-icon"><x-icon name="folder" style="width: 1rem; height: 1rem;" /></span>
                            <span class="flex-grow-1">Arsip Rapor</span>
                            <span class="soon-badge">Soon</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
