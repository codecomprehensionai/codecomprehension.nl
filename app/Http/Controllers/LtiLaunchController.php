<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

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
            'lti_message_hint'   => 'nullable',
            'target_link_uri'    => 'required|url',
            'client_id'          => 'required',
            'deployment_id'      => 'required',
            'canvas_region'      => 'required',
            'canvas_environment' => 'required',
        ]);

        $state = Str::random();
        $nonce = Str::random();

        $parameters = [
            'scope'            => 'openid',
            'response_type'    => 'id_token',
            'client_id'        => $validated['client_id'],
            // 'redirect_uri'     => route('oidc.callback'),
            'redirect_uri'     => 'http://localhost:8000/api/v1/oidc/callback',
            'login_hint'       => $validated['login_hint'],
            'lti_message_hint' => $validated['lti_message_hint'],
            'state'            => $state,
            'response_mode'    => 'form_post',
            'nonce'            => $nonce,
            'prompt'           => 'none',
        ];

        // TODO: make sure cookies are encrypted

        return redirect(url()->query('https://sso.test.canvaslms.com/api/lti/authorize_redirect', $parameters))
            ->withCookie(Cookie::make('lti_state', $state, 10, httpOnly: true, sameSite: 'none'))
            ->withCookie(Cookie::make('lti_nonce', $nonce, 10, httpOnly: true, sameSite: 'none'));
    }
}
