<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LtiController;
use App\Http\Controllers\LtiTestController;
use App\Http\Controllers\LtiDebugController;

Route::get('/', function () {
    return view('welcome');
});

// LTI Routes
Route::prefix('auth')->group(function () {
    Route::get('oidc', [LtiController::class, 'oidcInitiation'])->name('lti.oidc');
    Route::post('oidc', [LtiController::class, 'oidcInitiation']);
    // Route::post('launch', [LtiController::class, 'launch'])->name('lti.launch');
    Route::post('callback', [LtiController::class, 'launch'])->name('lti.launch');
    Route::get('jwks', [LtiController::class, 'jwks'])->name('lti.jwks');
});

Route::get('lti', [LtiController::class, 'tool'])->name('lti.tool');
Route::get('lti/config', [LtiController::class, 'config'])->name('lti.config');

// Protected LTI API routes
Route::middleware(['lti'])->prefix('api/lti')->group(function () {
    Route::get('user', [LtiController::class, 'getUserInfo'])->name('lti.api.user');
    Route::get('course', [LtiController::class, 'getCourseInfo'])->name('lti.api.course');
    Route::post('grade', [LtiController::class, 'sendGrade'])->name('lti.api.grade');
});

// LTI Testing Routes (for development only - remove in production)
Route::prefix('lti/test')->group(function () {
    Route::get('/', [LtiTestController::class, 'dashboard'])->name('lti.test.dashboard');
    Route::post('oidc', [LtiTestController::class, 'simulateOidc'])->name('lti.test.oidc');
    Route::post('tool', [LtiTestController::class, 'testTool'])->name('lti.test.tool');
    Route::post('grade', [LtiTestController::class, 'testGradePassback'])->name('lti.test.grade');
    Route::get('clear', [LtiTestController::class, 'clearSession'])->name('lti.test.clear');
});

// LTI Debug Routes (for development only - remove in production)
Route::prefix('lti/debug')->group(function () {
    Route::any('/', [LtiDebugController::class, 'debug'])->name('lti.debug');
    Route::any('launch', [LtiDebugController::class, 'captureLaunch'])->name('lti.debug.launch');
});
