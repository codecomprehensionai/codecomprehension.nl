<?php

namespace App\Livewire;

use App\Models\Assignment;
use Livewire\Component;

class AssignmentStudent extends Component
{
    public Assignment $assignment;
    public $index = 0;

    // TODO: wizard met alle vragen (laat maximaal aantal punten zien)
    // TODO: als er een submission is laat dan vragen, antwoorden en feedback zien

    public function render()
    {
        return view('livewire.assignment-student');
    }
}
