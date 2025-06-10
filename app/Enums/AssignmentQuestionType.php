<?php

namespace App\Enums;

enum AssignmentQuestionType: string
{
    case CodeExplanation = 'code_explanation';
    case MultipleChoice = 'multiple_choice';
    case FillInTheBlanks = 'fill_in_the_blanks';

    public function getDescription(): string
    {
        return match ($this) {
            default => 'TODO: write description',
        };
    }
}
