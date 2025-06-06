<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'lti' => \App\Http\Middleware\LtiMiddleware::class,
        ]);

        // Exclude LTI routes from CSRF protection
        $middleware->validateCsrfTokens(except: [
            'auth/oidc',
            'auth/launch',
            'auth/callback',
            'lti*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
