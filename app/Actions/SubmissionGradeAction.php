<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\AssignmentData;
use App\Data\QuestionData;
use App\Data\SubmissionData;
use App\Models\Assignment;
use App\Models\JwtKey;
use App\Models\Question;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

final readonly class SubmissionGradeAction
{
    public function handle(Submission $submission): SubmissionData
    {
        $submissionData = SubmissionData::from($submission);

        /** @var Question $question */
        $question = $submission->question;
        $questionData = QuestionData::from($question);

        /** @var Assignment $assignment */
        $assignment = $question->assignment;
        $assignmentData = AssignmentData::from($assignment);

        $sub = Auth::id() ?? 'anonymous';
        $aud = 'https://llm.codecomprehension.nl';
        $token = JwtKey::first()->sign($sub, $aud, now()->addDay());

        $response = Http::withToken($token)
            ->connectTimeout(3)
            ->timeout(120)
            ->throw()
            ->post('https://llm.codecomprehension.nl/grade', [
                'assignment' => $assignmentData->toArray(),
                'question'   => [
                    'language'  => $questionData->language,
                    'type'      => $questionData->type,
                    'level'     => $questionData->level,
                    'question'  => $questionData->question,
                    'answer'    => $questionData->answer,
                    'score_max' => (string) $questionData->scoreMax,
                ],
                'submission' => [
                    'answer' => $submissionData->answer,
                ],
            ])
            ->json('data');

        return SubmissionData::from($response['submission']);
    }
}
