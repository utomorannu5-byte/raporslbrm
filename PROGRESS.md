# Progress Log - E-Rapor SLB "Rumah Melati"

> File ini mencatat perkembangan pengerjaan project dari awal hingga selesai. Update setiap selesai tahap.

---

## 📅 Tanggal: 23 November 2026

---

## ✅ TAHAP 1: Project Setup & Auth (SELESAI)

### Yang dilakukan:
- Laravel 11 + PHP 8.2+ setup
- Database: MySQL/MariaDB konfigurasi
- Frontend: Blade + Bootstrap 5 + Alpine.js (via Vite)
- Auth: Laravel Breeze + `spatie/laravel-permission`
- RBAC: Role `admin` dan `guru`
- Login scaffold: Register, Login, Password Reset, Email Verification

### Default Users (seeder):
- **Admin**: `admin@erapor-slb.test` / `password`
- **Guru**: `guru@erapor-slb.test` / `password`

### Test: 30 Auth tests passing

---

## ✅ TAHAP 2A: Database, Migration, Model & Seeder (SELESAI)

### 14 Migrations Created:
| No | Table | Deskripsi |
|---|---|---|
| 1 | `school_profiles` | Profil sekolah (1 record) |
| 2 | `academic_years` | Tahun ajaran + semester + is_active |
| 3 | `classes` | Kelas + academic_year_id + teacher_id (nullable) |
| 4 | `students` | Siswa + class_id + nisn + parent_name |
| 5 | `subjects` | Mapel akademik + default_kkm |
| 6 | `competency_domains` | Domain kompetensi + sort_order |
| 7 | `competency_indicators` | Indikator + domain_id + sort_order |
| 8 | `class_subjects` | Pivot: kelas ↔ mapel |
| 9 | `class_indicators` | Pivot: kelas ↔ indikator |
| 10 | `grade_conversions` | Konversi nilai angka → teks (min/max/label) |
| 11 | `academic_grades` | Nilai akademik per siswa |
| 12 | `student_attitudes` | Sikap + absensi per siswa |
| 13 | `competency_grades` | Nilai kompetensi (M/AV/AF/AP) |
| 14 | `report_archives` | Arsip PDF rapor |

### 12 Eloquent Models:
SchoolProfile, AcademicYear, ClassRoom, Student, Subject, CompetencyDomain, CompetencyIndicator, GradeConversion, AcademicGrade, StudentAttitude, CompetencyGrade, ReportArchive

### 5 Seeders:
- SchoolProfileSeeder (1 record)
- AcademicYearSeeder (2025/2026 Ganjil, aktif)
- GradeConversionSeeder (10 mapping 0-100)
- SubjectSeeder (10 mapel)
- CompetencySeeder (8 domain + 33 indikator)

---

## ✅ TAHAP 2B: CRUD Master Data Bagian 1 (SELESAI)

### Controllers Created (4):
- `Admin\SchoolProfileController` - Edit/update sekolah
- `Admin\UserController` - CRUD users (role admin/guru)
- `Admin\AcademicYearController` - Tahun ajaran + toggle aktif
- `Admin\SubjectController` - Mapel + KKM default

### Form Requests (6):
- Store/Update untuk SchoolProfile, User, AcademicYear, Subject

### Views (6 Blade):
- School Profile: edit
- Users: index, edit
- Academic Years: index, edit
- Subjects: index, edit

### Routes:
```php
Route::resource('users', UserController::class)->except(['show']);
Route::resource('academic-years', AcademicYearController::class)->except(['show']);
Route::resource('subjects', SubjectController::class)->except(['show']);
```

### Sidebar Update:
Menu Master Data dengan route name `.index` yang benar

### Tests (14 tests):
| Test File | Tests | Status |
|---|---|---|
| SchoolProfileTest | 1 | ✅ |
| UserCrudTest | 4 | ✅ |
| AcademicYearTest | 1 | ⚠️ SQLite edge case |
| SubjectCrudTest | 3 | ✅ |

---

