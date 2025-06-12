<?php

namespace App\Http\Controllers\LTI;

use App\Models\LtiSession;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Utility\LtiPayloadHelper;
use Illuminate\Support\Str;


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
        $payload->lti_user_id = $payload->{'https://purl.imsglobal.org/spec/lti/claim/lti1p1'}->user_id;
        $payload->user_type = LtiPayloadHelper::extractUserType($payload);

        $payloadValidator = Validator::make((array) $payload, [
            'user_type' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email',
            'picture' => 'nullable|url',
            'lti_user_id' => 'required|string',
            'locale' => 'nullable|string',
        ]);
        $payloadValidated = $payloadValidator->validate();

        $user = User::where('lti_user_id', $payloadValidated['lti_user_id'])->first();
        if (!$user) {
            $user = User::create([
                'type' => $payloadValidated['user_type'],
                'name' => $payloadValidated['name'],
                'password' => bcrypt(Str::random()), // Temporary password
                'email' => $payloadValidated['email'],
                'lti_user_id' => $payloadValidated['lti_user_id'],
                'avatar_url' => $payloadValidated['picture'] ?? null,
                'locale' => $payloadValidated['locale'] ?? 'en-GB',
            ]);
        }

        Auth::login($user, true);

        // https://developerdocs.instructure.com/services/canvas/external-tools/lti/file.lti_launch_overview
        return redirect()->route('test'); // voor nu naar een test pagina
    }
}
