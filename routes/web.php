<?php

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/**
 * Test call of the first API.
 * 
 */
Route::get('/test', function() {
    return response()->json(User::factory()->make()->toArray());
});