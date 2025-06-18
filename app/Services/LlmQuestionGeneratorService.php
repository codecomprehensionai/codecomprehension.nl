<?php

namespace App\Services;

use App\Data\QuestionData;
use App\Models\Assignment;
use App\Models\Question;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LlmQuestionGeneratorService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('llm.base_url'), '/');
        $this->timeout = config('llm.timeout', 30);
    }

    /**
     * Generate a new question using LLM API
     */
    public function generateQuestion(
        Assignment $assignment,
        array $newQuestionParams,
        string $prompt,
        array $existingQuestions = []
    ): ?QuestionData {
        try {
            $requestData = $this->buildRequestData($assignment, $existingQuestions, $newQuestionParams, $prompt);

            // Debug: Log the request being sent for integration testing
            if (app()->environment('testing')) {
                Log::info('LLM API Request Data', ['request' => $requestData]);
            }

            Log::info('Sending question generation request to LLM', [
                'assignment_id' => $assignment->id,
                'question_type' => $newQuestionParams['type'] ?? 'unknown',
                'language' => $newQuestionParams['language'] ?? 'unknown'
            ]);

            $response = $this->makeRequest(
                'POST',
                '/question',
                $requestData
            );

            // Debug: Log the raw response for integration testing
            if (app()->environment('testing')) {
                Log::info('LLM API Raw Response', ['response' => $response]);
            }

            if ($response && isset($response['data']['question'])) {
                Log::info('Successfully generated question via LLM', [
                    'assignment_id' => $assignment->id,
                    'question_type' => $response['data']['question']['type'] ?? 'unknown'
                ]);

                return $this->parseResponse($response);
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Exception during LLM question generation', [
                'error' => $e->getMessage(),
                'assignment_id' => $assignment->id,
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * Update an existing question using LLM API
     */
    public function updateQuestion(
        Assignment $assignment,
        Question $existingQuestion,
        array $updateParams,
        string $prompt,
        array $contextQuestions = []
    ): ?QuestionData {
        try {
            $requestData = $this->buildUpdateRequestData(
                $assignment,
                $existingQuestion,
                $contextQuestions,
                $updateParams,
                $prompt
            );

            Log::info('Sending question update request to LLM', [
                'assignment_id' => $assignment->id,
                'question_id' => $existingQuestion->id
            ]);

            $response = $this->makeRequest(
                'PUT',
                '/question',
                $requestData
            );

            if ($response && isset($response['data']['question'])) {
                Log::info('Successfully updated question via LLM', [
                    'assignment_id' => $assignment->id,
                    'question_id' => $existingQuestion->id
                ]);

                return $this->parseResponse($response);
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Exception during LLM question update', [
                'error' => $e->getMessage(),
                'assignment_id' => $assignment->id,
                'question_id' => $existingQuestion->id
            ]);

            return null;
        }
    }

    /**
     * Check if LLM service is available
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/health");

            $isAvailable = $response->successful();

            Log::info('LLM service health check', [
                'available' => $isAvailable,
                'status' => $response->status(),
                'url' => "{$this->baseUrl}/health"
            ]);

            return $isAvailable;

        } catch (\Exception $e) {
            Log::warning('LLM service health check failed', [
                'error' => $e->getMessage(),
                'url' => "{$this->baseUrl}/health"
            ]);
            return false;
        }
    }

    /**
     * Make HTTP request to LLM API
     */
    private function makeRequest(string $method, string $endpoint, array $data): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->$method("{$this->baseUrl}{$endpoint}", $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('LLM API request failed', [
                'status' => $response->status(),
                'response' => $response->body(),
                'endpoint' => $endpoint
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('LLM API request exception', [
                'error' => $e->getMessage(),
                'endpoint' => $endpoint
            ]);
            return null;
        }
    }

    /**
     * Build request data for new question generation
     */
    private function buildRequestData(
        Assignment $assignment,
        array $existingQuestions,
        array $newQuestionParams,
        string $prompt
    ): array {
        return [
            'assignment' => [
                'id' => (string) $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description ?? '',
            ],
            'questions' => $this->formatExistingQuestions($existingQuestions),
            'new_question' => [
                'language' => ucfirst($newQuestionParams['language'] ?? 'python'), // Capitalize first letter
                'type' => $this->normalizeQuestionType($newQuestionParams['type'] ?? 'multiple_choice'),
                'level' => $newQuestionParams['level'] ?? 'beginner',
                'estimated_answer_duration' => $this->formatDuration($newQuestionParams['estimated_answer_duration'] ?? 180),
                'topics' => $newQuestionParams['topics'] ?? [],
                'tags' => $newQuestionParams['tags'] ?? [],
            ],
            'new_question_prompt' => $prompt,
        ];
    }

    /**
     * Build request data for question update
     */
    private function buildUpdateRequestData(
        Assignment $assignment,
        Question $existingQuestion,
        array $contextQuestions,
        array $updateParams,
        string $prompt
    ): array {
        return [
            'assignment' => [
                'id' => (string) $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description ?? '',
            ],
            'questions' => $this->formatExistingQuestions($contextQuestions),
            'existing_question' => [
                'id' => (string) $existingQuestion->id,
                'language' => ucfirst($existingQuestion->language->value),
                'type' => $this->normalizeQuestionType($existingQuestion->type->value),
                'level' => $existingQuestion->level->value,
                'estimated_answer_duration' => $this->formatDuration($existingQuestion->estimated_answer_duration),
                'topics' => $existingQuestion->topic ? [$existingQuestion->topic] : [],
                'tags' => $existingQuestion->tags ?? [],
                'question' => $existingQuestion->question,
                'explanation' => $existingQuestion->explanation,
                'code' => $existingQuestion->code,
                'options' => $existingQuestion->options,
                'answer' => $existingQuestion->answer,
            ],
            'update_question' => $updateParams,
            'update_question_prompt' => $prompt,
        ];
    }

    /**
     * Format existing questions for API request
     */
    private function formatExistingQuestions(array $questions): array
    {
        return collect($questions)->map(function ($question) {
            if ($question instanceof Question) {
                return [
                    'id' => (string) $question->id,
                    'language' => ucfirst($question->language->value),
                    'type' => $this->normalizeQuestionType($question->type->value),
                    'level' => $question->level->value,
                    'estimated_answer_duration' => $this->formatDuration($question->estimated_answer_duration),
                    'topics' => $question->topic ? [$question->topic] : [],
                    'tags' => $question->tags ?? [],
                    'question' => $question->question,
                    'explanation' => $question->explanation,
                    'code' => $question->code,
                    'options' => $question->options,
                    'answer' => $question->answer,
                ];
            }

            return $question; // Already formatted array
        })->toArray();
    }

    /**
     * Parse LLM API response into QuestionData
     */
    private function parseResponse(array $responseData): ?QuestionData
    {
        // Check if response has success field and is successful
        if (isset($responseData['success']) && !$responseData['success']) {
            Log::error('LLM API returned unsuccessful response', $responseData);
            return null;
        }

        // Handle the actual API response format: {success: true, data: {question: {...}}}
        $questionData = null;
        if (isset($responseData['data']['question'])) {
            $questionData = $responseData['data']['question'];
        } elseif (isset($responseData['question'])) {
            // Fallback for direct question format
            $questionData = $responseData['question'];
        }

        if (!$questionData) {
            Log::error('Invalid LLM response: missing question data', $responseData);
            return null;
        }

        try {
            // Normalize values to match enum expectations
            $language = strtolower($questionData['language']);
            $type = strtolower($questionData['type']);
            $level = strtolower($questionData['level']);
            
            // Handle type variations
            if ($type === 'fill_in_blank') {
                $type = 'fill_in_the_blanks';
            }

            // Parse duration back to seconds if it's a string
            $duration = $questionData['estimated_answer_duration'];
            if (is_string($duration)) {
                $duration = $this->parseDurationToSeconds($duration);
            }

            return new QuestionData(
                language: \App\Enums\QuestionLanguage::from($language),
                type: \App\Enums\QuestionType::from($type),
                level: \App\Enums\QuestionLevel::from($level),
                estimatedAnswerDuration: $duration,
                topic: $questionData['topics'][0] ?? null,
                tags: $questionData['tags'] ?? [],
                question: $questionData['question'],
                explanation: $questionData['explanation'] ?? null,
                code: $questionData['code'] ?? null,
                options: $questionData['options'] ?? null,
                answer: $questionData['answer'] ?? null,
            );
        } catch (\Exception $e) {
            Log::error('Failed to parse LLM response into QuestionData', [
                'error' => $e->getMessage(),
                'response_data' => $questionData
            ]);

            return null;
        }
    }

    /**
     * Normalize question type to match LLM API expectations
     */
    private function normalizeQuestionType(string $type): string
    {
        $typeMap = [
            'multiple_choice' => 'multiple_choice',
            'fill_in_the_blanks' => 'fill_in_blank',
            'true_false' => 'true_false',
            'short_answer' => 'short_answer',
        ];
        
        return $typeMap[$type] ?? $type;
    }

    /**
     * Format duration from seconds to human readable format
     */
    private function formatDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return "{$seconds} seconds";
        }
        
        $minutes = round($seconds / 60);
        return "{$minutes} minutes";
    }

    /**
     * Parse duration string back to seconds
     */
    private function parseDurationToSeconds(string $duration): int
    {
        // Handle formats like "3 minutes", "30 seconds", "2 minute", etc.
        if (preg_match('/(\d+)\s*(minute|minutes)/i', $duration, $matches)) {
            return (int)$matches[1] * 60;
        }
        
        if (preg_match('/(\d+)\s*(second|seconds)/i', $duration, $matches)) {
            return (int)$matches[1];
        }
        
        // If no pattern matches, assume it's already in seconds or return default
        return is_numeric($duration) ? (int)$duration : 180;
    }
}
