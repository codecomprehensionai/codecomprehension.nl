{{-- @php
    // $question = $assignment->questions[$index] ?? null;
    $question->question =  "# List Comprehension with Filtering and Transformation\n\n## Description\nAnalyze the following Python code snippet, which uses a list comprehension to process a list of words. The list comprehension both filters and transforms elements from the original list.\n\n## Code Example\n```python\nwords = ['apple', 'banana', 'pear', 'plum', 'cherry', 'avocado', 'kiwi', 'apricot']\n\nresult = [w.upper() for w in words if w.startswith('a') and len(w) > 5]\nprint(result)\n```\n\n## Question\nExplain thoroughly what the list comprehension is doing. In your explanation, answer:\n\n1. **Filtering logic:** What is the filtering condition applied to each word? Which specific words from the `words` list satisfy this condition?\n2. **Transformation:** What transformation is performed on the filtered words?\n3. **Final Output:** What will be the exact contents of the `result` list after this code is run? Justify each value.\n\nBe detailed in your reasoning for each part.";

@endphp --}}
<div>
    <x-header />
    <div class="max-w-7xl mx-auto px-6 py-8">
        <x-assignment.title :$assignment />
        <x-assignment.progress :$assignment :$index />
        <x-assignment.code :$code :$question :$language />

        <!-- Answer Box -->
        @if ($this->getCurrentQuestion())
            <x-assignment.answer-box :assignment="$assignment" :code="$code" :question="$this->getCurrentQuestion()" :index="$index" :answers="$answers" />
        @endif

        <x-card>
            <div class="flex justify-between items-center">
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
        </x-card>
    </div>
</div>
