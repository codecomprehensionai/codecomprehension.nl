<?php

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
