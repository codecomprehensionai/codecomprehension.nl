<?php

/**
 * Enum representing levels of question difficulty for assessments or exercises.
 *
 * This enum categorizes questions by conceptual depth and complexity, ranging from beginner
 * fundamentals to expert level challenges. It includes a utility method for returning
 * human-readable summaries of each level, which is useful for UI displays, filtering
 * and analytics.
 */

namespace App\Enums;

enum QuestionLevel: string
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';
    case Expert = 'expert';

    public function getDescription(): string
    {
        return match ($this) {
            self::Beginner     => 'Basic concepts and syntax',
            self::Intermediate => 'Moderate complexity with multiple concepts',
            self::Advanced     => 'Complex scenarios and edge cases',
            self::Expert       => 'Highly advanced topics and optimization',
        };
    }
}
