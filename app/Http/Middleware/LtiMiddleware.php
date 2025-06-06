<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LtiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if this is an LTI-protected route
        if ($this->isLtiProtectedRoute($request)) {
            // Verify LTI context exists
            if (!session()->has('lti_context')) {
                Log::warning('LTI context missing for protected route', [
                    'route' => $request->route()->getName(),
                    'url' => $request->url(),
                    'session_id' => session()->getId()
                ]);

                return response()->view('lti.error', [
                    'message' => 'LTI context required. Please launch this tool from your Learning Management System.'
                ], 403);
            }

            // Add LTI context to request for easy access
            $request->merge([
                'lti_context' => session('lti_context'),
                'lti_user_id' => session('lti_user_id'),
                'lti_context_id' => session('lti_context_id'),
                'lti_resource_link_id' => session('lti_resource_link_id'),
            ]);
        }

        return $next($request);
    }

    /**
     * Determine if the current route requires LTI context
     */
    private function isLtiProtectedRoute(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        // Define routes that require LTI context
        $ltiProtectedRoutes = [
            'lti.tool',
            // Add other routes that need LTI context here
        ];

        return in_array($routeName, $ltiProtectedRoutes);
    }
}