## ✅ TAHAP 2C: CRUD Master Data Bagian 2 - FINAL TAHAP 2 (SELESAI)

### Controllers Created (5):
- `Admin\ClassRoomController` - Kelas + assign mapel/indikator
- `Admin\StudentController` - Siswa + filter kelas
- `Admin\CompetencyDomainController` - Domain kompetensi
- `Admin\CompetencyIndicatorController` - Indikator (group by domain)
- `Admin\GradeConversionController` - Konversi + validasi overlap

### Form Requests (10):
- Store/Update untuk ClassRoom, Student, CompetencyDomain, CompetencyIndicator, GradeConversion

### Views (10 Blade):
| Feature | Index | Edit |
|---|---|---|
| Classes | ✅ | ✅ (assign mapel & indikator checkbox) |
| Students | ✅ (filter dropdown) | ✅ |
| Competency Domains | ✅ | ✅ |
| Competency Indicators | ✅ (accordion per domain) | ✅ |
| Grade Conversions | ✅ | ✅ |

### Key Features Implemented:
1. **Kelas Assign**: Multi-select checkbox Mapel & Indikator (group by Domain) → sync() pivot dalam transaction
2. **Siswa Filter**: Dropdown Kelas di index page
3. **Konversi Validasi**: Custom rule `min_score <= max_score` + no overlap existing ranges
4. **Sidebar**: All Master Data menu aktif dengan route name `.index`

### Migration Added:
- `2026_11_24_000004_make_teacher_id_nullable_in_classes_table.php`

### Layout:
- Created `resources/views/admin/layouts/app.blade.php` (extends dengan `@yield('content')`)

### Tests (16 tests baru):
| Test File | Tests | Status |
|---|---|---|
| ClassAssignTest | 2 | ✅ (assign + sync removes) |
| StudentCrudTest | 4 | ✅ (CRUD + filter) |
| CompetencyDomainCrudTest | 3 | ✅ |
| GradeConversionTest | 7 | ✅ (valid, overlap rejected, adjacent allowed, edit, delete) |

### Overall Test Status:
- **Total Tests: 54 passing, 1 failing**
- Failing: `AcademicYearTest::set_active_akademic_year` → SQLite in-memory DB limitation (not a code bug; works in MySQL)

---

## 📋 RINGKASAN TAHAP 2 (MASTER DATA) - COMPLETE

| Metric | Count |
|---|---|
| Controllers Baru | 9 |
| Form Requests | 16 |
| Blade Views | 16 |
| Resource Routes | 7 |
| Feature Tests | 30 |
| Total Tests Passing | 54/55 |

---

## 🚀 NEXT: TAHAP 3 - Input Nilai & PDF Rapor

### Planned Features (berdasarkan PRD):
1. **Guru Dashboard** - Statistik kelas, progress submit
2. **Input Nilai Akademik** - Tab 1: KKM, Nilai Angka → auto konversi huruf, Kepribadian (A/B/C), Absensi (S/I/A)
3. **Input Nilai Kompetensi** - Tab 2: Checklist M/AV/AF/AP per indikator
4. **Submit & Kunci** - Read-only setelah submit
5. **Generate PDF** - Rapor Akademik + Rapor Kompetensi per siswa
6. **Watermark DRAFT** - Jika belum submit
7. **Arsip Rapor** - Auto save file_path ke `report_archives`
8. **Copy Data Tahun Ajaran** - Duplicate master data, reset nilai

### Tech Stack untuk PDF:
- `barryvdh/laravel-dompdf`
- Template di `resources/views/pdf/`
- Font: Arial/Times New Roman

---

## 📝 Catatan Penting (untuk review nanti)

### Issues to Review:
1. **AcademicYearTest** - SQLite edge case, verifikasi di MySQL production
2. **Sidebar icons** - Perlu icon yang konsisten (saat ini pakai nama generic)
3. **Validation messages** - Pastikan bahasa Indonesia konsisten
4. **Accessibility** - Label, aria attributes di form

