<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', fn() => Inertia::render('dashboard'))->name('dashboard');
});

Route::get('test', fn() => dd(Auth::user()))->name('test');
