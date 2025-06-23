<div>
    <x-header />
    <x-assignment.title :$assignment />
    @foreach ($this->assignment->questions as $question)
        {{-- <x-assignment.question :$question /> --}}
        <x-assignment.code :$question />
        <x-assignment.answer-box :$question />
    @endforeach
</div>