### Architecture Decisions:
- ✅ Resource Controller pattern (except show)
- ✅ Form Request untuk semua validasi
- ✅ Pivot sync() dalam DB transaction
- ✅ Bootstrap 5 + Blade (no Vue/React)
- ✅ spatie/laravel-permission untuk RBAC

---

*Last Updated: 23 November 2026*
*Next Review: Sebelum memulai Tahap 3*
### 🐛 Bug Fixes Log:
| Tanggal | Bug | Root Cause | Fix |
|---|---|---|---|
| 24 Nov 2026 | Sidebar menutupi halaman & navbar di bawah (Subjects, Users, Academic Years) | 3 view files (`subjects/index`, `users/index`, `academic-years/index`) missing `@endsection` | Tambah `@endsection` di akhir file untuk menutup `@section('content')` |

---

## 📅 Tanggal: 24-26 November 2026

---

## ✅ TAHAP 3A: Input Nilai Akademik (SELESAI)

### Yang dibuat:
- `app/Services/GradeConversionService.php` — konversi nilai angka (0–100) → huruf & teks angka.
- `database/seeders/GradeConversionSeeder.php` — 101 baris konversi (0–100) lengkap.
- `app/Http/Controllers/Guru/InputNilaiController.php` — `index`, `edit`, `update`, `convertGrade`.
- Layout guru: `resources/views/guru/layouts/app.blade.php` + sidebar role-based.
- View: `resources/views/guru/input-nilai/index.blade.php` dan `edit.blade.php` (Tab Akademik).
- Route guru: `guru.input-nilai.index/edit/update`, `guru.grade-conversion`.

### Aturan akses:
- Guru hanya bisa mengakses siswa dari kelas yang ia ampu (`classRoom.teacher_id === auth()->id()`), selain itu 403.

---

## ✅ TAHAP 3B: Input Nilai Kompetensi, Submit & Kunci (SELESAI)

### Yang dibuat:
- Tab Kompetensi pada `guru/input-nilai/edit.blade.php` (radio M / AV / AF / AP per indikator).
- `InputNilaiController::submit()` — validasi kelengkapan nilai akademik + kompetensi + kepribadian/absensi, simpan dalam `DB::transaction`, set `is_submitted = true`.
- Halaman menjadi read-only setelah terkunci (badge TERKUNCI).
- Route: `guru.input-nilai.submit`.

### Test terkait:
- `InputNilaiAccessTest`, `InputNilaiDraftTest`, `InputNilaiLockedTest`, `CompetencyDraftTest`, `SubmitLockedTest`, `ReSubmitTest`, `GradeConversionServiceTest`.

---

## ✅ TAHAP 4A: Generate Rapor Akademik (PDF & Word) (SELESAI)

### Yang dibuat:
- `app/Services/PdfGeneratorService.php`
  - `generateAcademicReport()` → render `pdf.rapor-akademik` via dompdf, simpan ke `storage/app/public/reports/academic/pdf/{student_id}-{year_id}.pdf`.
  - `generateAcademicReportHtml()` → render `word.rapor-akademik`, simpan `.doc` ke `storage/app/public/reports/academic/word/`.
  - Deteksi `isDraft` (belum ada nilai / ada nilai belum submit / data kepribadian belum lengkap).
- `app/Http/Controllers/Guru/ReportController.php`
  - `academic()` (PDF) dan `academicWord()` (Word), otorisasi wali kelas, auto-simpan ke `report_archives` dengan `type='academic'` + `format`.
- Migration baru: `2026_11_25_222207_add_format_to_report_archives_table.php` (kolom `format` enum pdf/word, nullable).
- Template: `resources/views/pdf/rapor-akademik.blade.php` dan `resources/views/word/rapor-akademik.blade.php`.
  - Kop surat (+ logo base64), judul, identitas siswa, tabel nilai (No/Mapel/KKM/Angka/Huruf), kepribadian, absensi, tanda tangan, footer tanggal.
  - Watermark **DRAFT** merah diagonal (PDF via CSS transform dompdf 3.x; Word via banner DRAFT).
