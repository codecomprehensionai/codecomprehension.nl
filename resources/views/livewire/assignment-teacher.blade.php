<form wire:submit="create">
    {{ $this->form }}

    <x-filament::button type="submit">
        {{ __('Submit') }}
    </x-filament::button>

    <x-filament-actions::modals />
</form>
