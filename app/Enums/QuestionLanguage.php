<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuestionLanguage: string implements HasLabel
{
    case Python = 'python';

    public function getLabel(): string
    {
        return match ($this) {
            self::Python => 'Python',
        };
    }
}
