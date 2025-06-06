<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\LtiState;

/**
 * LTI Platform Storage API Controller
 * 
 * Implements the LTI Platform Storage specification for Safari compatibility
 * https://www.imsglobal.org/spec/lti/v1p3/#lti-platform-storage-api
 */
class LtiStorageController extends Controller
{
    /**
     * Store data for LTI launch (used by Canvas for Safari compatibility)
     * 
     * POST /lti/storage
     */
    public function store(Request $request)
    {
        try {
            $key = $request->input('key');
            $value = $request->input('value');
            $target_origin = $request->input('target_origin');

            if (!$key || !$value) {
                return response()->json([
                    'error' => 'invalid_request',
                    'error_description' => 'Missing required parameters: key and value'
                ], 400);
            }

            // Store with 10 minute expiration (same as state timeout)
            $cacheKey = 'lti_storage_' . hash('sha256', $key);
            Cache::put($cacheKey, [
                'value' => $value,
                'target_origin' => $target_origin,
                'stored_at' => now()
            ], 600); // 10 minutes

            Log::info('LTI Platform Storage: Data stored', [
                'key_hash' => $cacheKey,
                'target_origin' => $target_origin
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data stored successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('LTI Platform Storage: Store failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'server_error',
                'error_description' => 'Failed to store data'
            ], 500);
        }
    }

    /**
     * Retrieve stored data for LTI launch
     * 
     * GET /lti/storage
     */
    public function retrieve(Request $request)
    {
        try {
            $key = $request->input('key');

            if (!$key) {
                return response()->json([
                    'error' => 'invalid_request',
                    'error_description' => 'Missing required parameter: key'
                ], 400);
            }

            $cacheKey = 'lti_storage_' . hash('sha256', $key);
            $data = Cache::get($cacheKey);

            if (!$data) {
                return response()->json([
                    'error' => 'not_found',
                    'error_description' => 'Data not found or expired'
                ], 404);
            }

            Log::info('LTI Platform Storage: Data retrieved', [
                'key_hash' => $cacheKey
            ]);

            // Remove from cache after retrieval (one-time use)
            Cache::forget($cacheKey);

            return response()->json([
                'success' => true,
                'value' => $data['value'],
                'target_origin' => $data['target_origin'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('LTI Platform Storage: Retrieve failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'server_error',
                'error_description' => 'Failed to retrieve data'
            ], 500);
        }
    }

    /**
     * Handle postMessage API for iframe communication
     * 
     * GET /lti/storage/postmessage
     */
    public function postMessage(Request $request)
    {
        // Return HTML page that handles postMessage communication
        return view('lti.storage-postmessage');
    }
}
