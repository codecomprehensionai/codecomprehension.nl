<?php
/**
 * RequireJsonMiddleware
 *
 * This middleware checks if the incoming request expects a JSON response.
 * If not, it throws a NotAcceptableHttpException with an error message.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class RequireJsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->wantsJson()) {
            throw new NotAcceptableHttpException('Please request with HTTP header: Accept: application/json');
        }

        return $next($request);
    }
}
