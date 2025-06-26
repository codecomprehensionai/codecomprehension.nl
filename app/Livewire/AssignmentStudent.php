<?php

namespace App\Livewire;

use App\Jobs\GradeAndSyncAssignmentJob;
use App\Models\Assignment;
use App\Models\Question;
use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;

class AssignmentStudent extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Assignment $assignment;
    public ?array $data = [];
    public bool $isSubmitted = false;

    public function mount(): void
    {
        $this->form->fill($this->assignment->attributesToArray());
        $this->isSubmitted = Submission::where('user_id', Auth::id())->whereIn('question_id', $this->assignment->questions->pluck('id'))->exists();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->record($this->assignment)
            ->disabled(fn() => $this->isSubmitted)
            ->components([
                Section::make(fn(Assignment $record) => $record->title)
                    ->description(function (Assignment $record): HtmlString {
                        return new HtmlString(
                            __(':count_questions questions, :sum_score_max total points', [
                                'count_questions' => $record->questions->count(),
                                'sum_score_max'   => $record->questions->sum('score_max'),
                            ])
                        );
                    })
                    ->afterHeader([
                        Action::make('submitted')
                            ->label(fn(Assignment $record) => __(
                                'Submitted at :date',
                                [
                                    'date' => Submission::where('user_id', Auth::id())
                                        ->whereIn('question_id', $this->assignment->questions->pluck('id'))
                                        ->first()
                                        ->created_at
                                        ->inTimezone()
                                        ->formatDateTime(),
                                ]
                            ))
                            ->visible(fn() => $this->isSubmitted)
                            ->disabled(fn() => $this->isSubmitted)
                            ->outlined(),

                        Action::make('submit')
                            ->label(__('Submit'))
                            ->hidden(fn() => $this->isSubmitted)
                            ->requiresConfirmation()
                            ->action(function () {
                                $data = $this->form->getState();

                                $submissions = collect();

                                foreach ($this->assignment->questions as $question) {
                                    $submission = $question->submissions()->create([
                                        'user_id' => Auth::id(),
                                        'answer'  => $data[$question->id]['answer'] ?? null,
                                    ]);

                                    $submissions->push($submission);
                                }

                                GradeAndSyncAssignmentJob::dispatch($this->assignment, $submissions);

                                Notification::make()
                                    ->title(__('Assignment submitted'))
                                    ->success()
                                    ->send();

                                $this->isSubmitted = true;
                                $this->dispatch('refresh');
                            }),
                    ]),

                Wizard::make(
                    fn(Assignment $record): array => $record->questions->map(
                        fn(Question $question) => Step::make(__('Question :order', ['order' => $question->order]))
                            ->description(trans_choice(
                                '{1} :score point|{2,*} :score points',
                                $question->score_max,
                                ['score' => $question->score_max]
                            ))
                            ->model($question)
                            ->schema(function (Question $record) {
                                return [
                                    Flex::make([
                                        Text::make(new HtmlString(__(
                                            '<strong>Language:</strong> :language',
                                            ['language' => $record->language->getLabel()]
                                        ))),

                                        Text::make(new HtmlString(__(
                                            '<strong>Type:</strong> :type',
                                            ['type' => $record->type->getLabel()]
                                        ))),

                                        Text::make(new HtmlString(__(
                                            '<strong>Level:</strong> :level',
                                            ['level' => $record->level->getLabel()]
                                        ))),
                                    ]),

                                    // TODO: use better markdown rendering
                                    Text::make(
                                        new HtmlString(
                                            '<div class="prose">' . Str::of($record->question)->markdown() . '</div>'
                                        ),
                                    ),

                                    MarkdownEditor::make(sprintf('%s.answer', $record->id))
                                        ->toolbarButtons([
                                            ['bold', 'italic', 'link'],
                                            ['heading'],
                                            ['codeBlock', 'bulletList', 'orderedList'],
                                            ['undo', 'redo'],
                                        ]),

                                    // TODO: show feedback
                                    // MarkdownEditor::make(sprintf('%s.feedback', $record->id))
                                    //     ->toolbarButtons([
                                    //         ['bold', 'italic', 'link'],
                                    //         ['heading'],
                                    //         ['codeBlock', 'bulletList', 'orderedList'],
                                    //         ['undo', 'redo'],
                                    //     ]),
                                ];
                            })
                    )->all()
                )
                    ->previousAction(
                        fn(Action $action) => $action
                            ->label(__('Previous'))
                            ->color('primary')
                            ->outlined(),
                    )
                    ->nextAction(
                        fn(Action $action) => $action
                            ->label(__('Next'))
                            ->color('primary')
                            ->outlined(),
                    )
                    ->skippable(),
            ]);
    }

    public function render()
    {
        // TODO: if not published show a message that the assignment is not published yet

        return view('livewire.assignment-student');
    }
}
