<?php

namespace App\Livewire;

use App\Enums\QuestionLanguage;
use App\Enums\QuestionLevel;
use App\Enums\QuestionType;
use App\Models\Assignment;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
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
            ->disabled(fn (Assignment $record) => filled($record->published_at))
            ->components([
                Wizard::make(
                    fn (Assignment $record): array => $record->questions
                        ->map(function (Question $question) {
                            return Step::make(__('Question :order', ['order' => $question->order]))
                                ->description(trans_choice(
                                    '{1} :score point|{2,*} :score points',
                                    $question->score_max,
                                    ['score' => $question->score_max]
                                ))
                                ->schema([
                                    // TextInput::make('title')
                                    //     ->label('Question Title')
                                    //     ->required()
                                    //     ->default($question->title),
                                    // Select::make('type')
                                    //     ->label('Question Type')
                                    //     ->options(QuestionType::asSelectArray())
                                    //     ->default($question->type)
                                    //     ->required(),
                                    // Select::make('language')
                                    //     ->label('Programming Language')
                                    //     ->options(QuestionLanguage::asSelectArray())
                                    //     ->default($question->language)
                                    //     ->required(),
                                    // Select::make('level')
                                    //     ->label('Difficulty Level')
                                    //     ->options(QuestionLevel::asSelectArray())
                                    //     ->default($question->level)
                                    //     ->required(),
                                    // MarkdownEditor::make('description')
                                    //     ->label('Description')
                                    //     ->default($question->description)
                                    //     ->required(),
                                ]);
                        })
                        ->all()
                )
                    ->previousAction(
                        fn (Action $action) => $action
                            ->label(__('Previous'))
                            ->outlined(),
                    )
                    ->nextAction(
                        fn (Action $action) => $action
                            ->label(__('Next'))
                            ->outlined(),
                    )
                    ->skippable()
                    // ->startOnStep(2) // TODO: get from assignment student progression
                    ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                            <x-filament::button
                                type="submit"
                                size="sm"
                            >
                                Submit
                            </x-filament::button>
                        BLADE))),
            ]);
    }

    public function render()
    {
        return view('livewire.assignment-student');
    }
}
