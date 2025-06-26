@php
    $question = $assignment->questions[$index] ?? null;
@endphp
<div>
    <x-header />
    <div class="max-w-7xl mx-auto px-6 py-8">
        <x-assignment.title :$assignment />
        <x-assignment.progress :$assignment :$index />
        
    </div>
</div>
