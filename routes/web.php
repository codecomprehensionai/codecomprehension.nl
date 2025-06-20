<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JwksController;
use App\Http\Controllers\LtiCallbackController;
use App\Http\Controllers\LtiLaunchController;
use Illuminate\Support\Facades\Route;

Route::get('api/v1/jwks', JwksController::class)->name('oidc.jwks');
Route::get('api/v1/oidc/jwks', JwksController::class); /* Legacy */

Route::get('/', [DashboardController::class, 'login'])->name('login');

Route::middleware(['guest'])->group(function () {
    Route::post('api/v1/oidc', LtiLaunchController::class)->name('oidc.launch');
    Route::post('api/v1/oidc/callback', LtiCallbackController::class)->name('oidc.callback');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('{assignment}', [DashboardController::class, 'student'])->name('dashboard.student');
    Route::get('{assignment}/teacher', [DashboardController::class, 'teacher'])->name('dashboard.teacher');
});
