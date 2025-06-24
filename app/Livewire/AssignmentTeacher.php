<?php

namespace App\Livewire;

use App\Models\Assignment;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Section;
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
            ->record($this->assignment)
            ->components([
                Section::make(config('app.name'))
                    ->description($this->assignment->title),

                Repeater::make('questions')
                    ->relationship()
                    ->columns(2)
                    ->minItems(1)
                    ->maxItems(25)
                    ->addActionLabel(__('Add Question'))
                    ->schema([
                        TextInput::make('name')->required(),
                        Select::make('role')
                            ->options([
                                'member'        => 'Member',
                                'administrator' => 'Administrator',
                                'owner'         => 'Owner',
                            ])
                            ->required(),
                    ]),

                // TODO: (generate actions) ->extraItemActions([
                //     Action::make('sendEmail')
                //         ->icon(Heroicon::Envelope)
                //         ->action(function (array $arguments, Repeater $component): void {
                //             $itemData = $component->getItemState($arguments['item']);

                //             Mail::to($itemData['email'])
                //                 ->send(
                //                     // ...
                //                 );
                //         }),
                // ])

                // TODO: (show title) ->itemLabel(fn(array $state): ?string => $state['name'] ?? null),
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
