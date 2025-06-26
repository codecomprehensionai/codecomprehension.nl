<?php

namespace App\Livewire;

use App\Enums\QuestionLanguage;
use Livewire\Component;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\CommonMark\CommonMarkConverter;
use Illuminate\Support\Str;


class AssignmentStudent extends Component
{
    public Assignment $assignment;
    public int $index = 0;
    public array $answers = [];

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
        $converter = new CommonMarkConverter();
        $html = $converter->convert("# List Comprehension with Filtering and Transformation\n\n## Description\nAnalyze the following Python code snippet, which uses a list comprehension to process a list of words. The list comprehension both filters and transforms elements from the original list.\n\n## Code Example\n```python\nwords = ['apple', 'banana', 'pear', 'plum', 'cherry', 'avocado', 'kiwi', 'apricot']\n\nresult = [w.upper() for w in words if w.startswith('a') and len(w) > 5]\nprint(result)\n```\n\n## Question\nExplain thoroughly what the list comprehension is doing. In your explanation, answer:\n\n1. **Filtering logic:** What is the filtering condition applied to each word? Which specific words from the `words` list satisfy this condition?\n2. **Transformation:** What transformation is performed on the filtered words?\n3. **Final Output:** What will be the exact contents of the `result` list after this code is run? Justify each value.\n\nBe detailed in your reasoning for each part.");

        // $html = $converter->convert($this->assignment->questions[$this->index]->question);

        return view('livewire.assignment-student', ['html' => $html]);
    }

    public function getCurrentQuestion()
    {

        return $this->assignment->questions[$this->index] ?? null;
    }

    // private function parseQuestionContent()
    // {
    //     $content = "# List Comprehension with Filtering and Transformation\n\n## Description\nAnalyze the following Python code snippet, which uses a list comprehension to process a list of words. The list comprehension both filters and transforms elements from the original list.\n\n## Code Example\n```python\nwords = ['apple', 'banana', 'pear', 'plum', 'cherry', 'avocado', 'kiwi', 'apricot']\n\nresult = [w.upper() for w in words if w.startswith('a') and len(w) > 5]\nprint(result)\n```\n\n## Question\nExplain thoroughly what the list comprehension is doing. In your explanation, answer:\n\n1. **Filtering logic:** What is the filtering condition applied to each word? Which specific words from the `words` list satisfy this condition?\n2. **Transformation:** What transformation is performed on the filtered words?\n3. **Final Output:** What will be the exact contents of the `result` list after this code is run? Justify each value.\n\nBe detailed in your reasoning for each part.\n\n## Options\nA) The result will be ['APPLE', 'AVOCADO', 'APRICOT']\nB) The result will be ['AVOCADO', 'APRICOT']\nC) The result will be ['apple', 'avocado', 'apricot']\nD) The result will be an empty list";

    //     $lines = explode("\n", $content);
    //     $parsed = [
    //         'title' => '',
    //         'description' => '',
    //         'code' => '',
    //         'question' => '',
    //         'options' => []
    //     ];

    //     $currentSection = '';
    //     $codeBlockOpen = false;

    //     foreach ($lines as $line) {
    //         if (preg_match('/^# (.+)$/', $line, $matches)) {
    //             $parsed['title'] = '# ' . $matches[1];
    //             continue;
    //         }

    //         if (preg_match('/^## Description$/', $line)) {
    //             $currentSection = 'description';
    //             continue;
    //         }

    //         if (preg_match('/^## Code Example$/', $line)) {
    //             $currentSection = 'code';
    //             continue;
    //         }

    //         if (preg_match('/^## Question$/', $line)) {
    //             $currentSection = 'question';
    //             continue;
    //         }

    //         if (preg_match('/^## Options$/', $line)) {
    //             $currentSection = 'options';
    //             continue;
    //         }

    //         // Handle multiple choice options (A), B), C), etc.) - REMOVED dd() here
    //         if ($currentSection === 'options' && preg_match('/^([A-Z])\)\s*(.+)$/', $line, $matches)) {
    //             $parsed['options'][] = [
    //                 'key' => $matches[1],
    //                 'value' => trim($matches[2])
    //             ];
    //             continue;
    //         }

    //         if ($line === '```python' || preg_match('/^```\w*$/', $line)) {
    //             if (!$codeBlockOpen) {
    //                 $codeBlockOpen = true;
    //                 if ($currentSection === 'code') {
    //                     $parsed['code'] .= $line . "\n";
    //                 }
    //             }
    //             continue;
    //         }

    //         if ($line === '```' && $codeBlockOpen) {
    //             $codeBlockOpen = false;
    //             if ($currentSection === 'code') {
    //                 $parsed['code'] .= $line;
    //             }
    //             continue;
    //         }

    //         if ($currentSection && (!empty(trim($line)) || $codeBlockOpen)) {
    //             if ($currentSection !== 'options') {
    //                 $parsed[$currentSection] .= $line . "\n";
    //             }
    //         }
    //     }

    //     foreach ($parsed as $key => $value) {
    //         if ($key !== 'options') {
    //             $parsed[$key] = rtrim($value, "\n");
    //         }
    //     }

    //     return $parsed;
    // }

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
