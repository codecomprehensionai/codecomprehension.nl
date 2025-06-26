{{-- Choice component - now using text area since options are removed --}}
<textarea 
    wire:model="answers.{{ $index }}.answer" 
    name="question_{{ $question->id }}" 
    placeholder="Type your answer here... (Question {{ $index + 1 }})" 
    rows="4"
    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary resize-vertical"
    wire:key="answer-{{ $index }}"
></textarea>
<div class="text-xs text-gray-500 mt-1">
    Bound to: answers.{{ $index }}.answer | Current value: {{ $answers[$index]['answer'] ?? 'empty' }}
</div>
