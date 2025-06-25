@php
    $currentAnswer = isset($answers) && isset($index) && isset($answers[$index]) ? $answers[$index] : null;
@endphp

<x-card>
    <div class="text-muted-foreground text-sm">
        Provide your answer to the question below
    </div>

    <div class="flex flex-col gap-4">
        @if (isset($question->question) && $question->question)
            <div class="font-medium">{{ $question->question }}</div>
        @elseif(isset($question->text) && $question->text)
            <div class="font-medium">{{ $question->text }}</div>
        @elseif(isset($question->title) && $question->title)
            <div class="font-medium">{{ $question->title }}</div>
        @elseif(isset($question->content) && $question->content)
            <div class="font-medium">{{ $question->content }}</div>
        @endif

        <x-question-answer-box :question="$question" :index="$index"/>

        <div class="flex justify-between items-center mt-4">
            <x-button wire:click="previousQuestion" :disabled="$index === 0">
                <x-shapes.arrow_left />
            </x-button>

            @if ($index === count($assignment->questions) - 1)
                <x-button wire:click="submitAnswer" class="bg-green hover:bg-primary-dark">
                    Submit Answers
                </x-button>
            @else
            <x-button wire:click="nextQuestion">
                <x-shapes.arrow_right />
            </x-button>
            @endif

        </div>
    </div>
</x-card>
