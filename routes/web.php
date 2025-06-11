<?php

use App\Http\Controllers\CanvasSocialiteController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('welcome'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('auth/redirect', [CanvasSocialiteController::class, 'redirect'])->name('auth.redirect');
    Route::get('auth/callback', [CanvasSocialiteController::class, 'callback'])->name('auth.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('auth/logout', [CanvasSocialiteController::class, 'logout'])->name('auth.logout');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', fn () => Inertia::render('dashboard'))->name('dashboard');
});
