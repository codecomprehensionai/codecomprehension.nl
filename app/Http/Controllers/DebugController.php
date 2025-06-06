<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugController extends Controller
{
    public function sessionTest(Request $request)
    {
        // Set a test value in session
        if ($request->has('set')) {
            $value = $request->get('set');
            session(['test_value' => $value]);
            return response()->json([
                'action' => 'set',
                'value' => $value,
                'session_id' => session()->getId(),
                'session_data' => session()->all(),
            ]);
        }

        // Get the test value from session
        return response()->json([
            'action' => 'get',
            'test_value' => session('test_value'),
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'headers' => $request->headers->all(),
        ]);
    }

    public function ltiDebug(Request $request)
    {
        Log::info('LTI Debug Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'all_params' => $request->all(),
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
        ]);

        return response()->json([
            'message' => 'Debug info logged',
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'request_data' => $request->all(),
        ]);
    }
}
