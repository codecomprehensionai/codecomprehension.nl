<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class HealthCheckController
{
    public function __invoke(): JsonResponse
    {
        $data = Http::get('https://llm.codecomprehension.nl/health')->throw()->json();

        return response()->json([$data]);
    }
}
