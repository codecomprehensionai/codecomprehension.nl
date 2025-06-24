<?php

namespace App\Livewire;

use App\Models\Assignment;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

class AssignmentTeacher extends Component implements HasSchemas
{
    use InteractsWithForms;

    public Assignment $assignment;
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                MarkdownEditor::make('content'),
                // ...
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        dd($this->form->getState());
    }

    public function render()
    {
        return view('livewire.assignment-teacher');
    }
}
