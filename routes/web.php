<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('welcome'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('auth/login', fn () => 'todo: display error "login through canvas"')->name('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::post('auth/logout', fn () => 'todo: handle logout')->name('auth.logout');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', fn () => Inertia::render('dashboard'))->name('dashboard');
});
