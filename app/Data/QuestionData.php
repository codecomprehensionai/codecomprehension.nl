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
}