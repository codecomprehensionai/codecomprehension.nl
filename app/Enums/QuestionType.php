<?php

namespace App\Enums;

enum QuestionType: string
{
    case SingleChoice = 'single';
    case MultipleChoice = 'multiple';
    case Open = 'open';

    public function getDescription(): string
    {
        return match ($this) {
            default => 'TODO: write description',
        };
    }
}
