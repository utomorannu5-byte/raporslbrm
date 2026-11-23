<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
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
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        Route::get('/school-profile/edit', [App\Http\Controllers\Admin\SchoolProfileController::class, 'edit'])
            ->name('school-profile.edit');

        Route::put('/school-profile', [App\Http\Controllers\Admin\SchoolProfileController::class, 'update'])
            ->name('school-profile.update');

        Route::resource('users', App\Http\Controllers\Admin\UserController::class)
            ->except(['show']);

        Route::resource('academic-years', App\Http\Controllers\Admin\AcademicYearController::class)
            ->except(['show']);

        Route::resource('subjects', App\Http\Controllers\Admin\SubjectController::class)
            ->except(['show']);
    });

Route::middleware(['auth', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {
        Route::get('/dashboard', GuruDashboardController::class)->name('dashboard');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
