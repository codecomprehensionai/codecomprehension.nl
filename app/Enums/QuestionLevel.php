<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuestionLevel: string implements HasLabel
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
}
