<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LTI\LtiLaunchController;
use App\Http\Controllers\LTI\LtiCallbackController;

Route::prefix('v1')->as('v1:')->group(function (): void {
    Route::post('oidc', LtiLaunchController::class)->name('oidc.launch');
    Route::post('oidc/callback', LtiCallbackController::class)->name('oidc.callback');
});
