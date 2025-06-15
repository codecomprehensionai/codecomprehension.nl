<?php

use App\Http\Controllers\LTI\LtiCallbackController;
use App\Http\Controllers\LTI\LtiLaunchController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::post('auth/oidc', LtiLaunchController::class)->name('auth.launch');
    Route::post('auth/callback', LtiCallbackController::class)->name('auth.callback');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', fn () => Inertia::render('dashboard'))->name('dashboard');
});

Route::get('test', fn () => dd(Auth::user()))->name('test');
