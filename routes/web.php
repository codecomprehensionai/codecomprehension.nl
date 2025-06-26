<?php

use App\Http\Controllers\JwksController;
use App\Http\Controllers\OidcController;
use App\Http\Middleware\EnsureUserTypeMiddleware;
use App\Livewire\AssignmentResults;
use App\Livewire\AssignmentStudent;
use App\Livewire\AssignmentTeacher;
use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;

Route::get('/', Welcome::class)->name('login');

Route::get('api/v1/oidc', [OidcController::class, 'launch'])->name('oidc.launch');
Route::post('api/v1/oidc', [OidcController::class, 'launch'])->name('oidc.launch');

Route::get('api/v1/oidc/callback', [OidcController::class, 'callback'])->name('oidc.callback');
Route::post('api/v1/oidc/callback', [OidcController::class, 'callback'])->name('oidc.callback');

Route::get('api/v1/jwks', JwksController::class)->name('oidc.jwks');
Route::get('api/v1/oidc/jwks', JwksController::class); /* Legacy */

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('student')->as('student.')->middleware(EnsureUserTypeMiddleware::class . ':student')->group(function () {
        Route::get('{assignment}', AssignmentStudent::class)->name('assignment');
        Route::get('{assignment}/results', AssignmentResults::class)->name('assignment.results');
    });

    Route::prefix('teacher')->as('teacher.')->middleware(EnsureUserTypeMiddleware::class . ':teacher')->group(function () {
        Route::get('{assignment}', AssignmentTeacher::class)->name('assignment');
    });
});
