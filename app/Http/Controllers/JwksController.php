<?php

namespace App\Http\Controllers;

use App\Models\JwtKey;

class JwksController
{
    public function __invoke()
    {
        if (JwtKey::count() === 0) {
            JwtKey::create();
        }
        
        $keys = JwtKey::get()->map(function (JwtKey $key): array {
            $resource = openssl_pkey_get_public($key->public_key);
            $details = openssl_pkey_get_details($resource);

            return [
                'kty' => 'EC',
                'alg' => 'ES256',
                'crv' => 'P-256',
                'use' => 'sig',
                'kid' => $key->id,
                'x'   => base64_encode($details['ec']['x']),
                'y'   => base64_encode($details['ec']['y']),
            ];
        });

        return response()->json([
            'keys' => $keys,
        ]);
    }
}
