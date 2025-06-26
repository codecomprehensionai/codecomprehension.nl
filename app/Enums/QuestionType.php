<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuestionType: string implements HasLabel
{
    case CodeExplanation = 'code_explanation';
    case MultipleChoice = 'multiple_choice';
    case FillInTheBlanks = 'fill_in_the_blanks';

    public function getLabel(): string
    {
        return match ($this) {
            self::CodeExplanation => __('Code Explanation'),
            self::MultipleChoice  => __('Multiple Choice'),
            self::FillInTheBlanks => __('Fill in the Blanks'),
        };
    }
}