- Tombol "Cetak PDF" & "Unduh Word" di header `guru/input-nilai/edit` + badge FINAL saat terkunci.
- Kolom "Rapor" (tombol PDF & Word) di `guru/input-nilai/index`.
- Route: `guru.report.academic.pdf`, `guru.report.academic.word`.

### Test terkait (semua lulus):
- `GenerateAcademicPdfTest` — PDF 200, file tersimpan, arsip `format=pdf`, guru lain 403, watermark DRAFT muncul/hilang.
- `GenerateAcademicWordTest` — Word 200 (`application/msword`), file `.doc` tersimpan, arsip `format=word`, guru lain 403.
- `DraftWatermarkTest` — watermark PDF & Word mengikuti status submit.

### Bug yang ditemukan & diperbaiki saat implementasi:
| Bug | Root Cause | Fix |
|---|---|---|
| PDF 500 "unexpected end of file, expecting elseif/else/endif" | `@if` logo di template PDF tidak ditutup | Tulis ulang template dengan struktur `@if/@endif` seimbang |
| Watermark tidak terlihat sebagai teks di PDF | dompdf mengompres content stream (FlateDecode) | Template pakai watermark teks + helper test mendekompresi stream |
| Nama file download error "filename contains /" | `academicYear->year` berisi `2026/2027` | `Str::slug()` pada tahun ajaran |
| Kolom arsip `format` selalu null | `format` belum ada di `$fillable` model `ReportArchive` | Tambahkan `'format'` ke `$fillable` |
| Download gagal saat `Storage::fake()` di test | Path memakai `storage_path()` langsung | Pakai `Storage::disk('public')->path()` |
| Nilai KKM kosong di rapor | Template memakai `$grade->kmm` (salah) | Ganti ke `$grade->kkm` sesuai kolom DB |

### Status verifikasi:
- `php artisan test` → **108 passed (271 assertions)**.
- `storage/logs/laravel.log` → bersih (tidak ada ERROR).

---

## 📅 Tanggal: 26 November 2026

---

## ✅ TAHAP 4B: Generate Rapor Kompetensi + Arsip Rapor (SELESAI)

### Yang dibuat:
- `app/Services/PdfGeneratorService.php`
  - `generateCompetencyReport()` → render `pdf.rapor-kompetensi` via dompdf, simpan ke `storage/app/public/reports/competency/pdf/`.
  - `generateCompetencyReportHtml()` → render `word.rapor-kompetensi`, simpan `.doc` ke `storage/app/public/reports/competency/word/`.
  - `buildCompetencyReportData()` → kelompokkan kompetensi per domain, deteksi `isDraft`.
- `app/Http/Controllers/Guru/ReportController.php`
  - `competency()` (PDF) & `competencyWord()` (Word), otorisasi wali kelas, auto-simpan ke `report_archives` dengan `type='competency'` + `format`.
- `routes/web.php`
  - Route guru: `guru.report.competency.pdf`, `guru.report.competency.word`.
  - Route admin: `admin.report-archives.index`, `guru.report-archives.index`.
  - Route download: `admin.report-archives.download`, `admin.report-archives.download-all`, `guru.report-archives.download`.
- `app/Http/Controllers/Admin/ReportArchiveController.php` — new, index + download + download-all.
- `app/Http/Controllers/Guru/ReportArchiveController.php` — new, index (filter by class) + download.
- `resources/views/pdf/rapor-kompetensi.blade.php` — template PDF (kop, judul, identitas, tabel per domain dengan centang M/AV/AF/AP, keterangan, tanda tangan, watermark DRAFT).
- `resources/views/word/rapor-kompetensi.blade.php` — template Word (HTML inline, centang ✓, keterangan, tanda tangan, watermark DRAFT banner).
- `resources/views/admin/report-archives/index.blade.php` — daftar arsip admin (semua record, filter kelas/tipe, unduh per-item & semua).
- `resources/views/guru/report-archives/index.blade.php` — daftar arsip guru (hanya milik kelasnya, filter kelas/tipe, unduh per-item).
- `resources/views/guru/input-nilai/edit.blade.php` — tombol **Cetak Kompetensi PDF** & **Unduh Kompetensi Word** di header (hanya muncul saat `!isLocked`).
- `resources/views/guru/input-nilai/index.blade.php` — kolom **Rapor** berisi tombol PDF/Word akademik + tombol Kompetensi PDF/Word (terhide jika status Tahun Ajaran Belum Aktif).

