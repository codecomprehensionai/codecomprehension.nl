<?php

namespace App\Livewire;

use App\Enums\QuestionLanguage;
use App\Enums\QuestionLevel;
use App\Enums\QuestionType;
use App\Models\Assignment;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use Livewire\Component;

class AssignmentTeacher extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Assignment $assignment;
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->assignment->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->record($this->assignment)
            ->components([
                Section::make(config('app.name'))
                    ->description($this->assignment->title),

                Repeater::make('questions')
                    ->hiddenLabel()
                    ->relationship()
                    ->minItems(1)
                    ->maxItems(25)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Flex::make([
                            Select::make('language')
                                ->label(__('Language'))
                                ->options(QuestionLanguage::class)
                                ->default(QuestionLanguage::Python)
                                ->required()
                                ->native(false)
                                ->searchable(),

                            Select::make('level')
                                ->label(__('Level'))
                                ->options(QuestionLevel::class)
                                ->required()
                                ->native(false)
                                ->searchable(),

                            Select::make('type')
                                ->label(__('Type'))
                                ->options(QuestionType::class)
                                ->required()
                                ->native(false)
                                ->searchable(),

                            TextInput::make('score_max')
                                ->label(__('Max Score'))
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(10)
                                ->step(0.5)
                                ->default(1),
                        ]),

                        MarkdownEditor::make('question')
                            ->label(__('Question'))
                            ->required()
                            ->live()
                            ->toolbarButtons([
                                ['bold', 'italic', 'link'],
                                ['heading'],
                                ['codeBlock', 'bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ]),

                        MarkdownEditor::make('answer')
                            ->label(__('Answer'))
                            ->required()
                            ->toolbarButtons([
                                ['bold', 'italic', 'link'],
                                ['heading'],
                                ['codeBlock', 'bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ]),
                    ])
                    ->itemLabel(fn (array $state): ?string => Str::limit($state['question'], 100) ?? null)
                    ->extraItemActions([
                        // TODO: updateQuestion
                        Action::make('updateQuestion')
                            ->icon(Heroicon::Envelope)
                            ->schema([
                                //
                            ])
                            ->action(function (array $arguments, Repeater $component): void {
                                //
                            }),
                    ])
                    ->addActionLabel(__('Add Question')),
            ])
            ->statePath('data');
    }

    public function update(): void
    {
        $this->form->model($this->assignment)->saveRelationships();

        Notification::make()
            ->title(__('Assignment updated'))
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.assignment-teacher');
    }
}
