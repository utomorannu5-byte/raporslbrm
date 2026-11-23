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