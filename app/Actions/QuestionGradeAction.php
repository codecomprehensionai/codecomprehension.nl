<?php

/**
 * Handles updating and evaluating an existing question for a given assignment using an external LLM API.
 *
 * This action submits both the current and updated versions of a question, along with optional
 * prompting context, to an external grading service. It securely authenticates the request using
 * a signed JWT based on the current user or defaults to 'anonymous'. The updated and enriched
 * version of the question is returned as a QuestionData object.
 */

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
    public function handle(Assignment $assignment, QuestionData $existingQuestionData, QuestionData $updateQuestionData, string $updateQuestionPrompt = ''): QuestionData
    {
        $sub = Auth::id() ?? 'anonymous';
        $aud = 'https://llm.codecomprehension.nl';
        $token = JwtKey::first()->sign($sub, $aud, now()->addDay());

        $response = Http::withToken($token)
            ->connectTimeout(3)
            ->timeout(120)
            ->throw()
            ->put('https://llm.codecomprehension.nl/question', [
                'assignment' => [
                    'id'          => $assignment->id,
                    'title'       => $assignment->title,
                    'description' => $assignment->description,
                ],
                'questions' => $assignment->questions
                    ->map(fn (Question $question): array => QuestionData::from($question)->toArray())
                    ->toArray(),
                'existing_question'      => $existingQuestionData->toArray(),
                'update_question'        => $updateQuestionData->toArray(),
                'update_question_prompt' => $updateQuestionPrompt,
            ])
            ->json('data');

        return QuestionData::from($response['question']);
    }
}
