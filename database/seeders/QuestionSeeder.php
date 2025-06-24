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
                'language'                  => QuestionLanguage::Python,
                'type'                      => QuestionType::MultipleChoice,
                'level'                     => QuestionLevel::Beginner,
                'estimated_answer_duration' => 60,
                'topic'                     => 'Data Types',
                'tags'                      => ['variables', 'types', 'basic'],
                'question'                  => 'Which of the following is NOT a valid Python data type?',
                'explanation'               => 'Python has several built-in data types including int, float, str, bool, list, tuple, dict, and set. "char" is not a built-in data type in Python.',
                'code'                      => null,
                'options'                   => ['int', 'float', 'char', 'str'],
                'answer'                    => 'char',
            ],
            [
                'language'                  => QuestionLanguage::Python,
                'type'                      => QuestionType::MultipleChoice,
                'level'                     => QuestionLevel::Intermediate,
                'estimated_answer_duration' => 90,
                'topic'                     => 'Lists',
                'tags'                      => ['lists', 'methods', 'mutability'],
                'question'                  => 'What will be the output of the following code?',
                'explanation'               => 'The append() method adds the entire list [4, 5] as a single element, while extend() would add each element individually.',
                'code'                      => "my_list = [1, 2, 3]\nmy_list.append([4, 5])\nprint(len(my_list))",
                'options'                   => ['3', '4', '5', 'Error'],
                'answer'                    => '4',
            ],
            [
                'language'                  => QuestionLanguage::Python,
                'type'                      => QuestionType::MultipleChoice,
                'level'                     => QuestionLevel::Advanced,
                'estimated_answer_duration' => 120,
                'topic'                     => 'Scope',
                'tags'                      => ['scope', 'variables', 'functions'],
                'question'                  => 'What will be printed by this code?',
                'explanation'               => 'The global keyword allows the function to modify the global variable x. Without it, a local variable would be created.',
                'code'                      => "x = 10\n\ndef modify_x():\n    global x\n    x = 20\n\nmodify_x()\nprint(x)",
                'options'                   => ['10', '20', 'Error', 'None'],
                'answer'                    => '20',
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
                'language'                  => QuestionLanguage::Python,
                'type'                      => QuestionType::CodeExplanation,
                'level'                     => QuestionLevel::Beginner,
                'estimated_answer_duration' => 120,
                'topic'                     => 'Loops',
                'tags'                      => ['for-loop', 'range', 'iteration'],
                'question'                  => 'Explain what this code does and what it will output.',
                'explanation'               => 'This code uses a for loop to iterate through numbers 0 to 4 (range(5) generates 0,1,2,3,4) and prints each number multiplied by 2.',
                'code'                      => "for i in range(5):\n    print(i * 2)",
                'options'                   => null,
                'answer'                    => 'This code prints the even numbers from 0 to 8. It iterates through the range 0-4 and multiplies each number by 2, outputting: 0, 2, 4, 6, 8 on separate lines.',
            ],
            [
                'language'                  => QuestionLanguage::Python,
                'type'                      => QuestionType::CodeExplanation,
                'level'                     => QuestionLevel::Intermediate,
                'estimated_answer_duration' => 180,
                'topic'                     => 'List Comprehensions',
                'tags'                      => ['list-comprehension', 'filtering', 'conditionals'],
                'question'                  => 'Explain what this list comprehension does and what the result will be.',
                'explanation'               => 'This list comprehension creates a new list containing only the even numbers from the original list, but squared.',
                'code'                      => "numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]\nresult = [x**2 for x in numbers if x % 2 == 0]\nprint(result)",
                'options'                   => null,
                'answer'                    => 'This creates a list of squares of even numbers from the original list. It filters for even numbers (x % 2 == 0) and squares them (x**2). The output will be [4, 16, 36, 64, 100].',
            ],
            [
                'language'    => QuestionLanguage::Python,
                'type'        => QuestionType::CodeExplanation,
                'level'       => QuestionLevel::Advanced,
                'topic'       => 'Decorators',
                'tags'        => ['decorators', 'functions', 'higher-order'],
                'question'    => 'Explain how this decorator works and what happens when the decorated function is called.',
                'explanation' => 'This is a timing decorator that measures and prints the execution time of the decorated function.',
                'code'        => "import time\n\ndef timer(func):\n    def wrapper(*args, **kwargs):\n        start = time.time()\n        result = func(*args, **kwargs)\n        end = time.time()\n        print(f\"{func.__name__} took {end - start:.2f} seconds\")\n        return result\n    return wrapper\n\n@timer\ndef slow_function():\n    time.sleep(1)\n    return \"Done\"\n\nslow_function()",
                'options'     => null,
                'answer'      => 'This is a timing decorator that measures function execution time. When slow_function() is called, the decorator wrapper function starts a timer, executes the original function, stops the timer, prints the execution time, and returns the result. It will print something like "slow_function took 1.00 seconds" and return "Done".',
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
                'language'    => QuestionLanguage::Python,
                'type'        => QuestionType::FillInTheBlanks,
                'level'       => QuestionLevel::Beginner,
                'topic'       => 'Conditional Statements',
                'tags'        => ['if-else', 'conditionals', 'boolean'],
                'question'    => 'Fill in the blank to complete this if-else statement.',
                'explanation' => 'The elif keyword is used for additional conditions in Python if-else statements.',
                'code'        => "age = 18\nif age < 13:\n    print(\"Child\")\n____ age < 20:\n    print(\"Teenager\")\nelse:\n    print(\"Adult\")",
                'options'     => ['elif', 'elseif', 'else if', 'elsif'],
                'answer'      => 'elif',
            ],
            [
                'language'    => QuestionLanguage::Python,
                'type'        => QuestionType::FillInTheBlanks,
                'level'       => QuestionLevel::Intermediate,
                'topic'       => 'Dictionaries',
                'tags'        => ['dictionaries', 'methods', 'key-value'],
                'question'    => 'Fill in the blank to safely get a value from a dictionary with a default.',
                'explanation' => 'The get() method returns the value for a key if it exists, or a default value if it doesn\'t.',
                'code'        => "person = {'name': 'Alice', 'age': 30}\nheight = person.____('height', 'Unknown')\nprint(height)",
                'options'     => ['get', 'fetch', 'retrieve', 'obtain'],
                'answer'      => 'get',
            ],
            [
                'language'    => QuestionLanguage::Python,
                'type'        => QuestionType::FillInTheBlanks,
                'level'       => QuestionLevel::Advanced,
                'topic'       => 'Exception Handling',
                'tags'        => ['exceptions', 'try-catch', 'error-handling'],
                'question'    => 'Fill in the blank to handle the specific exception type.',
                'explanation' => 'ValueError is raised when a function receives an argument of correct type but inappropriate value.',
                'code'        => "try:\n    number = int(input(\"Enter a number: \"))\nexcept ____:\n    print(\"That's not a valid number!\")",
                'options'     => ['ValueError', 'TypeError', 'Exception', 'Error'],
                'answer'      => 'ValueError',
            ],
        ];

        foreach ($fillInBlanksQuestions as $questionData) {
            Question::create(array_merge($questionData, ['assignment_id' => $assignment->id]));
        }
    }
}
