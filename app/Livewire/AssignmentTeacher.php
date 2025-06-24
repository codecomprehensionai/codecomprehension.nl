<?php

namespace App\Livewire;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Livewire\Component;

class AssignmentTeacher extends Component
{
    public Assignment $assignment;
    public string $view = 'teacher';
    public bool $showAddForm = false;

    public array $newQuestion = [
        'language' => '',
        'level' => '',
        'type' => '',
        'estimated_answer_duration' => 5,
        'question' => '',
        'code' => '',
        'options' => [],
    ];

    public function mount(Assignment $assignment, Request $request)
    {
        $this->assignment = $assignment;
        $this->view = $request->query('view', 'teacher');
    }

    public function toggleAddForm()
    {
        $this->showAddForm = !$this->showAddForm;
    }

    public function saveQuestion()
    {
        $this->validate([
            'newQuestion.language' => 'required|string',
            'newQuestion.level' => 'required|string',
            'newQuestion.type' => 'required|string',
            'newQuestion.estimated_answer_duration' => 'required|integer',
            'newQuestion.question' => 'required|string',
            'newQuestion.code' => 'nullable|string',
            'newQuestion.options' => 'nullable|array',
        ]);

        $this->assignment->questions()->create([
            'language' => $this->newQuestion['language'],
            'level' => $this->newQuestion['level'],
            'type' => $this->newQuestion['type'],
            'estimated_answer_duration' => $this->newQuestion['estimated_answer_duration'],
            'question' => $this->newQuestion['question'],
            'code' => $this->newQuestion['code'],
            'options' => $this->newQuestion['options'],
        ]);

        $this->reset('newQuestion');

        session()->flash('success', 'Question added successfully.');
    }

    public function render()
    {
        return view('livewire.assignment-teacher');
    }
}
