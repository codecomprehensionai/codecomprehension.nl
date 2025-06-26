<?php

namespace App\Livewire;

use App\Models\Assignment;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;

class AssignmentStudent extends Component implements HasActions, HasSchemas
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
            // ->disabled(fn(Assignment $record) => filled($record->published_at)) // TODO: disable if submission is completed
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
                        // TODO: look at user submission instead of assignment
                        Action::make('submitted')
                            ->label(fn (Assignment $record) => __(
                                'Submitted at :date',
                                ['date' => $record->published_at->inTimezone()->formatDateTime()]
                            ))
                            ->visible(fn (Assignment $record) => filled($record->published_at))
                            ->disabled(fn (Assignment $record) => filled($record->published_at))
                            ->outlined(),

                        // TODO: look at user submission instead of assignment
                        Action::make('submit')
                            ->label(__('Submit'))
                            ->visible(fn (Assignment $record) => blank($record->published_at))
                            ->requiresConfirmation()
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
                    ]),

                Wizard::make(
                    fn (Assignment $record): array => $record->questions
                        ->map(function (Question $question) {
                            return Step::make(__('Question :order', ['order' => $question->order]))
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
                                            ->label(__('Answer'))
                                            ->required()
                                            ->live()
                                            ->toolbarButtons([
                                                ['bold', 'italic', 'link'],
                                                ['heading'],
                                                ['codeBlock', 'bulletList', 'orderedList'],
                                                ['undo', 'redo'],
                                            ]),
                                    ];
                                });

                            // TODO: laatste pagine met overview van questions, maybe
                        })
                        ->all()
                )
                    ->previousAction(
                        fn (Action $action) => $action
                            ->label(__('Previous'))
                            ->color('primary')
                            ->outlined(),
                    )
                    ->nextAction(
                        fn (Action $action) => $action
                            ->label(__('Next'))
                            ->color('primary')
                            ->outlined(),
                    )
                    // TODO: do we keep this button, trigger modal to show confirmation
                    ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                            <x-filament::button
                                type="submit"
                                size="sm"
                            >
                                Submit
                            </x-filament::button>
                        BLADE)))
                    ->skippable(),
                // ->startOnStep(2) // TODO: get from assignment student progression
            ]);
    }

    public function render()
    {
        // TODO: if not published show a message that the assignment is not published yet

        return view('livewire.assignment-student');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        foreach ($this->assignment->questions as $question) {
            $question->submissions()->create([
                'user_id' => Auth::id(),
                'answer'  => $data[$question->id]['answer'] ?? null,
            ]);
        }
    }
}
