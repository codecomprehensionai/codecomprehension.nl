<div>
    <x-header />
    <div class="max-w-7xl mx-auto px-6 py-8">
        <x-assignment.title :$assignment />
        <x-assignment.progress :$assignment />
        @php
            $question = $assignment->questions[$index] ?? null;
        @endphp
            <x-assignment.code :$question />
            <x-assignment.answer-box :$question />
    </div>
</div>

