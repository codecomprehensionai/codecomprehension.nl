@if(isset($question->type) && $question->type->value === 'multiple_choice' && isset($question->options) && $question->options)
<div class="space-y-2">
    @foreach($question->options as $index => $option)
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
@else
<textarea
    name="question_{{ $question->id }}"
    placeholder="Type your answer here..."
    rows="4"
    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary resize-vertical"
></textarea>
@endif
