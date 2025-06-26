@php
    $currentAnswer = isset($answers) && isset($index) && isset($answers[$index]) ? $answers[$index] : null;
@endphp

<x-card>
    <div class="text-muted-foreground text-sm">
        Provide your answer to the question below
    </div>

    <div class="flex flex-col gap-4">
        <x-question-answer-box :question="$question" :index="$index"/>
    </div>
</x-card>
