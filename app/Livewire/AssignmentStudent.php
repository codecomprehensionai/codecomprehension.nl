<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Assignment;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use App\Models\Submission;

class AssignmentStudent extends Component implements HasForms
{
    use InteractsWithForms;

    public Assignment $assignment;
    public int $index = 0;
    public array $formData = [];
    public array $answers = [];

    public function mount()
    {
        $this->form->fill([
            'lti_id' => $this->assignment->lti_id,
            'question_id' => $this->assignment->questions[$this->index]->id ?? null,
            'user_id' => Auth::id(),
            'answer' => '',
            'feedback' => '',
            'is_correct' => false,
        ]);
    }

    public function render()
    {
        return view('livewire.assignment-student');
    }

    public function form(Schema $schema): Schema
    {
        $question = $this->assignment->questions[$this->index] ?? null;
        $type = $question->type->value ?? null;

        if (!$question) {
            return $schema->components([]);
        }

        $components = [
            Hidden::make('lti_id')->default($this->assignment->lti_id),
            Hidden::make('question_id')->default($question->id),
            Hidden::make('user_id')->default(Auth::id()),
        ];

        switch ($type) {
            case 'multiple_choice':
                $components[] =
                    CheckboxList::make('selected_option')
                    ->required()
                    ->options($question->options)
                    ->columns(2);
                break;

            case 'code_explanation':
            case 'fill_in_the_blanks':
                $components[] = TextInput::make('answer')
                    ->type('text')
                    ->required()
                    ->hiddenLabel();
                break;

            default:
                dd("Unsupported question type: $type");
                $components[] = TextInput::make('answer');
        }


        return $schema
            ->components($components)
            ->statePath('formData');
    }


    public function nextQuestion()
    {
        $this->saveCurrentAnswer();
        if ($this->index < count($this->assignment->questions) - 1) {
            $this->index++;
            $this->loadCurrentAnswer();
        }
    }

    public function previousQuestion()
    {
        $this->saveCurrentAnswer();
        if ($this->index > 0) {
            $this->index--;
            $this->loadCurrentAnswer();
        }
    }

    private function saveCurrentAnswer()
    {
        Submission::updateOrCreate(
            [
                'lti_id' => $this->assignment->lti_id,
                'question_id' => $this->assignment->questions[$this->index]->id,
                'user_id' => Auth::id(),
                'answer' => $this->form->getState()['answer'] ?? '',
            ],
            $this->form->getState()
        );
        $this->answers[$this->index] = $this->form->getState();
    }

    private function loadCurrentAnswer()
    {
        $question = $this->assignment->questions[$this->index] ?? null;
        if (!$question) {
            return;
        }
        if (isset($this->answers[$this->index])) {
            $this->form->fill($this->answers[$this->index]);
        } else {
            $this->form->fill([
                'lti_id' => $this->assignment->lti_id,
                'question_id' => $question->id,
                'user_id' => Auth::id(),
                'answer' => '',
                'selected_option' => [],
                'feedback' => '',
                'is_correct' => false,
            ]);
        }
    }
    public function submitAnswer()
    {
        $this->saveCurrentAnswer();
        dd($this->answers);
    }
}
