<?php

use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\TeacherDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LTI\LtiLaunchController;
use App\Http\Controllers\LTI\LtiCallbackController;

Route::get('/', fn() => Inertia::render('welcome'))->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', fn() => Inertia::render('dashboard'))->name('dashboard');

    // Teacher dashboard
    Route::get('teacher', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');

    // Student dashboard
    Route::get('student', [StudentDashboardController::class, 'index'])->name('student.dashboard');
});
