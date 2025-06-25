@if (isset($question->type) && $question->type->value === 'multiple_choice')
    <x-inputs.multiple-choice :question="$question" />
@else
    <textarea wire:model="textAnswer" name="question_{{ $question->id }}" placeholder="Type your answer here..." rows="4"
        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary resize-vertical"></textarea>
@endif
