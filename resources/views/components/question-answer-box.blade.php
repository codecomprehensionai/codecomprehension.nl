<<<<<<< main
@if (isset($question->type) && $question->type->value === 'multiple_choice' && isset($question->options) && $question->options)
<div class="space-y-2">
    @foreach ($question->options as $index => $option)
        <label class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
            <input
                type="radio"
                name="question_{{ $question->id }}"
                value="{{ $option }}"
                class="w-4 h-4 text-primary focus:ring-primary"
            >
            <span>{{ $option }}</span>
        </label>
    @endforeach
</div>
=======

@if (isset($question->type) && $question->type->value === 'multiple_choice')
    <x-inputs.multiple-choice :question="$question" :index="$index"/>
>>>>>>> refactor-dashboard
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
