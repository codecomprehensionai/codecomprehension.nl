<x-card>
    <div class="text-muted-foreground text-sm">
        Provide your answer to the question below
    </div>

    <div class="flex flex-col gap-4">
        @if (isset($question->question) && $question->question)
            <div class="font-medium">{{ $question->question }}</div>
        @elseif (isset($question->text) && $question->text)
            <div class="font-medium">{{ $question->text }}</div>
        @elseif (isset($question->title) && $question->title)
            <div class="font-medium">{{ $question->title }}</div>
        @elseif (isset($question->content) && $question->content)
            <div class="font-medium">{{ $question->content }}</div>
        @endif

        <x-question-answer-box :question="$question" />
        <button wire:click="nextQuestion" class="mt-4 btn btn-primary">
            Next
        </button>
        <button wire:click="previousQuestion" class="mt-4 btn btn-secondary" @if ($index === 0) disabled @endif>
            Previous
        </button>
    </div>
</x-card>

