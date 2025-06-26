<?php

/**
 * Handles the generation of a new question for an assignment using an external LLM API.
 *
 * This action collects relevant data from an Assignment instance—including metadata,
 * existing questions, and a prompt for the new question—then securely communicates with
 * an external question-generation service via a signed JWT. The result is parsed into
 * a QuestionData object and returned.
 */

declare(strict_types=1);

namespace App\Actions;

use App\Data\QuestionData;
use App\Models\Assignment;
use App\Models\JwtKey;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

final readonly class QuestionGenerateAction
{
    public function handle(Assignment $assignment, QuestionData $newQuestionData, string $newQuestionPrompt = ''): QuestionData
    {
        $sub = Auth::id() ?? 'anonymous';
        $aud = 'https://llm.codecomprehension.nl';
        $token = JwtKey::first()->sign($sub, $aud, now()->addDay());

        $response = Http::withToken($token)
            ->connectTimeout(3)
            ->timeout(120)
            ->throw()
            ->post('https://llm.codecomprehension.nl/question', [
                'assignment' => [
                    'id'          => $assignment->id,
                    'title'       => $assignment->title,
                    'description' => $assignment->description,
                ],
                'questions' => $assignment->questions
                    ->map(fn (Question $question): array => QuestionData::from($question)->toArray())
                    ->toArray(),
                'new_question'        => $newQuestionData->toArray(),
                'new_question_prompt' => $newQuestionPrompt,
            ])
            ->json('data');

        return QuestionData::from($response['question']);
    }
}
