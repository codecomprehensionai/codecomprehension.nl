<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LtiController;
use App\Http\Controllers\LtiStorageController;

Route::get('/', function () {
    return redirect('/debug-session');
});

// Debug route to check session (remove after fixing)
Route::get('/debug-session', function () {
    return response()->json([
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'has_lti_context' => !empty(session('lti_context')),
        'lti_user_id' => session('lti_user_id'),
        'config' => [
            'driver' => config('session.driver'),
            'lifetime' => config('session.lifetime'),
            'same_site' => config('session.same_site'),
            'secure' => config('session.secure'),
            'path' => config('session.path'),
            'domain' => config('session.domain'),
        ]
    ]);
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

// LTI Platform Storage API
Route::prefix('lti/storage')->group(function () {
    Route::post('/', [LtiStorageController::class, 'store'])->name('lti.storage.store');
    Route::get('/', [LtiStorageController::class, 'retrieve'])->name('lti.storage.retrieve');
    Route::get('postmessage', [LtiStorageController::class, 'postMessage'])->name('lti.storage.postmessage');
});

// Protected LTI API routes
Route::middleware(['lti'])->prefix('api/lti')->group(function () {
    Route::get('user', [LtiController::class, 'getUserInfo'])->name('lti.api.user');
    Route::get('course', [LtiController::class, 'getCourseInfo'])->name('lti.api.course');
    Route::post('grade', [LtiController::class, 'sendGrade'])->name('lti.api.grade');
});
