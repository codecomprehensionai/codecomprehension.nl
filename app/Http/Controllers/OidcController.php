<?php

namespace App\Http\Controllers;

use App\Data\LtiAssignmentData;
use App\Data\LtiCourseData;
use App\Data\LtiUserData;
use App\Enums\UserType;
use App\Models\Course;
use App\Models\Question;
use App\Models\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OidcController
{
    public function launch(Request $request)
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
            'redirect_uri'     => route('oidc.callback'),
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

    /**
     * https://developerdocs.instructure.com/services/canvas/external-tools/lti/file.lti_launch_overview
     */
    public function callback(Request $request)
    {
        $validated = $request->validate([
            'authenticity_token' => 'required',
            'id_token'           => 'required',
            'state'              => 'required',
            'lti_storage_target' => 'nullable',
        ]);

        if ($validated['state'] !== $request->cookie('lti_state')) {
            return response('Invalid state.', 401)
                ->withCookie(Cookie::make('lti_state', '', -1, httpOnly: true, sameSite: 'none'))
                ->withCookie(Cookie::make('lti_nonce', '', -1, httpOnly: true, sameSite: 'none'));
        }

        $jwks = Cache::flexible(
            'cloudflare-access.jwks',
            [300, 3600],
            fn () => Http::get(config('services.canvas.endpoint') . '/api/lti/security/jwks')->throw()->json()
        );

        $jwt = JWT::decode($validated['id_token'], JWK::parseKeySet($jwks));

        if ($jwt->iss !== config('services.canvas.issuer')) {
            abort(401, "Provided issuer {$jwt->iss} is not valid.");
        }

        if ($jwt->nonce !== $request->cookie('lti_nonce')) {
            return response("Provided nonce {$jwt->nonce} is not valid.", 401)
                ->withCookie(Cookie::make('lti_state', '', -1, httpOnly: true, sameSite: 'none'))
                ->withCookie(Cookie::make('lti_nonce', '', -1, httpOnly: true, sameSite: 'none'));
        }

        $courseData = LtiCourseData::fromJwt($jwt);
        $assignmentData = LtiAssignmentData::fromJwt($jwt);
        $userData = LtiUserData::fromJwt($jwt);

        $course = Course::updateOrCreate(['lti_id' => $courseData->ltiId], [
            'title' => $courseData->title,
        ]);

        $assignment = $course->assignments()->updateOrCreate(['lti_id' => $assignmentData->ltiId], [
            'lti_lineitem_endpoint' => $assignmentData->ltiLineitemEndpoint,
            'title'                 => $assignmentData->title,
            'description'           => $assignmentData->description,
        ]);

        $assignment->questions()->delete();
        Question::factory()->count(5)->create([
            'assignment_id' => $assignment->id,
        ]);

        $user = User::updateOrCreate(['lti_id' => $userData->ltiId], [
            'type'              => $userData->type,
            'name'              => $userData->name,
            'email'             => $userData->email,
            'email_verified_at' => now(),
            'password'          => Str::password(),
            'avatar_url'        => $userData->avatarUrl,
            'locale'            => $userData->locale,
        ]);

        Auth::login($user);

        return redirect()->route('assignment.student', $assignment);

        return match ($user->type) {
            UserType::Teacher => redirect()->route('assignment.teacher', $assignment),
            UserType::Student => redirect()->route('assignment.student', $assignment),
        };
    }
}
