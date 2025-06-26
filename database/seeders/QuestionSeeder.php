<?php

namespace Database\Seeders;

use App\Enums\QuestionLanguage;
use App\Enums\QuestionLevel;
use App\Enums\QuestionType;
use App\Models\Assignment;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample assignments if they don't exist
        $assignment = Assignment::first() ?: Assignment::factory()->create([
            'title'       => 'Python Fundamentals Assessment',
            'description' => 'A comprehensive assessment covering basic Python programming concepts.',
        ]);

        // Multiple Choice Questions
        $this->createMultipleChoiceQuestions($assignment);

        // Code Explanation Questions
        $this->createCodeExplanationQuestions($assignment);

        // Fill in the Blanks Questions
        $this->createFillInTheBlanksQuestions($assignment);
    }

    private function createMultipleChoiceQuestions(Assignment $assignment): void
    {
        $multipleChoiceQuestions = [
            [
                'language'   => QuestionLanguage::Python,
                'type'       => QuestionType::MultipleChoice,
                'level'      => QuestionLevel::Beginner,
                'question'   => 'Which of the following is NOT a valid Python data type?',
                'answer'     => 'char',
                'score_max'  => 1,
            ],
            [
                'language'   => QuestionLanguage::Python,
                'type'       => QuestionType::MultipleChoice,
                'level'      => QuestionLevel::Intermediate,
                'question'   => 'What will be the output of the following code? my_list = [1, 2, 3]; my_list.append([4, 5]); print(len(my_list))',
                'answer'     => '4',
                'score_max'  => 2,
            ],
            [
                'language'   => QuestionLanguage::Python,
                'type'       => QuestionType::MultipleChoice,
                'level'      => QuestionLevel::Advanced,
                'question'   => 'What will be printed by this code? x = 10; def modify_x(): global x; x = 20; modify_x(); print(x)',
                'answer'     => '20',
                'score_max'  => 3,
            ],
        ];

        foreach ($multipleChoiceQuestions as $questionData) {
            Question::create(array_merge($questionData, ['assignment_id' => $assignment->id]));
        }
    }

    private function createCodeExplanationQuestions(Assignment $assignment): void
    {
        $codeExplanationQuestions = [
            [
                'language'   => QuestionLanguage::Python,
                'type'       => QuestionType::CodeExplanation,
                'level'      => QuestionLevel::Beginner,
                'question'   => 'Explain what this code does and what it will output: for i in range(5): print(i * 2)',
                'answer'     => 'This code prints the even numbers from 0 to 8. It iterates through the range 0-4 and multiplies each number by 2, outputting: 0, 2, 4, 6, 8 on separate lines.',
                'score_max'  => 2,
            ],
            [
                'language'   => QuestionLanguage::Python,
                'type'       => QuestionType::CodeExplanation,
                'level'      => QuestionLevel::Intermediate,
                'question'   => 'Explain what this list comprehension does: result = [x**2 for x in [1,2,3,4,5,6,7,8,9,10] if x % 2 == 0]',
                'answer'     => 'This creates a list of squares of even numbers from the original list. It filters for even numbers (x % 2 == 0) and squares them (x**2). The output will be [4, 16, 36, 64, 100].',
                'score_max'  => 3,
            ],
            [
                'language'   => QuestionLanguage::Python,
                'type'       => QuestionType::CodeExplanation,
                'level'      => QuestionLevel::Advanced,
                'question'   => 'Explain how this decorator works: def timer(func): def wrapper(*args, **kwargs): start = time.time(); result = func(*args, **kwargs); end = time.time(); print(f"{func.__name__} took {end - start:.2f} seconds"); return result; return wrapper',
                'answer'     => 'This is a timing decorator that measures function execution time. When a decorated function is called, the wrapper function starts a timer, executes the original function, stops the timer, prints the execution time, and returns the result.',
                'score_max'  => 4,
            ],
        ];

        foreach ($codeExplanationQuestions as $questionData) {
            Question::create(array_merge($questionData, ['assignment_id' => $assignment->id]));
        }
    }

    private function createFillInTheBlanksQuestions(Assignment $assignment): void
    {
        $fillInBlanksQuestions = [
            [
                'language'   => QuestionLanguage::Python,
                'type'       => QuestionType::FillInTheBlanks,
                'level'      => QuestionLevel::Beginner,
                'question'   => 'Fill in the blank to complete this if-else statement: if age < 13: print("Child"); ____ age < 20: print("Teenager"); else: print("Adult")',
                'answer'     => 'elif',
                'score_max'  => 1,
            ],
            [
                'language'   => QuestionLanguage::Python,
                'type'       => QuestionType::FillInTheBlanks,
                'level'      => QuestionLevel::Intermediate,
                'question'   => 'Fill in the blank to safely get a value from a dictionary: person = {"name": "Alice", "age": 30}; height = person.____("height", "Unknown")',
                'answer'     => 'get',
                'score_max'  => 2,
            ],
            [
                'language'   => QuestionLanguage::Python,
                'type'       => QuestionType::FillInTheBlanks,
                'level'      => QuestionLevel::Advanced,
                'question'   => 'Fill in the blank to handle the specific exception type: try: number = int(input("Enter a number: ")); except ____: print("That\'s not a valid number!")',
                'answer'     => 'ValueError',
                'score_max'  => 2,
            ],
        ];

        foreach ($fillInBlanksQuestions as $questionData) {
            Question::create(array_merge($questionData, ['assignment_id' => $assignment->id]));
        }
    }
}
