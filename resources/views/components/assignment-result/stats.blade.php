<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
    <!-- Correct Answers -->
    <div class="text-center">
        <div class="text-3xl font-bold text-gray-900 mb-2">{{ $correctAnswers }}/{{ $totalQuestions }}</div>
        <p class="text-gray-600">Correct Answers</p>
    </div>

    <!-- Time Spent -->
    <div class="text-center">
        <div class="text-3xl font-bold text-gray-900 mb-2">{{ $timeSpent }} minutes</div>
        <p class="text-gray-600">Time Spent</p>
    </div>

    <!-- Class Rank -->
    <div class="text-center">
        <div class="text-3xl font-bold text-gray-900 mb-2">#{{ $classRank }}</div>
        <p class="text-gray-600">Class Rank</p>
    </div>
</div>