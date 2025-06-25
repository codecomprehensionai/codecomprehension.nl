<div>
    <x-header />
    <div class="max-w-7xl mx-auto px-6 py-8">
        <x-assignment.title :$assignment />
        <x-assignment.progress :$assignment :$index />
        @php
            $question = $assignment->questions[$index] ?? null;
        @endphp
        <x-assignment.code :$question />
        <x-card>
            <form wire:submit.prevent="submitAnswer" class="space-y-6">
                <div class="custom-form-wrapper bg-gray-50 p-6 rounded-lg border-2 border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Answer</h3>
                    <div class="answer-field-container">
                        {{ $this->form }}
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</div>
