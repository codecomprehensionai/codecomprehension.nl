<?php

namespace App\Services\Canvas;

use App\Models\JwtKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CanvasTokenService
{
    public static function get(): string
    {
        return Cache::flexible(
            'services.canvas.token',
            [55 * 60, 60 * 60],
            function () {
                $endpoint = config('services.canvas.endpoint') . '/login/oauth2/token';
                $token = JwtKey::first()->sign(config('services.canvas.client_id'), $endpoint, now()->addMinutes(5));

                return Http::asForm()->post($endpoint, [
                    'grant_type'            => 'client_credentials',
                    'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                    'client_assertion'      => $token,
                    'scope'                 => config('services.canvas.scopes'),
                ])->throw()->json('access_token');
            }
        );
    }
}
