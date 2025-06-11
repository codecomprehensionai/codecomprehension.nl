<?php

namespace App\Data;

use DateTimeInterface;
use Spatie\LaravelData\Data;

class AssignmentData extends Data
{
    public function __construct(
        public string $title,
        public ?string $description = null,
        public ?DateTimeInterface $publishedAt = null,
        public ?DateTimeInterface $deadlineAt = null,
    ) {}
}
