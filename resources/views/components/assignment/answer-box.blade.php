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

        <x-question-answer-box :question="$question" />

        {{-- Save button --}}
        <div class="flex justify-between items-center mt-4">
            <button
                type="button"
                class="bg-primary text-primary-foreground hover:bg-primary/90 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            >
                Save Answer
            </button>
        </div>
    </div>
</x-card>

