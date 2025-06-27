<?php

namespace App\Livewire;

use App\Actions\QuestionGenerateAction;
use App\Actions\QuestionUpdateAction;
use App\Data\QuestionData;
use App\Enums\QuestionLanguage;
use App\Enums\QuestionLevel;
use App\Enums\QuestionType;
use App\Models\Assignment;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
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
            ->statePath('data')
            ->record($this->assignment)
            ->disabled(fn (Assignment $record) => filled($record->published_at))
            ->components([
                Section::make(fn (Assignment $record) => $record->title)
                    ->description(function (Assignment $record): HtmlString {
                        return new HtmlString(
                            __(':count_questions questions, :sum_score_max total points', [
                                'count_questions' => $record->questions->count(),
                                'sum_score_max'   => $record->questions->sum('score_max'),
                            ])
                        );
                    })
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
                            ->label(__('Save & Publish'))
                            ->visible(fn (Assignment $record) => blank($record->published_at))
                            ->requiresConfirmation()
                            ->color('gray')
                            ->outlined()
                            ->action(function (Assignment $record, Action $action) {
                                $this->form->model($record)->saveRelationships();

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
                            ->visible(fn (Assignment $record) => blank($record->published_at))
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
                    ->reorderable()
                    ->orderColumn('order')
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
                            ->toolbarButtons([
                                ['bold', 'italic', 'link'],
                                ['heading'],
                                ['codeBlock', 'bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ]),

                        MarkdownEditor::make('answer')
                            ->label(__('Answer'))
                            ->toolbarButtons([
                                ['bold', 'italic', 'link'],
                                ['heading'],
                                ['codeBlock', 'bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ]),
                    ])
                    ->itemLabel(function (array $state): ?string {
                        return trans_choice(
                            '{1} Question :order (:score point) |[2,*] Question :order (:score points)',
                            $state['score_max'] ?? 0,
                            [
                                'order' => $state['order'] ?? 0,
                                'score' => $state['score_max'] ?? 0,
                            ]
                        );
                    })
                    ->extraItemActions([
                        Action::make('update')
                            ->label(__('Update'))
                            ->icon(Heroicon::Sparkles)
                            ->button()
                            ->outlined()
                            ->color('primary')
                            ->modalHeading(__('Update question'))
                            ->modalFooterActionsAlignment(Alignment::End)
                            ->visible(fn (Assignment $record) => blank($record->published_at))
                            ->schema([
                                Textarea::make('prompt')
                                    ->label(__('Prompt'))
                                    ->placeholder(__('e.g. Make this question more challenging...'))
                                    ->required(),
                            ])
                            ->action(function (array $arguments, array $data, Repeater $component): void {
                                $newQuestionData = $component->getItemState($arguments['item']);

                                $isNewQuestion = blank($newQuestionData['question']);

                                if ($isNewQuestion) {
                                    $newQuestionData = QuestionData::from([
                                        'language' => $newQuestionData['language'],
                                        'level'    => $newQuestionData['level'],
                                        'type'     => $newQuestionData['type'],
                                    ]);

                                    $responseQuestionData = app(QuestionGenerateAction::class)->handle(
                                        $this->assignment,
                                        $newQuestionData
                                    );
                                } else {
                                    // TODO: how to get original question data?

                                    $updateQuestionData = QuestionData::from([
                                        'language'  => $newQuestionData['language'],
                                        'level'     => $newQuestionData['level'],
                                        'type'      => $newQuestionData['type'],
                                        'question'  => $newQuestionData['question'],
                                        'answer'    => $newQuestionData['answer'],
                                        'score_max' => $newQuestionData['score_max'],
                                    ]);

                                    $responseQuestionData = app(QuestionUpdateAction::class)->handle(
                                        $this->assignment,
                                        $updateQuestionData,
                                        $updateQuestionData,
                                        $data['prompt']
                                    );
                                }

                                /* Update the question state */
                                $state = $component->getState();
                                $state[$arguments['item']] = array_merge($state[$arguments['item']], $responseQuestionData->toArray());
                                $component->state($state);

                                Notification::make()
                                    ->title(__('Question updated'))
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->addAction(
                        fn (Action $action) => $action
                            ->label(__('Add Question'))
                            ->color('primary'),
                    )
                    ->deleteAction(
                        fn (Action $action) => $action
                            ->requiresConfirmation(),
                    ),
            ]);
    }

    public function render()
    {
        return view('livewire.assignment-teacher');
    }
}
