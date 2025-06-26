@if (isset($question->type) && $question->type->value === 'multiple_choice')
    <x-inputs.multiple-choice :question="$question" :index="$index" :options="$options" />
@else
    <textarea wire:model="answers.{{ $index }}.answer" name="question_{{ $question->id }}" placeholder="Type your answer here..." rows="4"
        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary resize-vertical"></textarea>
@endif

{{-- <x-card class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-6">
        <form wire:submit.prevent="submitAnswer" class="space-y-6">
            <div class="filament-form-container">
                {{ $this->form }}
            </div>
        </form>
    </div>
</x-card>

 --}}
