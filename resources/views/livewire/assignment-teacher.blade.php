<div class="max-w-5xl mx-auto py-10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Create New Assignment</h1>
            <p class="text-gray-500">AI-powered code comprehension exercise generator</p>
        </div>
        <div>
            <a href="{{ route('assignment.teacher', ['assignment' => $assignment->id, 'view' => 'teacher']) }}"
               class="mr-4 {{ $view === 'teacher' ? 'text-blue-600 font-bold' : 'text-gray-600' }}">
                Questions
            </a>
            <a href="{{ route('assignment.teacher', ['assignment' => $assignment->id, 'view' => 'overview']) }}"
               class="{{ $view === 'overview' ? 'text-blue-600 font-bold' : 'text-gray-600' }}">
                Overview
            </a>
        </div>
    </div>

    @if ($view === 'teacher')
        {{-- teacher/QUESTION PAGE --}}
        <x-card class="py-13">
            <div class="flex items-center justify-between">
                <b class="text-2xl">{{ $assignment->title ?: 'Untitled Assignment' }}</b>
                <div class="text-right">
                    <p>Due Date:</p>
                    <p>{{ $assignment->deadline_at?->format('d-m-Y, H:i:s') ?? 'No due date' }}</p>
                </div>
            </div>
        </x-card>

        {{-- Add Question Button --}}
        <div class="flex justify-center my-4">
            <button wire:click="toggleAddForm"
                class="bg-white hover:bg-gray-50 text-gray-800 font-semibold py-2 px-4 border border-gray-200 rounded shadow">
                {{ $showAddForm ? 'Cancel' : '+ Add question' }}
            </button>
        </div>

        @if ($showAddForm)
        <div class="bg-white border border-gray-200 rounded p-6 mb-6 shadow">
            <h2 class="text-lg font-semibold mb-4">Add New Question</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- LEFT SIDE: Question Configuration --}}
                <div class="space-y-4">
                    {{-- Language --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Language</label>
                        <select wire:model="newQuestion.language" class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="">Select Language</option>
                            <option value="python">Python</option>
                            <option value="javascript">JavaScript</option>
                            <option value="php">PHP</option>
                            <option value="java">Java</option>
                        </select>
                    </div>

                    {{-- Difficulty --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Difficulty</label>
                        <select wire:model="newQuestion.level" class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="">Select Difficulty</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>
                    </div>

                    {{-- Question Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Question Type</label>
                        <div class="flex space-x-4 mt-2">
                            <label class="flex items-center space-x-1">
                                <input wire:model="newQuestion.type" type="radio" name="question_type" value="single" onchange="renderAnswers()" />
                                <span>Single-choice</span>
                            </label>
                            <label class="flex items-center space-x-1">
                                <input wire:model="newQuestion.type" type="radio" name="question_type" value="multiple" onchange="renderAnswers()" />
                                <span>Multiple-choice</span>
                            </label>
                            <label class="flex items-center space-x-1">
                                <input wire:model="newQuestion.type" type="radio" name="question_type" value="open" onchange="renderAnswers()" />
                                <span>Open</span>
                            </label>
                        </div>
                    </div>

                    {{-- Estimated Duration --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estimated Duration (minutes)</label>
                        <input wire:model="newQuestion.estimated_answer_duration" type="number" class="w-full border border-gray-300 rounded px-3 py-2" min="1" placeholder="e.g. 5">
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Question</label>
                        <textarea wire:model="newQuestion.question" class="w-full border border-gray-300 rounded px-3 py-2" rows="3"
                            placeholder="Enter your question here..."></textarea>
                    </div>

                    {{-- Code Snippet Placeholder --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Code Snippet</label>
                        <textarea wire:model="newQuestion.code" class="w-full border border-gray-300 rounded px-3 py-2 font-mono bg-gray-50" rows="4"
                            placeholder="// Enter code snippet here..."></textarea>
                    </div>

                    {{-- Answers --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mt-4">Answer Options</label>
                        <div id="answer-options" class="space-y-2 mt-2">
                            {{-- This will be dynamically updated --}}
                        </div>
                    </div>
                </div>

                {{-- RIGHT SIDE: AI Assistant --}}
                <div class="flex flex-col h-full">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-lg font-semibold text-gray-800">Question assistant</h3>
                    </div>

                    {{-- AI Response Area --}}
                    <div class="flex-1 border border-gray-200 rounded bg-gray-50 p-4 overflow-y-auto h-64 mb-4">
                        <p class="text-sm text-gray-600">Chat with our AI to refine the test contest</p>
                    </div>

                    {{-- AI Input --}}
                    <div class="flex items-center space-x-2">
                        <input type="text" class="flex-1 border border-gray-300 rounded px-3 py-2" placeholder="Type your message...">
                        <button class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
                            Send
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-right">
                <button wire:click="saveQuestion" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">
                    Add Question
                </button>
            </div>
        </div>
        @endif

        <livewire:question-teacher :assignment="$assignment" />

        <div class="flex justify-end mt-6">
            <a href="{{ route('assignment.teacher', ['assignment' => $assignment->id, 'view' => 'overview']) }}"
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow">
                Next: Overview →
            </a>
        </div>

    @elseif ($view === 'overview')
        {{-- OVERVIEW PAGE --}}
        @include('components.assignment.overview', ['assignment' => $assignment])

        <div class="flex justify-between mt-6">
            <a href="{{ route('assignment.teacher', ['assignment' => $assignment->id, 'view' => 'teacher']) }}"
               class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded shadow">
                ← Back
            </a>

            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                ✅ Save Assignment
            </button>
        </div>
    @endif
</div>


{{-- JavaScript --}}
<script>
    function renderAnswers() {
        const container = document.getElementById('answer-options');
        const selectedType = document.querySelector('input[name="question_type"]:checked').value;
        container.innerHTML = '';

        if (selectedType === 'open') {
            const input = document.createElement('input');
            input.type = 'text';
            input.placeholder = 'Model answer here';
            input.className = 'w-full border border-gray-300 rounded px-3 py-2';
            container.appendChild(input);
        } else {
            for (let i = 0; i < 4; i++) {
                const wrapper = document.createElement('div');
                wrapper.className = 'flex items-center space-x-2';

                const input = document.createElement('input');
                input.type = selectedType === 'single' ? 'radio' : 'checkbox';
                input.name = selectedType === 'single' ? 'correct_option' : 'correct_option[]';
                input.className = 'text-blue-500';

                const text = document.createElement('input');
                text.type = 'text';
                text.placeholder = 'Answer option ' + (i + 1);
                text.className = 'flex-1 border border-gray-300 rounded px-3 py-2';

                wrapper.appendChild(input);
                wrapper.appendChild(text);
                container.appendChild(wrapper);
            }
        }
    }

    // Initial render
    document.addEventListener('DOMContentLoaded', renderAnswers);
</script>