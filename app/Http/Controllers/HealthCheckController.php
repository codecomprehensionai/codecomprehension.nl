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
                $data = $response->json();

                return response()->json([
                    'status' => $data['status'] ?? 'unknown',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                ], 500);
            }
        } catch (Exception) {
            return response()->json([
                'status' => 'error',
            ], 500);
        }
    }
}
