<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\CarbonImmutable;
use App\Models\JwtKey;

class CanvasTokenService
{
    public static function get(): string
    {
        return Cache::remember('canvas.access_token', 55 * 60, function () {

            $clientAssertion = JwtKey::first()->sign(
                config('services.canvas.client_id'),
                config('services.canvas.token_aud'),
                CarbonImmutable::now()->addMinutes(5)
            );

            $res = Http::asForm()->post(
                config('services.canvas.token_aud'),
                [
                    'grant_type'             => 'client_credentials',
                    'client_assertion_type'  => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                    'client_assertion'       => $clientAssertion,
                    'scope'                  => config('services.canvas.scopes'),
                ]
            )->throw()->json();

            return $res['access_token'];
        });
    }
} 