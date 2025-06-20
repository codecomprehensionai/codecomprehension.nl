<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JwksController;
use App\Http\Controllers\LtiCallbackController;
use App\Http\Controllers\LtiLaunchController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\TeacherDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('api/v1/jwks', JwksController::class)->name('oidc.jwks');
Route::get('api/v1/oidc/jwks', JwksController::class); /* Legacy */

Route::get('/', [DashboardController::class, 'login'])->name('login');

Route::middleware(['guest'])->group(function () {
    Route::post('api/v1/oidc', LtiLaunchController::class)->name('oidc.launch');
    Route::post('api/v1/oidc/callback', LtiCallbackController::class)->name('oidc.callback');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', fn () => Inertia::render('dashboard'))->name('dashboard');

    Route::get('teacher', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
    Route::get('student', [StudentDashboardController::class, 'index'])->name('student.dashboard');
});
