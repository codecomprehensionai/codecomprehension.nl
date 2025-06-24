<?php

namespace App\Data;

use App\Enums\QuestionLanguage;
use App\Enums\QuestionLevel;
use App\Enums\QuestionType;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;

class QuestionData extends Data
{
    public function __construct(
        /* Question metadata */
        public QuestionLanguage $language,
        public QuestionType $type,
        public QuestionLevel $level,
        #[MapInputName('score_max'), MapOutputName('score_max')]
        public float $scoreMax,

        /* Question content */
        public ?string $question,
        public ?string $answer,
    ) {}
}
