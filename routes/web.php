<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JwksController;
use App\Http\Controllers\OidcController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'login'])->name('login');

Route::post('api/v1/oidc', [OidcController::class, 'launch'])->name('oidc.launch');
Route::post('api/v1/oidc/callback', [OidcController::class, 'callback'])->name('oidc.callback');
Route::get('api/v1/jwks', JwksController::class)->name('oidc.jwks');
Route::get('api/v1/oidc/jwks', JwksController::class); /* Legacy */

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('{assignment}', [DashboardController::class, 'student'])->name('dashboard.student');
    Route::get('{assignment}/teacher', [DashboardController::class, 'teacher'])->name('dashboard.teacher');
});
