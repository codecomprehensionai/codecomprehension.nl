<?php

use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\TeacherDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('loginError', fn() => 'todo')
    ->middleware('guest')
    ->name('login');

Route::get('login', function () {
    Auth::loginUsingId(1, true);

    return redirect('user');
});

Route::get('user', function () {
    return Auth::user() ?? 'unauthenticated';
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', fn() => Inertia::render('dashboard'))->name('dashboard');

    Route::get('teacher', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
    Route::get('student', [StudentDashboardController::class, 'index'])->name('student.dashboard');
});
