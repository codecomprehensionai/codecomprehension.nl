<?php

namespace App\Http\Controllers\LTI;

use App\Data\LtiUserData;
use App\Models\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class LtiCallbackController
{
    /**
     * https://developerdocs.instructure.com/services/canvas/external-tools/lti/file.lti_launch_overview
     */
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'authenticity_token' => 'required',
            'id_token'           => 'required',
            'state'              => 'required',
            'lti_storage_target' => 'nullable',
        ]);

        if ($validated['state'] !== $request->cookie('lti_state')) {
            abort(401, 'Invalid state.');
        }

        $jwks = Cache::flexible(
            'cloudflare-access.jwks',
            [300, 3600],
            // TODO: configure url
            fn () => Http::get('https://canvas.test.instructure.com/api/lti/security/jwks')->throw()->json()
        );

        try {
            $jwt = JWT::decode($validated['id_token'], JWK::parseKeySet($jwks));

            // TODO: validate iss and nonce

            $data = LtiUserData::fromJwt($jwt);

            dd($jwt, $data);
        } catch (Throwable) {
            abort(401, 'Invalid LTI token.');
        }

        $user = User::updateOrCreate(['lti_id' => $data->ltiId], [
            'type'              => $data->type,
            'name'              => $data->name,
            'email'             => $data->email,
            'email_verified_at' => now(),
            'password'          => Str::password(),
            'avatar_url'        => $data->avatarUrl,
            'locale'            => $data->locale,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
