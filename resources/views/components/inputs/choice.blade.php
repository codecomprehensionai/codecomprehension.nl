<div class="space-y-2">
    @foreach($question->options as $index => $option)
        <label class="w-full flex items-center p-3 rounded-lg border transition-colors cursor-pointer
            {{ isset($answer) && $answer === $index
                ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-200'
                : 'bg-white border-gray-200 hover:bg-gray-50'
            }}">
            <input
                type="radio"
                wire:model="answers.{{ $index }}"
                name="question_{{ $question->id }}"
                value="{{ $index }}"
                class="mr-3"
            >
            <span class="font-mono text-sm mr-3">{{ chr(65 + $index) }})</span>
            {{ $option }}
        </label>
    @endforeach
</div>
