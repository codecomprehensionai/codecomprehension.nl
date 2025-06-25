<div class="space-y-3">
    @foreach($question->options as $index => $option)
        <div class="flex items-start space-x-3">
            <x-filament::input.checkbox
                wire:model.live="multipleChoiceAnswers.{{ $index }}"
                :id="'option_' . $question->id . '_' . $index"
                name="question_{{ $question->id }}"
                value="{{ $index }}"
                class="mt-1"
            />
            <label
                :for="'option_' . $question->id . '_' . $index"
                class="flex-1 cursor-pointer text-sm leading-6"
            >
                <span class="font-mono font-medium mr-2">{{ chr(65 + $index) }})</span>
                <span>{{ $option }}</span>
            </label>
        </div>
    @endforeach
</div>
