@php
    $currentAnswer = (isset($answers) && isset($index) && isset($answers[$index])) ? $answers[$index] : null;
@endphp

<x-card>
    <div class="text-muted-foreground text-sm">
        Provide your answer to the question below
    </div>

    <div class="flex flex-col gap-4">
        @if(isset($question->question) && $question->question)
            <div class="font-medium">{{ $question->question }}</div>
        @elseif(isset($question->text) && $question->text)
            <div class="font-medium">{{ $question->text }}</div>
        @elseif(isset($question->title) && $question->title)
            <div class="font-medium">{{ $question->title }}</div>
        @elseif(isset($question->content) && $question->content)
            <div class="font-medium">{{ $question->content }}</div>
        @endif

        <x-question-answer-box :question="$question"  />
            @if($index === count($assignment->questions) - 1)
                <button
                    wire:click="submitAssignment"
                    class="mt-4 btn btn-secondary hover:bg-green-400 hover:border-black-400 hover:shadow-sm"
                >
                    Submit
                </button>
            @else
                <button
                    wire:click="nextQuestion"
                    class="mt-4 btn btn-secondary hover:bg-blue-50 hover:border-gray-400 hover:shadow-sm"
                >
                    Next
                </button>
            @endif
        <button wire:click="previousQuestion" class="mt-4 btn btn-secondary hover:bg-blue-50 hover:border-gray-400 hover:shadow-sm" @disabled($index === 0)>
            Previous
        </button>
    </div>
</x-card>

