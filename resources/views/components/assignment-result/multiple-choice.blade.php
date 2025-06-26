@php
    if (gettype($correctAnswer) !== 'array') {
        $correctAnswer = is_string($correctAnswer) ? json_decode($correctAnswer, true) : $correctAnswer;
    }
@endphp
<div class="space-y-2">
    @foreach ($options as $optionKey => $option)
        <div class="flex items-center space-x-3 p-3 rounded-lg

            @if (in_array($optionKey, $correctAnswer, true)) bg-green-50 border-2 border-green-300 bg-green-200
            @elseif(!in_array($optionKey, $userAnswer, true) && in_array($optionKey, $correctAnswer, true))
                bg-gray-50 border border-red-300
            @elseif(!in_array($optionKey, $userAnswer, true) && !in_array($optionKey, $correctAnswer, true))
                bg-red-50 border-2 border-red-300
            @else
                bg-red-300 border border-red-300 @endif">

            <span class="font-medium text-gray-700">{{ strtoupper($optionKey) }})</span>
            <span
                class="text-gray-900
                    @if (in_array($optionKey, $correctAnswer, true)) text-green-800
                    @elseif(in_array($optionKey, $userAnswer, true) && !in_array($optionKey, $correctAnswer, true))
                        text-red-800 @endif">{{ $option }}</span>
            @if (in_array($optionKey, $userAnswer, true) && !in_array($optionKey, $correctAnswer, true))
                <span class="ml-auto text-red-600 text-sm font-medium">✗ Your Answer</span>
            @elseif (in_array($optionKey, $correctAnswer, true) ||
                    (!in_array($optionKey, $userAnswer, true) && !in_array($optionKey, $correctAnswer, true)))
                <span class="ml-auto text-green-600 text-sm font-medium">✓ Correct</span>
            @endif
        </div>
    @endforeach
</div>
