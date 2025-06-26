<div>
    <x-header />
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
                                    Question {{ $loop->iteration }}: {{ $question->question }}
                                </h4>
                                @if ($userSubmission)
                                    @if ($isCorrect)
                                        <x-assignment-result.correct :question="$question" />
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            Incorrect
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Not Answered
                                    </span>
                                @endif
                            </div>

                            @if ($question->code)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                                    <pre class="text-sm text-gray-800 whitespace-pre-wrap"><code>{{ $question->code }}</code></pre>
                                </div>
                            @endif

                            @if ($question->options)
                                @php
                                    $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                    $userAnswer = $userSubmission ? $userSubmission->answer : null;
                                    $correctAnswer = $question->correct_answer;
                                @endphp

                                @if (is_array($options))
                                    <div class="space-y-2">
                                        @foreach ($options as $optionKey => $option)
                                            <div class="flex items-center space-x-3 p-3 rounded-lg
                                                @if ($optionKey === $correctAnswer) bg-green-50 border border-green-200
                                                @elseif ($optionKey === $userAnswer && !$isCorrect) bg-red-50 border border-red-200
                                                @else bg-gray-50 border border-gray-200 @endif">

                                                <span class="font-medium text-gray-700">{{ strtoupper($optionKey) }})</span>
                                                <span class="text-gray-900">{{ $option }}</span>

                                                @if ($optionKey === $correctAnswer)
                                                    <span class="ml-auto text-green-600 text-sm font-medium">Correct</span>
                                                @elseif ($optionKey === $userAnswer && !$isCorrect)
                                                    <span class="ml-auto text-red-600 text-sm font-medium">Your Answer</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
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
</div>