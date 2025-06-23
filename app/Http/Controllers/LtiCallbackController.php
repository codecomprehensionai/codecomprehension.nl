<?php

namespace App\Http\Controllers;

use App\Data\LtiAssignmentData;
use App\Data\LtiCourseData;
use App\Data\LtiUserData;
use App\Models\Course;
use App\Models\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
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

        if ($validated['state'] !== $request->cookie('lti_state')) {
            return response('Invalid state.', 401)
                ->withCookie(Cookie::make('lti_state', '', -1, httpOnly: true, sameSite: 'none'))
                ->withCookie(Cookie::make('lti_nonce', '', -1, httpOnly: true, sameSite: 'none'));
        }

        $jwks = Cache::flexible(
            'cloudflare-access.jwks',
            [300, 3600],
            fn() => Http::get(config('services.canvas.endpoint') . '/api/lti/security/jwks')->throw()->json()
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

        // TODO: get deadline from somewhere
        $assignment = $course->assignments()->updateOrCreate(['lti_id' => $assignmentData->ltiId], [
            'lti_lineitem_endpoint' => $assignmentData->ltiLineitemEndpoint,
            'title'                 => $assignmentData->title,
            'description'           => $assignmentData->description,
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

        /**
         * TODO: check
         * This might introduce a bug when a student/teacher opens two tabs
         * with different courses/assignments. We will need to investigate
         * this later, but for now, we will just store the last accessed.
         */
        session([
            'course_id'     => $course->id,
            'assignment_id' => $assignment->id,
        ]);

        return redirect()->route('dashboard');
    }
}
