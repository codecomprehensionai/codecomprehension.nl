<?php

/**
 * QuestionTeacher
 *
 * This Livewire component handles the display of a question for teachers.
 * It allows teachers to view the question details and associated assignment.
 */

namespace App\Livewire;

use App\Models\Assignment;
use Livewire\Component;

class QuestionTeacher extends Component
{
    public Assignment $assignment;

    public function render()
    {
        return view('livewire.question-teacher');
    }
}
