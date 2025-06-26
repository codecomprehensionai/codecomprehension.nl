<?php

/**
 * Enum representing the different types of questions supported by the system.
 *
 * This enum provides a structured way to classify questions by format, enabling
 * consistent handling of logic across question generation, evaluation, and display.
 * Each case corresponds to a distinct pedagogical format, and the included method
 * is intended to supply a user-friendly explanation of each type.
 */


namespace App\Enums;

enum QuestionType: string
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
