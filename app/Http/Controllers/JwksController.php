<?php

namespace App\Http\Controllers;

use App\Enums\CryptoKeyType;
use App\Models\CryptoKey;

class JwksController
{
    public function __invoke()
    {
        $keys = CryptoKey::type(CryptoKeyType::JWT)->get()
            ->map(function (CryptoKey $key): array {
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
