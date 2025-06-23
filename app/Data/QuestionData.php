<?php

namespace App\Data;

use App\Enums\QuestionLanguage;
use App\Enums\QuestionLevel;
use App\Enums\QuestionType;
use Spatie\LaravelData\Data;

class QuestionData extends Data
{
    public function __construct(
        /* Question metadata */
        public QuestionLanguage $language,
        public QuestionType $type,
        public QuestionLevel $level,
        public int $estimatedAnswerDuration,

        /* Question aidata */
        public ?string $topic,
        public ?array $tags,

        /* Question content */
        public string $question,
        public ?string $explanation = null,
        public ?string $code = null,
        public ?array $options = null,
        public ?string $answer = null,
    ) {
    }

    /**
     * Create a QuestionData instance from an array of parameters.
     */
    public static function fromArray(array $params): self
    {
        return new self(
            language: QuestionLanguage::from($params['language'] ?? 'python'),
            type: QuestionType::from($params['type'] ?? 'multiple_choice'),
            level: QuestionLevel::from($params['level'] ?? 'beginner'),
            estimatedAnswerDuration: $params['estimated_answer_duration'] ?? 3,
            topic: $params['topic'] ?? null,
            tags: $params['tags'] ?? [],
            question: $params['question'] ?? '',
            explanation: $params['explanation'] ?? null,
            code: $params['code'] ?? null,
            options: $params['options'] ?? null,
            answer: $params['answer'] ?? null,
        );
    }
}
