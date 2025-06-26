<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\QuestionData;
use App\Models\Assignment;
use App\Models\JwtKey;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

// TODO: build this class
final readonly class QuestionGradeAction
{
    public function handle(Assignment $assignment, Question $question, string $submissionAnswer): array
    {
        $sub = Auth::id() ?? 'anonymous';
        $aud = 'https://llm.codecomprehension.nl';
        $token = JwtKey::first()->sign($sub, $aud, now()->addDay());

        $response = Http::withToken($token)
            ->connectTimeout(3)
            ->timeout(120)
            ->throw()
            ->post('https://llm.codecomprehension.nl/grade', [
                'assignment' => [
                    'title'       => $assignment->title,
                    'description' => $assignment->description,
                ],
                'question' => [
                    'language'  => $question->language,
                    'type'      => $question->type,
                    'level'     => $question->level,
                    'question'  => $question->question,
                    'answer'    => $question->answer,
                    'score_max' => $question->score_max,
                ],
                'submission' => [
                    'answer' => $submissionAnswer,
                ],
            ])
            ->json();

        return [
            'answer'   => $response['submission']['answer'],
            'feedback' => $response['submission']['feedback'],
            'score'    => $response['submission']['score'],
        ];
    }
}
