<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum QuestionLevel: string implements HasDescription, HasLabel
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';
    case Expert = 'expert';

    public function getLabel(): string
    {
        return match ($this) {
            self::Beginner     => __('Beginner'),
            self::Intermediate => __('Intermediate'),
            self::Advanced     => __('Advanced'),
            self::Expert       => __('Expert'),
        };
    }

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
