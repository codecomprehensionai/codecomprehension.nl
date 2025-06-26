<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <x-assignment-result.title :assignment="$assignment" />
        @if ($submissions->isNotEmpty())
            <!-- Main Results Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <!-- Score Display -->
                <x-assignment-result.score :score="$score" />

                <!-- Stats Grid -->
                <x-assignment-result.stats :correct-answers="$correctAnswers" :total-questions="$totalQuestions" :time-spent="$timeSpent" :class-rank="$classRank" />

                <!-- Feedback -->
                @if ($feedback)
                    <x-assignment-result.feedback :feedback="$feedback" />
                @endif
            </div>

            <!-- Detailed Feedback Section -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Detailed Feedback</h3>
                <p class="text-gray-600 mb-6">AI-generated insights on your performance</p>

                @foreach ($assignment->questions as $question)
                    @php
                        $userSubmission = $submissions->firstWhere('question_id', $question->id);
                        $isCorrect = $userSubmission ? $userSubmission->is_correct : false;
                    @endphp

                    <div class="border border-gray-200 rounded-lg p-6 mb-4">
                        <div class="flex items-start justify-between mb-4">
                            <h4 class="text-lg font-medium text-gray-900">
                                Question {{ $question->type }}: {{ $question->question }}
                            </h4>
                            @if ($userSubmission)
                                @if ($isCorrect)
                                    <x-assignment-result.correct :question="$question" />
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Incorrect
                                    </span>
                                @endif
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Not Answered
                                </span>
                            @endif
                        </div>
                        @if ($question->code)
                        <x-assignment-result.code :question="$question" />
                        @endif
                        @if ($question->type->value === 'multiple_choice')
                            @php
                                $options = is_string($question->options)
                                    ? json_decode($question->options, true)
                                    : $question->options;
                                $userAnswer = $userSubmission ? array_keys($userSubmission->answer) : null;
                                $correctAnswer = $question->answer ?? [1];
                            @endphp
                            @if (is_array($options))
                            <x-assignment-result.multiple-choice :options="$options" :correctAnswer="$correctAnswer" :userAnswer="$userAnswer" />
                            @endif
                        @else
                            <div
                                class="@if ($isCorrect) bg-green-50 border border-green-200 @else bg-red-50 border border-red-200 @endif rounded-lg p-4 mb-4">
                                <p class="@if ($isCorrect) text-green-800 @else text-red-800 @endif">
                                    {{ $userSubmission ? $userSubmission->answer : 'No answer provided' }}
                                </p>
                                @if ($isCorrect)
                                    <span class="text-green-600 text-sm font-medium mt-2 block">✓ Correct</span>
                                @else
                                    <span class="text-red-600 text-sm font-medium mt-2 block">✗ Incorrect</span>
                                @endif
                            </div>
                        @endif

                        @if ($userSubmission && $userSubmission->feedback)
                            <x-assignment-result.ai-feedback :userSubmission="$userSubmission" />
                        @endif

                        @if (!$isCorrect && $question->explanation)
                            <x-assignment-result.feedback-explanation :question="$question" />
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <!-- No Submission Found -->
            <x-assignment-result.no-submission :assignment="$assignment" />
        @endif

        <!-- Back Button -->
        <x-assignment-result.back-button :assignment="$assignment" />
    </div>
</div>
