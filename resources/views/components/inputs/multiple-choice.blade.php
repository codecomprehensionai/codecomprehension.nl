<div class="space-y-3">
    @foreach ($question->options as $optionIndex => $option)
        <div class="flex items-start space-x-3">
            <x-filament::input.checkbox
                wire:model="answers.{{ $index }}.answer.{{ $optionIndex }}"
                :id="'option_' . $question->id . '_' . $optionIndex"
                name="question_{{ $question->id }}"
                value="{{ $optionIndex }}"
                class="mt-1"
            />
            <label
                :for="'option_' . $question->id . '_' . $optionIndex"
                class="flex-1 cursor-pointer text-sm leading-6"
            >
                <span class="font-mono font-medium mr-2">{{ chr(65 + $optionIndex) }})</span>
                <span>{{ $option }}</span>
            </label>
        </div>
    @endforeach
</div>
