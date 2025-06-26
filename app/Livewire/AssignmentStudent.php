<?php

/**
 * AssignmentStudent
 *
 * This Livewire component handles the display and navigation of assignment questions for students.
 * It allows students to view questions, navigate to the next or previous question, and renders the component view.
 */

namespace App\Livewire;

use App\Models\Assignment;
use Livewire\Component;

class AssignmentStudent extends Component
{
    public Assignment $assignment;
    public $index = 0;

    public function render()
    {
        return view('livewire.assignment-student');
    }

    public function nextQuestion()
    {
        if ($this->index < count($this->assignment->questions) - 1) {
            ++$this->index;
        }
    }

    public function previousQuestion()
    {
        if ($this->index > 0) {
            --$this->index;
        }
    }
}
