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

        $endpoint = config('services.canvas.endpoint');

        $jwks = Cache::flexible(
            'cloudflare-access.jwks',
            [300, 3600],
            fn () => Http::get("{$endpoint}/api/lti/security/jwks")->throw()->json()
        );

        try {
            $jwt = JWT::decode($validated['id_token'], JWK::parseKeySet($jwks));

            if ($jwt->iss !== $endpoint) {
                abort(401, 'Invalid issuer.');
            }

            if ($jwt->nonce !== $request->cookie('lti_nonce')) {
                abort(401, 'Invalid nonce.');
            }

            $courseData = LtiCourseData::fromJwt($jwt);
            $assignmentData = LtiAssignmentData::fromJwt($jwt);
            $userData = LtiUserData::fromJwt($jwt);
        } catch (Throwable) {
            abort(401, 'Invalid LTI token. View as Student is not supported.');
        }

        $course = Course::updateOrCreate(['lti_id' => $courseData->ltiId], [
            'title' => $courseData->title,
        ]);

        // TODO: get deadline from somewhere
        $assignment = $course->assignments()->updateOrCreate(['lti_id' => $assignmentData->ltiId], [
            'title'       => $assignmentData->title,
            'description' => $assignmentData->description,
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

        return redirect()->route('dashboard');
    }
}
