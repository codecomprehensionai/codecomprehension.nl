<?php

namespace App\Http\Controllers\LTI;

use App\Models\LtiSession;
use Illuminate\Http\Request;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;

class LtiCallbackController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'authenticity_token' => 'required',
            'id_token'           => 'required',
            'state'              => 'required',
            'lti_storage_target' => 'nullable',
        ]);

        $session = LtiSession::where('state', $validated['state'])->firstOrFail();

        // TODO: add cache
        $jwks = Http::get('https://canvas.test.instructure.com/api/lti/security/jwks')
            ->throw()
            ->json();

        $payload = JWT::decode($validated['id_token'], JWK::parseKeySet($jwks));
    }
}
