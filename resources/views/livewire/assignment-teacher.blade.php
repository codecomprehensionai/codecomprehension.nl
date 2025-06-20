<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <h1>Teacher Dashboard</h1>

    {{-- TODO: assignment details --}}
    <p>{{ $this->assignment->title }}</p>

    {{-- TODO: questions block --}}

    {{-- TODO: foreach question --}}
    <livewire:question-teacher :assignment="$this->assignment" />

    {{-- TODO: add question button --}}
</div>
