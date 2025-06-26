<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\AssignmentData;
use App\Data\QuestionData;
use App\Models\Assignment;
use App\Models\JwtKey;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

final readonly class QuestionUpdateAction
{
    public function handle(Assignment $assignment, QuestionData $existingQuestionData, QuestionData $updateQuestionData, string $updateQuestionPrompt = ''): QuestionData
    {
        $assignmentData = AssignmentData::from($assignment);

        $sub = Auth::id() ?? 'anonymous';
        $aud = 'https://llm.codecomprehension.nl';
        $token = JwtKey::first()->sign($sub, $aud, now()->addDay());

        $response = Http::withToken($token)
            ->connectTimeout(3)
            ->timeout(120)
            ->throw()
            ->put('https://llm.codecomprehension.nl/question', [
                'assignment' => $assignmentData->toArray(),
                'questions'  => $assignment->questions
                    ->map(fn(Question $question): array => QuestionData::from($question)->toArray())
                    ->toArray(),
                'original_question'      => $existingQuestionData->toArray(),
                'update_question'        => $updateQuestionData->toArray(),
                'update_question_prompt' => $updateQuestionPrompt,
            ])
            ->json('data');

        return QuestionData::from($response['question']);
    }
}
