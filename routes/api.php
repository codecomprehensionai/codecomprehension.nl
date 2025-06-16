<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LTI\LtiLaunchController;
use App\Http\Controllers\LTI\LtiCallbackController;
use App\Http\Middleware\RequireJsonMiddleware;

Route::prefix('v1')->as('v1:')->group(function (): void {
    Route::post('oidc', LtiLaunchController::class)->withoutMiddleware(RequireJsonMiddleware::class)->name('oidc.launch');
    Route::post('oidc/callback', LtiCallbackController::class)->withoutMiddleware(RequireJsonMiddleware::class)->name('oidc.callback');
});
