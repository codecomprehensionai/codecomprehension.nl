{{-- Simple textarea input since options are removed --}}
<textarea 
    wire:model="answers.{{ $index }}.answer" 
    name="question_{{ $question->id }}" 
    placeholder="Type your answer here... (Question {{ $index + 1 }})" 
    rows="4"
    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary resize-vertical"
    wire:key="answer-{{ $index }}"
></textarea>
{{-- Debug info --}}
<div class="text-xs text-gray-500 mt-1">
    Bound to: answers.{{ $index }}.answer | Current value: {{ $answers[$index]['answer'] ?? 'empty' }}
</div>

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
