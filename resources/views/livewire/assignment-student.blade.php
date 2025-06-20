<div>
    @foreach ($this->assignment->questions as $question)
        <x-assignment.question :$question />
    @endforeach
</div>
