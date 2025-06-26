<?php

/**
 * AssignmentTeacher
 *
 * This Livewire component handles the display of assignment details for teachers.
 * It allows teachers to view the assignment and its associated questions.
 */

namespace App\Livewire;

use App\Models\Assignment;
use Livewire\Component;

class AssignmentTeacher extends Component
{
    public Assignment $assignment;

    public function render()
    {
        return view('livewire.assignment-teacher');
    }
}
