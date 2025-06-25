<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;

class AssignmentStudent extends Component
{
    public Assignment $assignment;
    public int $index = 0;
    public array $answer = [];
    public array $answers = [];

    public function mount()
    {
        $this->answers = array_fill(0, count($this->assignment->questions), null);
        foreach ($this->assignment->questions as $index => $question) {
            $this->answers[$index] = [
                'lti_id' => $this->assignment->lti_id,
                'question_id' => $question->id,
                'user_id' => Auth::id(),
                'answer' => $question->type->value === 'multiple_choice' ? [] : '',
                'selected_option' => [],
                'feedback' => '',
                'is_correct' => false,
            ];
        }
    }

    public function render()
    {
        return view('livewire.assignment-student');
    }

    public function nextQuestion()
    {
        // $this->createSubmission();
        if ($this->index < count($this->assignment->questions) - 1) {
            $this->index++;
            // $this->loadCurrentAnswer();
        }
    }

    public function previousQuestion()
    {
        // $this->createSubmission();
        if ($this->index > 0) {
            $this->index--;
            // $this->loadCurrentAnswer();
        }
    }

    private function loadCurrentAnswer()
    {
        $this->answer = $this->answers[$this->index]['answer'] ?? [];
    }

    public function submitAnswer()
    {
        // $this->createSubmission();
        dd($this->answers);
    }

    // private function getQuestionType()
    // {
    //     $question = $this->assignment->questions[$this->index] ?? null;
    //     if (!$question) {
    //         return null;
    //     }

    //     return $question->type;
    // }

    // private function createSubmission()
    // {
    //     $question = $this->assignment->questions[$this->index] ?? null;
    //     if (!$question) {
    //         return;
    //     }

    //     $this->answers[$this->index] = [
    //         'lti_id' => $this->assignment->lti_id,
    //         'question_id' => $question->id,
    //         'user_id' => Auth::id(),
    //         'answer' => $this->answer,
    //         'selected_option' => [],
    //         'feedback' => '',
    //         'is_correct' => false,
    //     ];
    // }
}
