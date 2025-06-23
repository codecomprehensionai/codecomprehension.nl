<div>
    <x-header />
    <div class="max-w-7xl mx-auto px-6 py-8">
        <x-assignment.title :$assignment />
        @php
            $question = $assignment->questions[$index] ?? null;
        @endphp
        <x-assignment.code :$question />
        <x-assignment.answer-box :$question :$index :assignment="$assignment" wire:previousQuestion="previousQuestion"
            wire:nextQuestion="nextQuestion" />
    </div>
</div>
