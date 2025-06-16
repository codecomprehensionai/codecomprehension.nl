<?php

namespace App\Http\Controllers\LTI;

use App\Models\LtiSession;
use Illuminate\Http\Request;
use Illuminate\Support\Uri;

class LtiLaunchController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'iss'                => 'required',
            'login_hint'         => 'required',
            'lti_message_hint'       => 'nullable',
            'target_link_uri'    => 'required|url',
            'client_id'          => 'required',
            'deployment_id'      => 'required',
            'canvas_region'      => 'required',
            'canvas_environment' => 'required',
        ]);

        $session = LtiSession::create($validated);

        $parameters = [
            'scope'         => 'openid',
            'response_type' => 'id_token',
            'client_id'     => $session->client_id,
            'redirect_uri'  => route('v1:oidc.callback'),
            'login_hint'    => $session->login_hint,
            'lti_message_hint'  => $session->lti_message_hint,
            'state'         => $session->state,
            'response_mode' => 'form_post',
            'nonce'         => $session->nonce,
            'prompt'        => 'none',
        ];

        return redirect(URI::of('https://sso.test.canvaslms.com/api/lti/authorize_redirect')->withQuery($parameters));
    }
}
