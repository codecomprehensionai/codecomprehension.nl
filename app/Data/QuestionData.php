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
        public ?string $topic = null,
        public array $tags = [],
        public ?string $question = null,
        public ?string $explanation = null,
        public ?string $code = null,
        public ?array $options = null,
        public ?string $answer = null,
    ) {}
}
