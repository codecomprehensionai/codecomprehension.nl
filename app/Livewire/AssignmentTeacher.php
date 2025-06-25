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
            ->disabled(fn (Assignment $record) => filled($record->published_at))
            ->components([
                // TODO: in blok met titel ook aantal vragen en totaal aantal punten tonen
                Section::make(config('app.name'))
                    ->afterHeader([
                        Action::make('published')
                            ->label(fn (Assignment $record) => __(
                                'Published at :date',
                                ['date' => $record->published_at->inTimezone()->formatDateTime()]
                            ))
                            ->visible(fn (Assignment $record) => filled($record->published_at))
                            ->disabled(fn (Assignment $record) => filled($record->published_at))
                            ->outlined(),

                        Action::make('publish')
                            ->label(__('Publish'))
                            ->visible(fn (Assignment $record) => blank($record->published_at))
                            ->requiresConfirmation() // TODO: modal is ugly
                            ->action(function () {
                                $this->assignment->published_at = now();
                                $this->assignment->save();

                                Notification::make()
                                    ->title(__('Assignment published'))
                                    ->success()
                                    ->send();
                            }),

                        Action::make('update')
                            ->label(__('Update'))
                            ->visible(fn (Assignment $record) => blank($record->published_at))
                            ->action(function () {
                                $this->form->model($this->assignment)->saveRelationships();

                                Notification::make()
                                    ->title(__('Assignment updated'))
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->description($this->assignment->title),

                Repeater::make('questions')
                    ->hiddenLabel()
                    ->relationship()
                    ->minItems(1)
                    ->maxItems(25)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        // TODO: check if FusedGroup is pretty
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
                    ->itemLabel(function (array $state): ?string {
                        // TODO: in itemLabel vraagnummer en aantal punten tonen
                        return Str::limit($state['question'], 100) ?? null;
                    })
                    ->extraItemActions([
                        // TODO: updateQuestion
                        Action::make('update')
                            ->icon(Heroicon::Envelope)
                            ->schema([
                                //
                            ])
                            ->action(function (array $arguments, Repeater $component): void {
                                // TODO: een vraag kunnen genereren
                                // TODO: bij een vraag een prompt kunnen uitvoeren om te updaten
                            }),
                    ])
                    ->addAction(
                        fn (Action $action) => $action
                            ->label(__('Add Question'))
                            ->color('primary'),
                    )
                    ->deleteAction(
                        fn (Action $action) => $action
                            ->requiresConfirmation(), // TODO: modal is ugly
                    ),
            ])
            ->statePath('data');
    }

    public function render()
    {
        return view('livewire.assignment-teacher');
    }
}
