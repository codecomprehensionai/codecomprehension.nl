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
    public function handle(Assignment $assignment, QuestionData $existingQuestionData, QuestionData $updateQuestionData, string $updateQuestionPrompt = ''): QuestionData
    {
        $sub = Auth::id() ?? 'anonymous';
        $aud = 'https://llm.codecomprehension.nl';
        $token = JwtKey::first()->sign($sub, $aud, now()->addDay());

        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiIsImtpZCI6IjAxank2aGdlbWRzN2c5dzNhdzAzdzhmOXk2In0.eyJpc3MiOiJodHRwczovL2NvZGVjb21wcmVoZW5zaW9uLm5sIiwic3ViIjoiMTA0NDAwMDAwMDAwMDAwMzQwIiwiYXVkIjoiaHR0cHM6Ly9sbG0uY29kZWNvbXByZWhlbnNpb24ubmwiLCJleHAiOjE3NTA4Mzk3MzUsIm5iZiI6MTc1MDc1MzMzNSwiaWF0IjoxNzUwNzUzMzM1LCJqdGkiOiIwMWp5Z2dmcDYxZmt4Nm54MXF5bnNzN3IyciJ9.4Axr0pnx5HMN1l5m-NWPn_pQX7vkgMiwU-EmDhrYV93Bjeakz9y9xxAsuZg5-e9GAMNpL8q7uBKnM8QdwE-yXg';

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
