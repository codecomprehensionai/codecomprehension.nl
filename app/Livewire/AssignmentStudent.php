<?php

namespace App\Livewire;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AssignmentStudent extends Component
{
    public Assignment $assignment;
    public int $index = 0;
    public array $answers = [];

    public function mount()
    {
        $this->answers = array_fill(0, count($this->assignment->questions), null);
        foreach ($this->assignment->questions as $index => $question) {
            $this->answers[$index] = [
                'lti_id'      => $this->assignment->lti_id,
                'question_id' => $question->id,
                'user_id'     => Auth::id(),
                'answer'      => 'multiple_choice' === $question->type->value ? [] : '',
            ];
        }
    }

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

    public function submitAnswer()
    {
        DB::transaction(function () {
            foreach ($this->answers as $submission) {
                Submission::create($submission);
            }
        });

        return redirect()->route('assignment.results', ['assignment' => $this->assignment->id])
            ->with('success', 'Your answers have been submitted successfully.');
    }
}
