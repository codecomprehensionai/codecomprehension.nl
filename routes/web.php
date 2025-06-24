<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JwksController;
use App\Http\Controllers\OidcController;
use App\Http\Controllers\WelcomeController;
use App\Livewire\AssignmentStudent;
use App\Livewire\AssignmentTeacher;
use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;

Route::get('/', Welcome::class)->name('login');

Route::post('api/v1/oidc', [OidcController::class, 'launch'])->name('oidc.launch');
Route::post('api/v1/oidc/callback', [OidcController::class, 'callback'])->name('oidc.callback');
Route::get('api/v1/jwks', JwksController::class)->name('oidc.jwks');
Route::get('api/v1/oidc/jwks', JwksController::class); /* Legacy */

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('{assignment}', AssignmentStudent::class)->name('assignment.student');
    Route::get('{assignment}/teacher', AssignmentTeacher::class)->name('assignment.teacher');
    // Route::put('teacher/assignment/{assignmentId}/questions', [TeacherDashboardController::class, 'UpdateAssignmentQuestion']);
});
