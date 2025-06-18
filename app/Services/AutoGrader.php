<?php

namespace App\Services;

class AutoGrader
{
    public function grade($answer): int
    {
        // TODO: real grading logic
        return random_int(70, 100);
    }
} 