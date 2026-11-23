# AGENTS.md

> File ini berisi instruksi untuk AI coding agent (OpenCode, Cursor, Claude Code, dll.) yang bekerja pada project **E-Rapor SLB "Rumah Melati"**. Baca seluruh file ini sebelum menulis kode.

---

## 1. Project Overview

**Nama:** E-Rapor SLB "Rumah Melati"  
**Tujuan:** Aplikasi web pelaporan hasil belajar siswa berkebutuhan khusus (SLB) di semua jenjang (SD, SMP, SMA, Lanjutan, Vokasional).  
**Output Utama per Siswa:** 2 dokumen PDF — **Rapor Akademik** (nilai angka/huruf + kepribadian + absensi) dan **Rapor Kompetensi** (checklist M/AV/AF/AP).

**Referensi Lengkap:** Lihat `PRD-E-Rapor-SLB.md` di root project. File itu adalah sumber kebenaran (source of truth) untuk semua keputusan bisnis.

---

## 2. Tech Stack (WAJIB DIIKUTI)

| Layer | Teknologi |
|---|---|
| Backend | Laravel 11.x (PHP 8.2+) |
| Database | MySQL 8.x / MariaDB |
| Frontend | Blade + Bootstrap 5 + Alpine.js (via Vite) |
| Auth & RBAC | Laravel Breeze + `spatie/laravel-permission` |
| PDF | `barryvdh/laravel-dompdf` |
| Storage | Laravel Storage (`storage/app/public/reports/`) |

**JANGAN** menambahkan library/framework lain tanpa persetujuan (misal: Vue, React, Livewire, Inertia) kecuali diminta secara eksplisit.

---

## 3. Aturan Arsitektur & Konvensi Kode

### 3.1 Struktur Direktori
Ikuti struktur standar Laravel. Gunakan:
- `app/Models/` untuk semua Eloquent model.
- `app/Http/Controllers/Admin/` untuk controller yang diakses role Admin.
- `app/Http/Controllers/Guru/` untuk controller yang diakses role Guru.
- `app/Http/Requests/` untuk Form Request (validasi).
- `app/Policies/` untuk otorisasi (misal: `ReportPolicy` untuk cek status submit).
- `app/Services/` untuk logika bisnis kompleks (misal: `PdfGeneratorService`, `GradeConversionService`, `AcademicYearCopyService`).
- `resources/views/admin/` dan `resources/views/guru/` untuk view yang terpisah per role.

### 3.2 Penamaan
- Model: **Singular PascalCase** (`Student`, `AcademicGrade`, `CompetencyIndicator`).
- Tabel: **Plural snake_case** (`students`, `academic_grades`, `competency_indicators`).
- Controller: **PascalCase + suffix** (`StudentController`, `AcademicGradeController`).
- Route: **kebab-case** (`/admin/competency-indicators`).
- Kolom DB: **snake_case**.

### 3.3 Eloquent
- Selalu definisikan `$fillable` (jangan pakai `$guarded = []`).
- Definisikan relasi di kedua sisi (`belongsTo` dan `hasMany`).
- Gunakan **Eager Loading** (`with()`) di semua query yang menampilkan list data untuk mencegah N+1.
- Gunakan **Accessor** untuk konversi nilai angka → huruf (contoh: `AcademicGrade::getScoreTextAttribute()`).

### 3.4 Validasi
- **Selalu** gunakan Form Request, bukan validasi inline di controller.
- Nilai akademik dibatasi: `min:0`, `max:100`.
- Nilai kompetensi dibatasi: `in:M,AV,AF,AP`.

### 3.5 Otorisasi
- Gunakan **Laravel Policies** untuk cek akses (contoh: guru hanya bisa edit nilai milik kelasnya sendiri).
- Gunakan middleware `role:admin` dan `role:guru` di route group.
- **Submit & Kunci:** jika `is_submitted = true`, tolak semua update via Policy.

---

## 4. Aturan Bisnis Kritis (JANGAN DILANGGAR)

1. **Copy Data Tahun Ajaran:** hanya menduplikasi master data (kelas, siswa, mapel, indikator). **Semua nilai (value) WAJIB di-reset/kosong.**
2. **Tidak Ada Cetak Massal:** cetak PDF hanya per-siswa per-dokumen.
3. **Tidak Ada Approval:** guru bisa langsung cetak setelah submit (atau bahkan saat draft).
4. **Arsip Otomatis:** setiap generate PDF, otomatis simpan record di `report_archives`.
5. **Watermark DRAFT:** jika `is_submitted = false`, PDF wajib diberi watermark "DRAFT" merah.
6. **Assign per Kelas:** mapel & indikator kompetensi di-assign ke kelas via pivot table (`class_subjects`, `class_indicators`). Jangan tampilkan semua mapel/indikator ke semua kelas.

---

## 5. Aturan Pembuatan PDF

- Gunakan `barryvdh/laravel-dompdf`.
- Simpan template Blade PDF di `resources/views/pdf/`.
- Nama file: `rapor-akademik-{student_id}-{academic_year_id}.pdf` dan `rapor-kompetensi-{student_id}-{academic_year_id}.pdf`.
- PDF harus meniru layout referensi (kop surat, tabel, tanda tangan). **Jangan improvisasi desain.**
- Gunakan font standar (Arial/Times New Roman).

---

## 6. Testing

- Tulis **Feature Test** untuk setiap endpoint baru di `tests/Feature/`.
- Wajib ada test untuk: login per role, input nilai, submit & kunci, copy data tahun ajaran, generate PDF.
- Gunakan `RefreshDatabase` trait.
- Jalankan `php artisan test` sebelum commit.

---

## 7. Git & Commit

- Branch: `feat/nama-fitur`, `fix/nama-bug`, `chore/nama-task`.
- Commit message: **Conventional Commits** (contoh: `feat(academic-grade): tambah form input nilai`).
- Jangan commit file `.env`, `vendor/`, `node_modules/`, atau hasil generate PDF.
- Sebelum commit: jalankan `./vendor/bin/pint` (Laravel Pint) untuk format kode.

---

## 8. Yang TIDAK Boleh Dilakukan Agent

- ❌ Jangan hardcode data siswa, mapel, atau indikator di view/controller. **Semua harus dari database.**
- ❌ Jangan membuat fitur yang tidak ada di PRD tanpa bertanya dulu.
- ❌ Jangan mengubah skema database (migration yang sudah dijalankan) — buat migration baru.
- ❌ Jangan menggunakan `DB::raw()` tanpa parameter binding (risiko SQL injection).
- ❌ Jangan skip validasi server-side meskipun sudah ada validasi client-side.

---

## 9. Alur Kerja Agent (Rekomendasi)

Saat menerima instruksi, ikuti urutan:
1. **Baca** instruksi + cek PRD (`PRD-E-Rapor-SLB.md`) untuk konteks.
2. **Rencanakan** file apa saja yang akan dibuat/diubah.
3. **Buat Migration** (jika perlu tabel/kolom baru) → jalankan `php artisan migrate`.
4. **Buat/Update Model** + relasi.
5. **Buat Form Request** untuk validasi.
6. **Buat Service** (jika logika kompleks).
7. **Buat Controller** (tipis, delegasi ke Service).
8. **Buat Route** + middleware role.
9. **Buat View** (Blade + Bootstrap 5).
10. **Buat Feature Test**.
11. **Jalankan test** dan pastikan lulus.

---

## 10. Perintah Berguna

```bash
# Setup awal
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Development
php artisan serve
npm run dev

# Testing
php artisan test

# Format
./vendor/bin/pint

# Clear cache (jika ada masalah aneh)
php artisan optimize:clear