<?php

/**
 * Data transfer object representing the full structure of a question in the system.
 *
 * This class defines the schema and transformation rules for question related data, including metadata,
 * AI-generated insights, and content for display or evaluation. It integrates with the Spatie Laravel Data
 * package for easy mapping, validation, and serialization.
 */


namespace App\Data;

use App\Enums\QuestionLanguage;
use App\Enums\QuestionLevel;
use App\Enums\QuestionType;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;

class QuestionData extends Data
{
    public function __construct(
        /* Question metadata */
        public QuestionLanguage $language,
        public QuestionType $type,
        public QuestionLevel $level,
        #[MapOutputName('estimated_answer_duration')]
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
    ) {}
}
