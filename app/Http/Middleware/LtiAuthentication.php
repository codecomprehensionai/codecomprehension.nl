<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LtiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if LTI context exists in session
        if (!session()->has('lti_context')) {
            // If this is an API request, return JSON error
            if ($request->expectsJson() || str_starts_with($request->path(), 'api/')) {
                return response()->json([
                    'error' => 'LTI authentication required',
                    'message' => 'This resource requires LTI context. Please launch from your LMS.'
                ], 401);
            }

            // For web requests, show an error page or redirect to LTI info
            return response()->view('lti.error', [
                'title' => 'LTI Context Required',
                'message' => 'This application must be launched from a Learning Management System (LMS) like Canvas.',
                'instructions' => [
                    'Please access this tool through your LMS',
                    'If you are an instructor, add this tool to your course',
                    'If you are a student, click on the assignment link in your course'
                ]
            ], 403);
        }

        // Add LTI context to request for controllers to use
        $request->merge([
            'lti_context' => session('lti_context'),
            'lti_user_id' => session('lti_user_id'),
            'lti_context_id' => session('lti_context_id'),
            'lti_resource_link_id' => session('lti_resource_link_id'),
        ]);

        return $next($request);
    }
}
