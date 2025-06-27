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
        public QuestionLanguage $language,
        public QuestionType $type,
        public QuestionLevel $level,
        public ?string $question = null,
        public ?string $answer = null,
        #[MapInputName('score_max'), MapOutputName('score_max')]
        public ?string $scoreMax = null,
    ) {}
}
