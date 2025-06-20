<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <h1>Teacher Dashboard</h1>

    {{-- TODO: assignment details --}}
    <p>{{ $this->assignment->title }}</p>

    <x-card class="py-13">
        <div class="flex items-center justify-between">
            <b class="text-2xl">{{ $this->assignment->title }}</b>
            <div class="text-right">
                <p>Due Date</p>
                <p>{{ $this->assignment->deadline_at }}</p>
            </div>
        </div>
    </x-card>
    <div class="flex justify-center my-4">
        <button
            class="bg-white hover:bg-gray-50 text-gray-800 font-semibold py-2 px-4 border border-gray-200 rounded shadow">
            + Add question
        </button>
    </div>
    {{-- TODO: questions block --}}

    {{-- TODO: foreach question --}}
    <livewire:question-teacher :assignment="$this->assignment" />

    {{-- TODO: add question button --}}
</div>