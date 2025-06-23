<div>
    @foreach ($this->assignment->questions as $question)
        {{-- <x-assignment.question :$question /> --}}
        <x-assignment.code :$question />
    @endforeach
    <x-assignment.title :$assignment />
</div>

