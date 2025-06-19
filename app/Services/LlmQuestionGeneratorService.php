<?php

namespace App\Services;

use App\Models\JwtKey;
use App\Data\QuestionData;
use App\Models\Assignment;
use App\Models\Question;
use Illuminate\Support\Facades\Http;

class LlmQuestionGeneratorService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('llm.base_url'), '/');
        $this->timeout = config('llm.timeout', 100);
    }

    public function getJWT() {
        $endpoint = config('llm.base_url');
        $token = JwtKey::first()->sign(config('services.canvas.client_id'), $endpoint, now()->addMinutes(5));
        return $token;
    }

    public function generateQuestion(Assignment $assignment, array $params, string $prompt, array $existing = []): ?QuestionData
    {
        $data = $this->buildRequest($assignment, $existing, $params, $prompt);
        $jwt = $this->getJWT();
        Http::withToken($jwt);
        $response = $this->request('POST', '/question', $data);
        return $response ? $this->parse($response) : null;
    }

    public function updateQuestion(Assignment $assignment, Question $question, array $params, string $prompt, array $context = []): ?QuestionData
    {
        $data = $this->buildUpdateRequest($assignment, $question, $context, $params, $prompt);
        $jwt = $this->getJWT();
        Http::withToken($jwt);
        $response = $this->request('PUT', '/question', $data);
        return $response ? $this->parse($response) : null;
    }

    public function isAvailable(): bool
    {
        try {
            return Http::timeout(5)->get("{$this->baseUrl}/health")->successful();
        } catch (\Exception) {
            return false;
        }
    }

    private function request(string $method, string $endpoint, array $data): ?array
    {
        try {
            $response = Http::timeout($this->timeout)->$method("{$this->baseUrl}{$endpoint}", $data);
            return $response->successful() ? $response->json() : null;
        } catch (\Exception) {
            return null;
        }
    }

    private function buildRequest(Assignment $assignment, array $existing, array $params, string $prompt): array
    {
        return [
            'assignment' => ['id' => (string) $assignment->id, 'title' => $assignment->title, 'description' => $assignment->description ?? ''],
            'questions' => $this->formatQuestions($existing),
            'new_question' => [
                'language' => ucfirst($params['language'] ?? 'python'),
                'type' => $this->normalizeType($params['type'] ?? 'multiple_choice'),
                'level' => $params['level'] ?? 'beginner',
                'estimated_answer_duration' => $params['estimated_answer_duration'] ?? 3,
                'topics' => $params['topics'] ?? [],
                'tags' => $params['tags'] ?? [],
            ],
            'new_question_prompt' => $prompt,
        ];
    }

    private function buildUpdateRequest(Assignment $assignment, Question $question, array $context, array $params, string $prompt): array
    {
        return [
            'assignment' => ['id' => (string) $assignment->id, 'title' => $assignment->title, 'description' => $assignment->description ?? ''],
            'questions' => $this->formatQuestions($context),
            'existing_question' => $this->formatQuestion($question),
            'update_question' => $params,
            'update_question_prompt' => $prompt,
        ];
    }

    private function formatQuestions(array $questions): array
    {
        return collect($questions)->map(fn($q) => $q instanceof Question ? $this->formatQuestion($q) : $q)->toArray();
    }

    private function formatQuestion(Question $q): array
    {
        return [
            'id' => (string) $q->id,
            'language' => ucfirst($q->language->value),
            'type' => $this->normalizeType($q->type->value),
            'level' => $q->level->value,
            'estimated_answer_duration' => $q->estimated_answer_duration,
            'topics' => $q->topic ? [$q->topic] : [],
            'tags' => $q->tags ?? [],
            'question' => $q->question,
            'explanation' => $q->explanation ?? '',
            'code' => $q->code ?? '',
            'options' => $q->options ?? [],
            'answer' => $q->answer ?? '',
        ];
    }

    private function parse(array $response): ?QuestionData
    {
        if (isset($response['success']) && !$response['success']) return null;
        
        $data = $response['data']['question'] ?? $response['question'] ?? null;
        if (!$data) return null;

        try {
            $type = strtolower($data['type']);
            if ($type === 'fill_in_blank') $type = 'fill_in_the_blanks';

            return new QuestionData(
                language: \App\Enums\QuestionLanguage::from(strtolower($data['language'])),
                type: \App\Enums\QuestionType::from($type),
                level: \App\Enums\QuestionLevel::from(strtolower($data['level'])),
                estimatedAnswerDuration: $data['estimated_answer_duration'],
                topic: $data['topics'][0] ?? null,
                tags: $data['tags'] ?? [],
                question: $data['question'],
                explanation: $data['explanation'] ?? null,
                code: $data['code'] ?? null,
                options: $data['options'] ?? null,
                answer: $data['answer'] ?? null,
            );
        } catch (\Exception) {
            return null;
        }
    }

    private function normalizeType(string $type): string
    {
        return ['fill_in_the_blanks' => 'fill_in_blank'][$type] ?? $type;
    }
}
