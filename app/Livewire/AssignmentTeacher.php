<?php

namespace App\Livewire;

use App\Actions\QuestionGenerateAction;
use App\Actions\QuestionUpdateAction;
use App\Data\QuestionData;
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
                                'sum_score_max'   => $record->questions->sum('score_max'),
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
                            ->action(function (Assignment $record, Action $action) {
                                if ($record->questions->isEmpty()) {
                                    Notification::make()
                                        ->title(__('Cannot publish without questions'))
                                        ->danger()
                                        ->send();

                                    $action->cancel();

                                    return;
                                }

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
                                ->default(QuestionLevel::Beginner)
                                ->required()
                                ->native(false)
                                ->searchable(),

                            Select::make('type')
                                ->label(__('Type'))
                                ->options(QuestionType::class)
                                ->default(QuestionType::CodeExplanation)
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
                        Action::make('generate')
                            ->label(__('AI Update'))
                            ->icon(Heroicon::Bolt)
                            ->modalHeading(__('Update Question with Prompt'))
                            ->schema([
                                TextInput::make('prompt')
                                    ->label(__('Prompt'))
                                    ->placeholder(__('e.g. Make this question more challenging...'))
                                    ->required(),
                            ])
                            ->action(function (array $arguments, $record, array $data, Repeater $component): void {
                                $itemKey = $arguments['item'] ?? null;
                                $fullState = $component->getRawState();

                                $item = $fullState[$itemKey] ?? [];

                                $isNew = blank($item['question'] ?? '') && blank($item['answer'] ?? '');
                                if ($isNew) {
                                    $questionData = QuestionData::from([
                                        'language' => QuestionLanguage::tryFrom($item['language'] ?? ''),
                                        'level'    => QuestionLevel::tryFrom($item['level'] ?? ''),
                                        'type'     => QuestionType::tryFrom($item['type'] ?? ''),
                                    ]);

                                    $response = app(QuestionGenerateAction::class)->handle(
                                        $this->assignment,
                                        $questionData
                                    );
                                } else {
                                    $existing = QuestionData::from([
                                        'language'  => QuestionLanguage::tryFrom($item['language'] ?? ''),
                                        'level'     => QuestionLevel::tryFrom($item['level'] ?? ''),
                                        'type'      => QuestionType::tryFrom($item['type'] ?? ''),
                                        'question'  => $item['question'],
                                        'answer'    => $item['answer'],
                                        'score_max' => $item['score_max'],
                                    ]);

                                    $update = QuestionData::from([
                                        ...$existing->toArray(),
                                    ]);

                                    $response = app(QuestionUpdateAction::class)->handle(
                                        $this->assignment,
                                        $existing,
                                        $update,
                                        $data['prompt']
                                    );
                                }
                                $payload = $response['question'] ?? [];

                                $updatedItem = array_merge($item, $payload['question']);
                                $fullState[$itemKey] = $updatedItem;
                                $component->state($fullState);

                                Notification::make()
                                    ->title(__('Question updated'))
                                    ->success()
                                    ->send();
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
