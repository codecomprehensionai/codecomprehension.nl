<?php

use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\TeacherDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('welcome'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', function () {
        Auth::loginUsingId(1);

        return to_route('dashboard');
    })
        ->name('login');

    // Route::get('login', [AuthenticatedSessionController::class, 'create'])
    //     ->name('login');

    // Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    // Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    //     ->name('logout');

    Route::post('logout', function () {
        Auth::logout();

        return to_route('home');
    })
        ->name('logout');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', fn () => Inertia::render('dashboard'))->name('dashboard');
    
    // Teacher dashboard
    Route::get('teacher', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
    
    // Student dashboard  
    Route::get('student', [StudentDashboardController::class, 'index'])->name('student.dashboard');
});
