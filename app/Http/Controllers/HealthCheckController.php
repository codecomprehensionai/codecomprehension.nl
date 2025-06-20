<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class HealthCheckController
{
    public function __invoke(): JsonResponse
    {
        try {
            $response = Http::get('https://llm.codecomprehension.nl/health');

            if ($response->successful()) {
                // You can check the returned JSON if you want
                $data = $response->json();

                return response()->json([
                    'status' => $data['status'] ?? 'unknown',
                    'message' => $data['message'] ?? 'External service status',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'External health check failed',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Health check exception: ' . $e->getMessage(),
            ], 500);
        }
    }
}
