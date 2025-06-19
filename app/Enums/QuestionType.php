<?php

namespace App\Enums;

enum QuestionType: string
{
    case CodeExplanation = 'code_explanation';
    case MultipleChoice = 'multiple_choice';
    case FillInTheBlanks = 'fill_in_blank';

    public function getDescription(): string
    {
        return match ($this) {
            default => 'TODO: write description',
        };
    }
}
