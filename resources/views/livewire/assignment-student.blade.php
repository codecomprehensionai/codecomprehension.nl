<div>
    <x-header />
    <div class="max-w-7xl mx-auto px-6 py-8">
        <x-assignment.title :$assignment />
        @foreach ($this->assignment->questions as $question)
            <x-assignment.code :$question />
            <x-assignment.answer-box :$question />
        @endforeach
    </div>
</div>

