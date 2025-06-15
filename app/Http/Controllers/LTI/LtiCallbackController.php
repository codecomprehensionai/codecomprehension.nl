<?php

namespace App\Http\Controllers\LTI;

use App\Data\LtiData;
use App\Models\LtiSession;
use App\Models\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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

        // TODO: check if session is used correctly and securely
        $session = LtiSession::query()
            ->where('state', $validated['state'])
            ->whereNowOrFuture('expires_at')
            ->firstOrFail();

        // TODO: add cache
        $jwks = Http::get('https://canvas.test.instructure.com/api/lti/security/jwks')
            ->throw()
            ->json();

        $payload = JWT::decode($validated['id_token'], JWK::parseKeySet($jwks));
        $ltiData = LtiData::fromJwt($payload);

        $user = User::firstOrCreate(['lti_id' => $ltiData->ltiId], [
            'lti_id'     => $ltiData->ltiId,
            'type'       => $ltiData->type,
            'name'       => $ltiData->name,
            'email'      => $ltiData->email,
            'password'   => Str::password(),
            'avatar_url' => $ltiData->avatarUrl,
            'locale'     => $ltiData->locale,
        ]);

        Auth::login($user);

        return redirect()->route('test'); // TODO: voor nu naar een test pagina
    }
}
