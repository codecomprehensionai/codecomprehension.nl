<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LtiDebugController extends Controller
{
    /**
     * Debug LTI requests to see what parameters are being sent
     */
    public function debug(Request $request)
    {
        $data = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'get_params' => $request->query->all(),
            'post_params' => $request->request->all(),
            'session' => $request->session()->all(),
        ];

        Log::info('LTI Debug Request', $data);

        // Return JSON for API calls, HTML for browser requests
        if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'message' => 'LTI Debug Information',
                'data' => $data,
                'timestamp' => now()->toISOString(),
            ]);
        }

        return view('lti.debug', [
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Capture any LTI launch attempt for debugging
     */
    public function captureLaunch(Request $request)
    {
        $data = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'all_params' => $request->all(),
            'id_token' => $request->input('id_token'),
            'state' => $request->input('state'),
            'session' => session()->all(),
        ];

        Log::info('LTI Launch Attempt Captured', $data);

        // Try to decode the JWT token if present
        if ($idToken = $request->input('id_token')) {
            try {
                $parts = explode('.', $idToken);
                if (count($parts) === 3) {
                    $header = json_decode(base64_decode(str_pad(strtr($parts[0], '-_', '+/'), strlen($parts[0]) % 4, '=', STR_PAD_RIGHT)), true);
                    $payload = json_decode(base64_decode(str_pad(strtr($parts[1], '-_', '+/'), strlen($parts[1]) % 4, '=', STR_PAD_RIGHT)), true);

                    Log::info('JWT Token Decoded', [
                        'header' => $header,
                        'payload' => $payload
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to decode JWT token', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'message' => 'LTI Launch captured for debugging',
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
