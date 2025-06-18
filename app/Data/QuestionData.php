<?php

namespace App\Data;

use App\Enums\QuestionLanguage;
use App\Enums\QuestionLevel;
use App\Enums\QuestionType;
use Spatie\LaravelData\Data;

class QuestionData extends Data
{
    public function __construct(
        public QuestionLanguage $language,
        public QuestionType $type,
        public QuestionLevel $level,
        public int $estimatedAnswerDuration,
        public ?string $topic,
        public array $tags,
        public string $question,
        public ?string $explanation,
        public ?string $code,
        public ?array $options,
        public ?string $answer,
    ) {}

    /**
     * Convert to array format suitable for database storage
     */
    public function toArray(): array
    {
        return [
            'language' => $this->language,
            'type' => $this->type,
            'level' => $this->level,
            'estimated_answer_duration' => $this->estimatedAnswerDuration,
            'topic' => $this->topic,
            'tags' => $this->tags,
            'question' => $this->question,
            'explanation' => $this->explanation,
            'code' => $this->code,
            'options' => $this->options,
            'answer' => $this->answer,
        ];
    }
}
