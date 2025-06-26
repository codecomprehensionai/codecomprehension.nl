<?php

namespace App\Livewire;

use App\Enums\QuestionLanguage;
use Livewire\Component;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\CommonMark\CommonMarkConverter;

class AssignmentStudent extends Component
{
    public Assignment $assignment;
    public int $index = 0;
    public array $answers = [];

    public string $code;
    public string $description;
    public string $question;
    public QuestionLanguage $language;


    public function mount()
    {
        $this->answers = array_fill(0, count($this->assignment->questions), null);
        foreach ($this->assignment->questions as $index => $question) {
            $this->answers[$index] = [
                'lti_id' => $this->assignment->lti_id,
                'question_id' => $question->id,
                'user_id' => Auth::id(),
                'answer' => $question->type->value === 'multiple_choice' ? [] : '',
            ];
        }
        $this->getCurrentQuestion();
    }

    public function render()
    {
        return view('livewire.assignment-student');
    }

    public function getCurrentQuestion()
    {
        $parsed = $this->parseQuestionContent();
        $this->code = $parsed['code'] ?? '';
        $this->description = $parsed['description'] ?? '';
        $this->question = $parsed['question'] ?? '';
        $this->language = $this->assignment->questions[$this->index]->language;

        return $this->assignment->questions[$this->index] ?? null;
    }

    private function parseQuestionContent()
    {
        $content = "# List Comprehension with Filtering and Transformation\n\n## Description\nAnalyze the following Python code snippet, which uses a list comprehension to process a list of words. The list comprehension both filters and transforms elements from the original list.\n\n## Code Example\n```python\nwords = ['apple', 'banana', 'pear', 'plum', 'cherry', 'avocado', 'kiwi', 'apricot']\n\nresult = [w.upper() for w in words if w.startswith('a') and len(w) > 5]\nprint(result)\n```\n\n## Question\nExplain thoroughly what the list comprehension is doing. In your explanation, answer:\n\n1. **Filtering logic:** What is the filtering condition applied to each word? Which specific words from the `words` list satisfy this condition?\n2. **Transformation:** What transformation is performed on the filtered words?\n3. **Final Output:** What will be the exact contents of the `result` list after this code is run? Justify each value.\n\nBe detailed in your reasoning for each part.";
        $lines = explode("\n", $content);
        $parsed = [
            'title' => '',
            'description' => '',
            'code' => '',
            'question' => ''
        ];

        $currentSection = '';
        $codeBlockOpen = false;

        foreach ($lines as $line) {
            if (preg_match('/^# (.+)$/', $line, $matches)) {
                $parsed['title'] = '# ' . $matches[1];
                continue;
            }

            if (preg_match('/^## Description$/', $line)) {
                $currentSection = 'description';
                continue;
            }

            if (preg_match('/^## Code Example$/', $line)) {
                $currentSection = 'code';
                continue;
            }

            if (preg_match('/^## Question$/', $line)) {
                $currentSection = 'question';
                continue;
            }

            if ($line === '```python') {
                $codeBlockOpen = true;
                $parsed['code'] .= $line . "\n";
                continue;
            }

            if ($line === '```' && $codeBlockOpen) {
                $codeBlockOpen = false;
                $parsed['code'] .= $line;
                continue;
            }

            if ($currentSection && !empty(trim($line)) || $codeBlockOpen) {
                $parsed[$currentSection] .= $line . "\n";
            }
        }

        foreach ($parsed as $key => $value) {
            $parsed[$key] = rtrim($value, "\n");
        }

        return $parsed;
    }

    public function nextQuestion()
    {
        if ($this->index < count($this->assignment->questions) - 1) {
            ++$this->index;
        }
    }

    public function previousQuestion()
    {
        if ($this->index > 0) {
            --$this->index;
        }
    }

    public function submitAnswer()
    {
        DB::transaction(function () {
            foreach ($this->answers as $submission) {
                Submission::create($submission);
            }
        });

        return redirect()->route('assignment.results', ['assignment' => $this->assignment->id])
            ->with('success', 'Your answers have been submitted successfully.');
    }
}
