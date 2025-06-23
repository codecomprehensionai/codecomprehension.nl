<div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl border p-6 shadow-sm">
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
</div>
