<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class SubmissionData extends Data
{
    public function __construct(
        public string $answer,
        public ?string $feedback = null,
        public bool $isCorrect = false,
    ) {}
}
