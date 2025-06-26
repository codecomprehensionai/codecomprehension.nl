<div>
    <x-header />
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <x-assignment-result.title :assignment="$assignment" />

            @if($submissions->isNotEmpty())
                <!-- Main Results Card -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                    <!-- Score Display -->
                    <x-assignment-result.score :score="$score" />

                    <!-- Stats Grid -->
                    <x-assignment-result.stats :correct-answers="$correctAnswers" :total-questions="$totalQuestions" :time-spent="$timeSpent" :class-rank="$classRank" />

                    <!-- Feedback -->
                    @if($feedback)
                        <x-assignment-result.feedback :feedback="$feedback" />
                    @endif
                </div>

                <!-- Detailed Feedback Section -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">Detailed Feedback</h3>
                    <p class="text-gray-600 mb-6">AI-generated insights on your performance</p>

                    @foreach($assignment->questions as $question)
                        @php
                            $userSubmission = $submissions->firstWhere('question_id', $question->id);
                            $isCorrect = $userSubmission ? $userSubmission->is_correct : false;
                        @endphp

                        <div class="border border-gray-200 rounded-lg p-6 mb-4">
                            <div class="flex items-start justify-between mb-4">
                                <h4 class="text-lg font-medium text-gray-900">
                                    Question {{ $loop->iteration }}: {{ $question->question }}
                                </h4>
                                @if($userSubmission)
                                    @if($isCorrect)
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
                                                        <div class="mt-2 p-2 bg-yellow-100 border border-yellow-300 rounded text-xs">
                                <strong>Debug:</strong><br>
                                User Submission: {{ $userSubmission ? 'EXISTS' : 'NULL' }}<br>
                                @if($userSubmission)
                                    Answer type: {{ gettype($userSubmission->answer) }}<br>
                                    Answer: {{ is_array($userSubmission->answer) ? json_encode($userSubmission->answer) : $userSubmission->answer }}<br>
                                    Answer is empty: {{ empty($userSubmission->answer) ? 'YES' : 'NO' }}
                                @endif
                            </div>

                            @if($userSubmission && !empty($userSubmission->answer))
                                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-sm font-medium text-blue-800 mb-1">Your Answer:</p>
                                    <div class="text-blue-900">
                                        @if(is_array($userSubmission->answer))
                                            @foreach($userSubmission->answer as $key => $value)
                                                <p><strong>{{ $key }}:</strong> {{ $value }}</p>
                                            @endforeach
                                        @else
                                            {{ $userSubmission->answer }}
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($question->answer)
                                <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <p class="text-sm font-medium text-green-800 mb-1">Correct Answer:</p>
                                    <p class="text-green-900">{{ $question->answer }}</p>
                                </div>
                            @endif

                            @if($userSubmission && $userSubmission->feedback)
                                <x-assignment-result.ai-feedback :userSubmission="$userSubmission" />
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
