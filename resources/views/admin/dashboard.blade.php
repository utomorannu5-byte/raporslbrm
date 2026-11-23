<x-app-layout title="Dashboard Admin">
    <div class="page-heading">
        <div>
            <h1>Dashboard Admin</h1>
            <p>Ringkasan pengelolaan data dan kelengkapan rapor sekolah.</p>
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
                    <div class="stat-label">Total Siswa</div>
                    <p class="stat-value">&mdash;</p>
                    <p class="stat-caption">Menunggu modul master siswa</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="surface-card stat-card">
                <div class="stat-icon indigo">
                    <x-icon name="users" />
                </div>
                <div>
                    <div class="stat-label">Total Guru</div>
                    <p class="stat-value">&mdash;</p>
                    <p class="stat-caption">Menunggu modul pengguna</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="surface-card stat-card">
                <div class="stat-icon amber">
                    <x-icon name="school" />
                </div>
                <div>
                    <div class="stat-label">Total Kelas</div>
                    <p class="stat-value">&mdash;</p>
                    <p class="stat-caption">Menunggu modul kelas</p>
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
                            <h2>Kelengkapan Submit per Kelas</h2>
                            <p>Status pengisian nilai akan tampil setelah modul nilai aktif.</p>
                        </div>
                        <span class="badge rounded-pill role-badge">Segera</span>
                    </div>

                    <div class="placeholder-block">
                        <div>
                            <div class="placeholder-icon">
                                <x-icon name="archive" style="width: 1.35rem; height: 1.35rem;" />
                            </div>
                            <div class="fw-semibold text-dark mb-1">Belum ada data kelas</div>
                            <div class="small">Statistik kelengkapan rapor akan tersedia pada tahap berikutnya.</div>
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
                            <h2>Fondasi Siap</h2>
                            <p>Modul yang sudah tersedia pada tahap ini.</p>
                        </div>
                    </div>

                    <ul class="check-list">
                        <li><span class="check-dot">&#10003;</span><span>Autentikasi dan pembatasan akses berbasis role.</span></li>
                        <li><span class="check-dot">&#10003;</span><span>Sidebar dan layout responsif dengan Bootstrap 5.</span></li>
                        <li><span class="check-dot">&#10003;</span><span>Akun administrator dan guru demo.</span></li>
                        <li><span class="check-dot">&#10003;</span><span>Struktur siap untuk master data dan rapor PDF.</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
