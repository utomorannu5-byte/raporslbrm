# E-Rapor SLB "Rumah Melati"

Aplikasi pelaporan hasil belajar siswa berkebutuhan khusus (SLB). Tahap 1 berisi fondasi aplikasi: autentikasi, RBAC, layout dashboard, dan halaman placeholder.

Dokumen acuan:

- `PRD-E-Rapor-SLB.md` — aturan bisnis dan spesifikasi fitur.
- `AGENTS.md` — konvensi teknis dan arsitektur.

## Tech Stack

- Laravel 11 (PHP 8.2+)
- MySQL 8.x / MariaDB
- Blade + Bootstrap 5 + Alpine.js (Vite)
- Laravel Breeze (Blade) + `spatie/laravel-permission`
- `barryvdh/laravel-dompdf`

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate

# Buat database (MySQL/MariaDB)
mysql -u root -p -e "CREATE DATABASE erapor_slb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Aplikasi berjalan di `http://localhost:8000`.

## Akun Default

| Role  | Email                    | Password   |
| ----- | ------------------------ | ---------- |
| Admin | `admin@erapor-slb.test`  | `password` |
| Guru  | `guru@erapor-slb.test`   | `password` |

Akun dibuat oleh `Database\Seeders\AdminUserSeeder`.

## Hak Akses

- Admin → `/admin/dashboard`
- Guru → `/guru/dashboard`

Setelah login, pengguna otomatis diarahkan ke dashboard sesuai role. Prefix route dilindungi middleware `auth` dan `role:admin` / `role:guru`.

## Testing

Test menggunakan SQLite in-memory (lihat `phpunit.xml`).

```bash
php artisan test
./vendor/bin/pint
```

## Status Tahap 1

Sudah tersedia:

- Login, logout, reset password (scaffold Breeze yang disesuaikan ke Bootstrap 5).
- Role `admin` dan `guru`.
- Layout utama: sidebar responsif, topbar, area konten.
- Dashboard placeholder untuk admin dan guru.

Belum tersedia (tahap berikutnya): master data, input nilai, submit & kunci, konversi nilai, generate PDF, dan arsip rapor.
