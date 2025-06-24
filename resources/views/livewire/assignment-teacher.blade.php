<form wire:submit="create">
    {{ $this->form }}

    <x-filament::button type="submit">
        {{ __('Submit') }}
    </x-filament::button>
</form>
