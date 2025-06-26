{{-- Multiple choice result component - now just shows text comparison since options are removed --}}
<div class="space-y-4">
    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-sm font-medium text-blue-800 mb-2">Your Answer:</p>
        <div class="text-blue-900">
            {{ $userAnswer ?? 'No answer provided' }}
        </div>
    </div>
    
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
        <p class="text-sm font-medium text-green-800 mb-2">Correct Answer:</p>
        <div class="text-green-900">
            {{ $correctAnswer ?? 'No correct answer available' }}
        </div>
    </div>
</div>
