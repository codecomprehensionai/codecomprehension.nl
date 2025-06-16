<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->as('v1:')->group(function (): void {
    Route::webhooks('canvas/assignment', 'canvas.assignment.create');
    Route::webhooks('canvas/assignment/update', 'canvas.assignment.update');
});
