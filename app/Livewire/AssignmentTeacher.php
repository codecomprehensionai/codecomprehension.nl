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
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
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
            ->disabled(fn(Assignment $record) => filled($record->published_at))
            ->components([
                Section::make(fn(Assignment $record) => $record->title)
                    ->description(function (Assignment $record): HtmlString {
                        return new HtmlString(
                            __(':count_questions questions, :sum_score_max total score', [
                                'count_questions' => $record->questions->count(),
                                'sum_score_max' => $record->questions->sum('score_max'),
                            ])
                        );
                    })
                    ->afterHeader([
                        Action::make('published')
                            ->label(fn(Assignment $record) => __(
                                'Published at :date',
                                ['date' => $record->published_at->inTimezone()->formatDateTime()]
                            ))
                            ->visible(fn(Assignment $record) => filled($record->published_at))
                            ->disabled(fn(Assignment $record) => filled($record->published_at))
                            ->outlined(),

                        Action::make('publish')
                            ->label(__('Publish'))
                            ->visible(fn(Assignment $record) => blank($record->published_at))
                            ->requiresConfirmation()
                            ->color('gray')
                            ->outlined()
                            ->action(function (Assignment $record) {
                                $record->published_at = now();
                                $record->save();

                                Notification::make()
                                    ->title(__('Assignment published'))
                                    ->success()
                                    ->send();
                            }),

                        Action::make('save')
                            ->label(__('Save'))
                            ->visible(fn(Assignment $record) => blank($record->published_at))
                            ->action(function (Assignment $record) {
                                $this->form->model($record)->saveRelationships();

                                Notification::make()
                                    ->title(__('Assignment saved'))
                                    ->success()
                                    ->send();
                            }),
                    ]),

                Repeater::make('questions')
                    ->hiddenLabel()
                    ->relationship()
                    ->minItems(1)
                    ->maxItems(25)
                    ->collapsible()
                    ->collapsed()
                    ->cloneable()
                    ->reorderable(true)
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
                                ->live()
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
                        $score = $state['score_max'] ?? '?';
                        ray($state);
                        $label = Str::limit($state['question'] ?? '', 100);

                        return "{$score} pts: {$label}";
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
                        fn(Action $action) => $action
                            ->label(__('Add Question'))
                            ->color('primary'),
                    )
                    ->deleteAction(
                        fn(Action $action) => $action
                            ->requiresConfirmation(),
                    ),
            ])
            ->statePath('data');
    }

    public function render()
    {
        return view('livewire.assignment-teacher');
    }
}