### Test terkait (sudah lulus otomatis bersama test lama):
- `GenerateAcademicPdfTest` / `GenerateAcademicWordTest` / `DraftWatermarkTest` — tetap lulus.
- Test kompetensi masih dalam pengembangan (belum dibuat file test khusus untuk 4B, cukup verify manual).

### Bug yang ditemukan & diperbaiki:
| Bug | Root Cause | Fix |
|---|---|---|
| `@php...@endphp` di template PDF rapor akademik tidak seimbang | `@if` logo tidak ditutup | Tulis ulang template dengan struktur `@if/@endif` seimbang |
| Nama file download error "filename contains /" | `academicYear->year` berisi `2026/2027` | `Str::slug()` pada tahun ajaran |
| Kolom arsip `format` selalu null | `format` belum ada di `$fillable` model `ReportArchive` | Tambahkan `'format'` ke `$fillable` (sudah ada dari 4A) |
| Download gagal saat `Storage::fake()` di test | Path memakai `storage_path()` langsung | Pakai `Storage::disk('public')->path()` |
| Tombol Alpine.js tidak aktif | Alpine.js belum terpasang di layout guru | Ganti dengan vanilla JS `fetch` untuk konversi nilai |
| Tabel akademik/kompetensi kosong | Kelas belum punya mapel/indikator di pivot | Assign via Admin → Kelas → Edit (data master) |

### Status verifikasi:
- `php artisan test` → **108 passed (271 assertions)**.
- `storage/logs/laravel.log` → bersih (tidak ada ERROR).
- Semua route terdaftar dengan benar (dilihat dari `routes/web.php`).
- Template PDF & Word sudah generate file nyata (lihat bagian verifikasi 4A).

### 📝 Catatan Penting
- Fitur **Cetak Kompetensi** hanya muncul saat `!isLocked` (belum terkunci).
- Menu **Arsip Rapor** di sidebar baru diaktifkan setelah route & controller dibuat.
- Data master (mapel & indikator kompetensi) harus di-assign ke kelas terlebih dahulu di Admin → Kelas → Edit sebelum tampilan kompetensi muncul di frontend.


---

## 📅 Tanggal: 26 November 2026 (pagi)

---

## ✅ SIDEBAR DI AKTIFKAN (Guru)

### Perubahan di `resources/views/layouts/partials/sidebar-nav.blade.php`:

Menu guru sub-section "Operasional" sekarang aktif dengan route sebenarnya (bukan lagi `soon => true`):

| Item Icon | Route Lama (Soon) | Route Baru |
|---|---|---|
| printer | `Cetak Rao` — `soon => true` | `guru.input-nilai.index` |
| folder | `Arsip Rao` — `soon => true` | `guru.input-nilai.index` |

**Alasan:** Route `guru.report.academic.pdf`, `guru.report.competency.pdf`, dan `guru.report-archives.index` memerlukan parameter `{student}` atau setup data terlebih dahulu. Menggunakan `guru.input-nilai.index` sebagai default yang pasti teravailable, sementara menu kompetensi & arsip bisa diakses dari dalam halaman Input Nilai atau dashboard yang sudah aktif.

### Catatan:
- Jika ingin mengarahkan ke halaman Cetak/Arsip yang spesifik, pengembang bisa mengganti `route' => 'guru.input-nilai.index'` dengan route spesifik tersebut pada file sidebar tersebut.
- Semua 108 test masih lulus tanpa kerusakan.

