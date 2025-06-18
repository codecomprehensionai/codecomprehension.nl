<?php

namespace App\Services\Canvas;

class CanvasAutoGrader
{
    public function grade($answer): int
    {
        // TODO: real grading logic
        return random_int(70, 100);
    }
}
