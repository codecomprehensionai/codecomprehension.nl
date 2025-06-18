<?php

namespace App\Http\Controllers;

use App\Services\CanvasTokenService;
use App\Services\AutoGrader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SubmissionHandler
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'attempt_uuid' => 'required|uuid',
            'answer_blob'  => 'required',
            'line_item_id' => 'required|integer',
            'score_max'    => 'required|integer|min:1',
        ]);

        // 1) Grade
        $score = app(AutoGrader::class)->grade($data['answer_blob']);

        // 2) Token
        $token = CanvasTokenService::get();

        // 3) Build payload
        $launch = session('lti.launch');          // set in LtiCallbackController
        $payload = [
            'timestamp'        => now()->toIso8601String(),
            'userId'           => $launch->sub,   // UUID from launch
            'scoreGiven'       => $score,
            'scoreMaximum'     => $data['score_max'],
            'activityProgress' => 'Completed',
            'gradingProgress'  => 'FullyGraded',
            'comment'          => 'Auto-graded by Code Comprehension',
            'https://canvas.instructure.com/lti/submission' => [
                'new_submission'            => true,
                'submission_type'           => 'basic_lti_launch',
                'submission_data'           => route('lti.launch', ['attempt' => $data['attempt_uuid']]),
                'prioritize_non_tool_grade' => true,
            ],
        ];

        // 4) POST to Canvas
        $url = sprintf(
            'https://%s/api/lti/courses/%d/line_items/%d/scores',
            config('services.canvas.domain'),
            session('lti.course_id'),
            $data['line_item_id']
        );

        $res = Http::withToken($token)
                   ->acceptJson()
                   ->post($url, $payload)
                   ->throw()
                   ->json();

        return response()->json([
            'ok'        => true,
            'score'     => $score,
            'resultUrl' => $res['resultUrl'] ?? null,
        ]);
    }
} 