<div>
    <h1>Teacher Dashboard</h1>

    {{-- TODO: assignment details --}}
    <p>{{ $this->assignment->title }}</p>

    <x-card class="py-13">
        <div class="flex items-center justify-between">
            <b class="text-2xl">{{ $this->assignment->title }}</b>
            {{-- TODO: assignment description --}}
        </div>
    </x-card>

    <form wire:submit="create">
        {{ $this->form }}

        <button type="submit">
            {{ __('Submit') }}
        </button>
    </form>
</div>
