<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\ClassRoomController;
use App\Http\Controllers\Admin\CompetencyDomainController;
use App\Http\Controllers\Admin\CompetencyIndicatorController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GradeConversionController;
use App\Http\Controllers\Admin\SchoolProfileController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\InputNilaiController;
use App\Http\Controllers\Guru\ReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardRedirectController::class)->name('home');

Route::get('/dashboard', DashboardRedirectController::class)
    ->middleware('auth')
    ->name('dashboard');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/school-profile/edit', [SchoolProfileController::class, 'edit'])
            ->name('school-profile.edit');

        Route::put('/school-profile', [SchoolProfileController::class, 'update'])
            ->name('school-profile.update');

        Route::resource('users', UserController::class)
            ->except(['show']);

        Route::resource('academic-years', AcademicYearController::class)
            ->except(['show']);

        Route::get('academic-years/copy', [AcademicYearController::class, 'copyForm'])
            ->name('academic-years.copy-form');

        Route::post('academic-years/copy', [AcademicYearController::class, 'copy'])
            ->name('academic-years.copy');

        Route::post('academic-years/{academic_year}/toggle', [AcademicYearController::class, 'toggle'])
            ->name('academic-years.toggle');

        Route::resource('subjects', SubjectController::class)
            ->except(['show']);

        Route::resource('classes', ClassRoomController::class)
            ->except(['show']);

        Route::resource('students', StudentController::class)
            ->except(['show']);

        Route::resource('competency-domains', CompetencyDomainController::class)
            ->except(['show']);

        Route::resource('competency-indicators', CompetencyIndicatorController::class)
            ->except(['show']);

        Route::resource('grade-conversions', GradeConversionController::class)
            ->except(['show']);
    });

Route::middleware(['auth', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {
        Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');

        Route::get('/input-nilai', [InputNilaiController::class, 'index'])
            ->name('input-nilai.index');

        Route::get('/input-nilai/{student}', [InputNilaiController::class, 'edit'])
            ->name('input-nilai.edit');

        Route::put('/input-nilai/{student}', [InputNilaiController::class, 'update'])
            ->name('input-nilai.update');

        Route::post('/input-nilai/{student}/submit', [InputNilaiController::class, 'submit'])
            ->name('input-nilai.submit');

        Route::get('/grade-conversion/{score}', [InputNilaiController::class, 'convertGrade'])
            ->name('grade-conversion');

        Route::get('/report/{student}/academic', [ReportController::class, 'academic'])
            ->name('report.academic.pdf');

        Route::get('/report/{student}/academic/word', [ReportController::class, 'academicWord'])
            ->name('report.academic.word');

        Route::get('/report/{student}/competency', [ReportController::class, 'competency'])
            ->name('report.competency.pdf');

        Route::get('/report/{student}/competency/word', [ReportController::class, 'competencyWord'])
            ->name('report.competency.word');

        Route::get('/report-archives', [Guru\ReportArchiveController::class, 'index'])
            ->name('report-archives.index');

        Route::get('/report-archives/download/{id}', [Guru\ReportArchiveController::class, 'download'])
            ->name('report-archives.download');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
