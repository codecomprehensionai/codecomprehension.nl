<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentGroupController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherOfController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    // User routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('{id}', [UserController::class, 'update']);
        Route::delete('{id}', [UserController::class, 'destroy']);
    });

    // Student routes
    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::get('{id}', [StudentController::class, 'show']);
        Route::post('/', [StudentController::class, 'store']);
        Route::delete('{id}', [StudentController::class, 'destroy']);
        Route::get('{id}/groups', [StudentController::class, 'groups']);
        Route::get('{id}/submissions', [StudentController::class, 'submissions']);
    });

    // Teacher routes
    Route::prefix('teachers')->group(function () {
        Route::get('/', [TeacherController::class, 'index']);
        Route::get('{id}', [TeacherController::class, 'show']);
        Route::post('/', [TeacherController::class, 'store']);
        Route::delete('{id}', [TeacherController::class, 'destroy']);
        Route::get('{id}/groups', [TeacherController::class, 'groups']);
        Route::get('{id}/submissions', [TeacherController::class, 'submissions']);
    });

    // Group routes
    Route::prefix('groups')->group(function () {
        Route::get('/', [GroupController::class, 'index']);
        Route::get('{id}', [GroupController::class, 'show']);
        Route::post('/', [GroupController::class, 'store']);
        Route::put('{id}', [GroupController::class, 'update']);
        Route::delete('{id}', [GroupController::class, 'destroy']);
        Route::get('{id}/assignments', [GroupController::class, 'assignments']);
        Route::get('{id}/students', [GroupController::class, 'students']);
        Route::get('{id}/teachers', [GroupController::class, 'teachers']);
    });

    // Language routes
    Route::prefix('languages')->group(function () {
        Route::get('/', [LanguageController::class, 'index']);
        Route::get('{id}', [LanguageController::class, 'show']);
        Route::post('/', [LanguageController::class, 'store']);
        Route::put('{id}', [LanguageController::class, 'update']);
        Route::delete('{id}', [LanguageController::class, 'destroy']);
        Route::get('{id}/assignments', [LanguageController::class, 'assignments']);
    });

    // Assignment routes
    Route::prefix('assignments')->group(function () {
        Route::get('/', [AssignmentController::class, 'home']);
        Route::get('{id}', [AssignmentController::class, 'show']);
        Route::post('/', [AssignmentController::class, 'store']);
        Route::put('{id}', [AssignmentController::class, 'update']);
        Route::delete('{id}', [AssignmentController::class, 'destroy']);
    });

    // Submission routes
    Route::prefix('submissions')->group(function () {
        Route::get('/', [SubmissionController::class, 'index']);
        Route::get('{id}', [SubmissionController::class, 'show']);
        Route::post('/', [SubmissionController::class, 'store']);
        Route::put('{id}', [SubmissionController::class, 'update']);
        Route::delete('{id}', [SubmissionController::class, 'destroy']);
    });

    // Student-Group relationship routes
    Route::prefix('student-groups')->group(function () {
        Route::get('/', [StudentGroupController::class, 'index']);
        Route::get('{id}', [StudentGroupController::class, 'show']);
        Route::post('/', [StudentGroupController::class, 'store']);
        Route::delete('{id}', [StudentGroupController::class, 'destroy']);
        Route::post('remove', [StudentGroupController::class, 'removeStudentFromGroup']);
    });

    // Teacher-Group relationship routes
    Route::prefix('teacher-groups')->group(function () {
        Route::get('/', [TeacherOfController::class, 'index']);
        Route::get('{id}', [TeacherOfController::class, 'show']);
        Route::post('/', [TeacherOfController::class, 'store']);
        Route::delete('{id}', [TeacherOfController::class, 'destroy']);
        Route::post('remove', [TeacherOfController::class, 'removeTeacherFromGroup']);
    });
});
