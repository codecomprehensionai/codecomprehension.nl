<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LtiController;

Route::get('/', function () {
    return view('welcome');
});

// LTI Routes
Route::prefix('auth')->group(function () {
    Route::get('oidc', [LtiController::class, 'oidcInitiation'])->name('lti.oidc');
    Route::post('oidc', [LtiController::class, 'oidcInitiation']);
    Route::post('launch', [LtiController::class, 'launch'])->name('lti.launch');
    Route::get('jwks', [LtiController::class, 'jwks'])->name('lti.jwks');
});

Route::get('lti', [LtiController::class, 'tool'])->name('lti.tool');
Route::get('lti/config', [LtiController::class, 'config'])->name('lti.config');
