<?php

namespace App\Services;

use App\Data\QuestionData;
use App\Models\Assignment;
use App\Models\JwtKey;
use App\Models\Question;
use Exception;
use Illuminate\Support\Facades\Http;
use Log;

class LlmQuestionGeneratorService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('llm.base_url');
        $this->timeout = config('llm.timeout', 300); // Increased to 5 minutes for AI processing
    }

    /**
     * Generate a JWT token for authenticating with the LLM API.
     *
     * @return string
     */
    public function getJWT()
    {
        $endpoint = config('llm.base_url');
        $token = JwtKey::first()->sign(config('services.canvas.client_id'), $endpoint, now()->addMinutes(5));

        return $token;
    }

    /**
     * Generate a new question using the LLM API.
     */
    public function generateQuestion(Assignment $assignment, array $params, string $prompt, array $existing = []): ?QuestionData
    {
        Log::info('LlmQuestionGeneratorService generateQuestion started', [
            'assignment_id' => $assignment->id,
            'params' => $params,
            'prompt' => $prompt,
        ]);

        $data = $this->buildRequest($assignment, $existing, $params, $prompt);
        Log::info('LlmQuestionGeneratorService built request data', ['data' => $data]);

        $response = $this->request('POST', '/question', $data);
        Log::info('LlmQuestionGeneratorService got response', ['response' => $response]);

        if ($response) {
            $parsed = $this->parse($response);
            Log::info('LlmQuestionGeneratorService parsed response', ['parsed' => $parsed]);

            return $parsed;
        }
        Log::warning('LlmQuestionGeneratorService generateQuestion: response is null', [
            'assignment_id' => $assignment->id,
            'params' => $params,
            'prompt' => $prompt,
            'existing' => $existing,
        ]);

        return null;
    }

    /**
     * Update an existing question using the LLM API.
     */
    public function updateQuestion(Assignment $assignment, Question $question, array $params, string $prompt, array $context = []): ?QuestionData
    {
        Log::info('LlmQuestionGeneratorService updateQuestion started', [
            'assignment_id' => $assignment->id,
            'question_id' => $question->id,
            'params' => $params,
            'prompt' => $prompt,
            'context_count' => count($context),
        ]);

        $data = $this->buildUpdateRequest($assignment, $question, $context, $params, $prompt);
        Log::info('LlmQuestionGeneratorService built update request data', ['data' => $data]);

        $response = $this->request('PUT', '/question', $data);
        Log::info('LlmQuestionGeneratorService got update response', ['response' => $response]);

        if ($response) {
            $parsed = $this->parse($response);
            Log::info('LlmQuestionGeneratorService parsed update response', ['parsed' => $parsed]);

            return $parsed;
        }
        Log::warning('LlmQuestionGeneratorService updateQuestion: response is null', [
            'assignment_id' => $assignment->id,
            'question_id' => $question->id,
            'params' => $params,
            'prompt' => $prompt,
            'context' => $context,
        ]);

        return null;
    }

    /**
     * Check if the LLM API is available.
     */
    public function isAvailable(): bool
    {
        try {
            return Http::get("{$this->baseUrl}/health")->successful();
        } catch (Exception) {
            // Log the exception for debugging purposes
            Log::error('LlmQuestionGeneratorService health check failed.');

            return false;
        }
    }

    /**
     * Make an HTTP request to the LLM API.
     */
    private function request(string $method, string $endpoint, array $data): ?array
    {
        try {
            $jwt = $this->getJWT();
            $http = Http::timeout($this->timeout)->withToken($jwt);

            $response = $http->{$method}("{$this->baseUrl}{$endpoint}", $data);

            if (!$response->successful()) {
                throw new Exception("Request failed with status {$response->status()}: " . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            // Log the exception for debugging purposes
            Log::error('LlmQuestionGeneratorService request error: ' . $e->getMessage(), [
                'method' => $method,
                'endpoint' => $endpoint,
                'data' => $data,
                'exception' => $e,
            ]);

            return null;
        }
    }

    /**
     * Build the request payload for generating a new question.
     */
    private function buildRequest(Assignment $assignment, array $existing, array $params, string $prompt): array
    {
        return [
            'assignment' => ['id' => (string) $assignment->id, 'title' => $assignment->title, 'description' => $assignment->description ?? ''],
            'questions' => $this->formatQuestions($existing),
            'new_question' => [
                'language' => ucfirst($params['language'] ?? 'python'),
                'type' => $params['type'] ?? 'multiple_choice',
                'level' => $params['level'] ?? 'beginner',
                'estimated_answer_duration' => $this->formatDuration($params['estimated_answer_duration'] ?? 3),
                'topics' => $params['topics'] ?? [],
                'tags' => $params['tags'] ?? [],
            ],
            'new_question_prompt' => $prompt,
        ];
    }

    /**
     * Build the request payload for updating an existing question.
     */
    private function buildUpdateRequest(Assignment $assignment, Question $question, array $context, array $params, string $prompt): array
    {
        // Format the update parameters to ensure correct data types
        $formattedParams = $params;
        if (isset($formattedParams['estimated_answer_duration'])) {
            $formattedParams['estimated_answer_duration'] = $this->formatDuration($formattedParams['estimated_answer_duration']);
        }

        return [
            'assignment' => ['id' => (string) $assignment->id, 'title' => $assignment->title, 'description' => $assignment->description ?? ''],
            'questions' => $this->formatQuestions($context),
            'existing_question' => $this->formatQuestion($question),
            'update_question' => $formattedParams,
            'update_question_prompt' => $prompt,
        ];
    }

    /**
     * Format an array of questions for the LLM API.
     */
    private function formatQuestions(array $questions): array
    {
        return collect($questions)->map(fn($q) => $q instanceof Question ? $this->formatQuestion($q) : $q)->toArray();
    }

    /**
     * Format a single question for the LLM API.
     */
    private function formatQuestion(Question $q): array
    {
        return [
            'id' => (string) $q->id,
            'language' => ucfirst($q->language->value),
            'type' => $q->type->value,
            'level' => $q->level->value,
            'estimated_answer_duration' => $this->formatDuration($q->estimated_answer_duration),
            'topics' => $q->topic ? [$q->topic] : [],
            'tags' => $q->tags ?? [],
            'question' => $q->question,
            'explanation' => $q->explanation ?? '',
            'code' => $q->code ?? '',
            'options' => $q->options ?? [],
            'answer' => $q->answer ?? '',
        ];
    }

    /**
     * Parse the response from the LLM API into a QuestionData object.
     */
    private function parse(array $response): ?QuestionData
    {
        if (isset($response['success']) && !$response['success']) {
            return null;
        }

        $data = $response['data']['question'] ?? $response['question'] ?? null;
        if (!$data) {
            return null;
        }

        try {
            $type = mb_strtolower($data['type']);

            return new QuestionData(
                language: \App\Enums\QuestionLanguage::from(mb_strtolower($data['language'])),
                type: \App\Enums\QuestionType::from($type),
                level: \App\Enums\QuestionLevel::from(mb_strtolower($data['level'])),
                estimatedAnswerDuration: $this->parseDuration($data['estimated_answer_duration']),
                topic: $data['topics'][0] ?? null,
                tags: $data['tags'] ?? [],
                question: $data['question'],
                explanation: $data['explanation'] ?? null,
                code: $data['code'] ?? null,
                options: $data['options'] ?? null,
                answer: $data['answer'] ?? null,
            );
        } catch (Exception $e) {
            Log::error('LlmQuestionGeneratorService parse error: ' . $e->getMessage(), [
                'response' => $response,
                'exception' => $e,
            ]);

            return null;
        }
    }

    /**
     * Format duration as string for LLM API.
     */
    private function formatDuration(int $minutes): string
    {
        return $minutes . ' minutes';
    }

    /**
     * Parse duration string from LLM API back to integer.
     */
    private function parseDuration(string $duration): int
    {
        // Extract the number from strings like "3 minutes", "180 seconds", etc.
        if (preg_match('/(\d+)/', $duration, $matches)) {
            return (int) $matches[1];
        }

        // Fallback to 3 minutes if parsing fails
        return 3;
    }
}
