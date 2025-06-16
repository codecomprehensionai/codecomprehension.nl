<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LTI\LtiLaunchController;
use App\Http\Controllers\LTI\LtiCallbackController;

Route::prefix('v1')->as('v1:')->group(function (): void {
    Route::webhooks('webhooks/question', 'question.create');
    Route::webhooks('webhooks/question/update', 'question.update');

    Route::post('oidc', LtiLaunchController::class)->name('oidc.launch');
    Route::post('oidc/callback', LtiCallbackController::class)->name('oidc.callback');
});
