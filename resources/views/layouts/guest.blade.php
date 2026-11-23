<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Masuk &middot; {{ config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <main class="auth-page">
            <div class="container">
                <div class="auth-shell">
                    <div class="card auth-card">
                        <div class="row g-0">
                            <div class="col-lg-5 auth-aside d-none d-lg-flex flex-column justify-content-between">
                                <div class="auth-aside-content">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="brand-mark">RM</div>
                                        <div>
                                            <p class="brand-title">E-Rapor SLB</p>
                                            <p class="brand-subtitle">Rumah Melati</p>
                                        </div>
                                    </div>

                                    <h1>Pelaporan hasil belajar yang rapi dan terarsip.</h1>
                                    <p>
                                        Satu pintu untuk mengelola nilai akademik dan kompetensi siswa
                                        berkebutuhan khusus di seluruh jenjang.
                                    </p>

                                    <ul class="auth-feature-list">
                                        <li><span class="feature-dot">1</span><span>Input nilai akademik &amp; kompetensi dalam satu halaman siswa.</span></li>
                                        <li><span class="feature-dot">2</span><span>Konversi nilai angka ke huruf secara otomatis.</span></li>
                                        <li><span class="feature-dot">3</span><span>Rapor PDF tersimpan otomatis di arsip digital.</span></li>
                                    </ul>
                                </div>

                                <p class="mb-0 mt-5" style="font-size: 0.72rem; color: #7fb3ac;">
                                    &copy; {{ now()->year }} SLB Rumah Melati
                                </p>
                            </div>

                            <div class="col-lg-7 auth-form-side">
                                <div class="auth-form-inner">
                                    <div class="auth-brand-mobile">
                                        <div class="brand-mark">RM</div>
                                        <div>
                                            <p class="brand-title" style="color: #0c2f2d;">E-Rapor SLB</p>
                                            <p class="brand-subtitle" style="color: #6b7a8c;">Rumah Melati</p>
                                        </div>
                                    </div>

                                    {{ $slot }}

                                    <p class="auth-footer-note">
                                        Gunakan akun yang diberikan oleh administrator sekolah.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
