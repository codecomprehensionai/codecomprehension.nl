<?php

namespace App\Livewire;

use App\Models\Assignment;
use Livewire\Component;

class AssignmentStudent extends Component
{
    public Assignment $assignment;

    public function render()
    {
        return view('livewire.assignment-student');
    }
}
