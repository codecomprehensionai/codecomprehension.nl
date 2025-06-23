<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $assignment->title }}</h1>
                    <p class="text-gray-600 mt-1">Assignment Results</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        Teacher
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        Student
                    </span>
                </div>
            </div>
        </div>

        @if($submissions->isNotEmpty())
            <!-- Main Results Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <!-- Score Display -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <h2 class="text-6xl font-bold text-blue-600 mb-2">{{ $score }}%</h2>
                    <p class="text-gray-600 text-lg">Your Score</p>
                </div>

                <!-- Stats Grid -->
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

                <!-- Feedback -->
                @if($feedback)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <p class="text-green-800 text-sm">{{ $feedback }}</p>
                    </div>
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
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Correct
                                    </span>
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

                        @if($question->code)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                                <pre class="text-sm text-gray-800 whitespace-pre-wrap"><code>{{ $question->code }}</code></pre>
                            </div>
                        @endif

                        @if($question->options)
                            @php
                                $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                $userAnswer = $userSubmission ? $userSubmission->answer : null;
                                $correctAnswer = $question->correct_answer;
                            @endphp

                            @if(is_array($options))
                                <div class="space-y-2">
                                    @foreach($options as $optionKey => $option)
                                        <div class="flex items-center space-x-3 p-3 rounded-lg
                                            @if($optionKey === $correctAnswer) bg-green-50 border border-green-200
                                            @elseif($optionKey === $userAnswer && !$isCorrect) bg-red-50 border border-red-200
                                            @else bg-gray-50 border border-gray-200 @endif">

                                            <span class="font-medium text-gray-700">{{ strtoupper($optionKey) }})</span>
                                            <span class="text-gray-900">{{ $option }}</span>

                                            @if($optionKey === $correctAnswer)
                                                <span class="ml-auto text-green-600 text-sm font-medium">Correct</span>
                                            @elseif($optionKey === $userAnswer && !$isCorrect)
                                                <span class="ml-auto text-red-600 text-sm font-medium">Your Answer</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif

                        @if($userSubmission && $userSubmission->feedback)
                            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    <span class="font-medium">Feedback:</span> {{ $userSubmission->feedback }}
                                </p>
                            </div>
                        @endif

                        @if(!$isCorrect && $question->explanation)
                            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    <span class="font-medium">Explanation:</span> {{ $question->explanation }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

        @else
            <!-- No Submission Found -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Submission Found</h3>
                <p class="text-gray-600">You haven't submitted this assignment yet.</p>
                <a href="{{ route('assignment.student', $assignment->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 mt-4">
                    Start Assignment
                </a>
            </div>
        @endif

        <!-- Back Button -->
        <div class="mt-8">
            <button wire:click="$refresh" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 mr-4">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh Results
            </button>
            <a href="{{ route('assignment.student', $assignment->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Assignment
            </a>
        </div>
    </div>
</div>
