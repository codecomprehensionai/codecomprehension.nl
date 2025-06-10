<?php

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
            self::Beginner => 'Basic concepts and syntax',
            self::Intermediate => 'Moderate complexity with multiple concepts',
            self::Advanced => 'Complex scenarios and edge cases',
            self::Expert => 'Highly advanced topics and optimization',
        };
    }
}
