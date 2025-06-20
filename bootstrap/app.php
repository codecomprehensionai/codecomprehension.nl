<?php

use App\Http\Middleware\RequireJsonMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'api/v1/oidc',
            'api/v1/oidc/callback',
        ]);

        $middleware->api(prepend: [
            RequireJsonMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            /* Render JSON for API requests */
            if ($request->is('api/*')) {
                return true;
            }

            /* Render JSON for requests that expect JSON */
            return $request->expectsJson();
        });
    })->create();
