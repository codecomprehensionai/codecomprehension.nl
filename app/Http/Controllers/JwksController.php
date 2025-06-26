<?php

/**
 * JwksController
 *
 * This controller handles the generation and serving of JSON Web Key Sets (JWKS).
 * It retrieves all JWT public keys from the database, formats them according to the JWKS specification,
 * and returns them as a JSON response.
 *
 * Methods:
 *   __invoke()
 *     - If no JWT keys exist in the database, creates a new one.
 *     - Retrieves all JWT keys, extracts their public key details, and formats them for JWKS.
 *     - Returns a JSON response containing the array of keys.
 */

namespace App\Http\Controllers;

use App\Models\JwtKey;

class JwksController
{
    public function __invoke()
    {
        if (0 === JwtKey::count()) {
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
