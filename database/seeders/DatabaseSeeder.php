<?php

namespace Database\Seeders;

use App\Enums\QuestionLanguage;
use App\Enums\QuestionLevel;
use App\Enums\QuestionType;
use App\Models\Assignment;
use App\Models\Group;
use App\Models\Question;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    private const DEMO_ACCOUNTS = [
        'teacher' => ['email' => 'teacher@example.com', 'name' => 'Teacher User'],
        'student' => ['email' => 'student@example.com', 'name' => 'Student User'],
    ];

    private const GROUP_TEMPLATES = [
        'Introduction to Programming' => ['sections' => ['A', 'B'], 'topics' => [
            'Variables and Data Types',
            'Control Flow Structures',
            'Functions and Procedures',
        ]],
        'Advanced Python Programming' => ['sections' => [''], 'topics' => [
            'Decorators and Generators',
            'Context Managers',
            'Async Programming',
        ]],
        'Data Structures & Algorithms' => ['sections' => [''], 'topics' => [
            'Linked Lists and Trees',
            'Sorting Algorithms',
            'Graph Algorithms',
        ]],
        'Web Development Fundamentals' => ['sections' => [''], 'topics' => [
            'HTML & CSS Basics',
            'JavaScript Fundamentals',
            'DOM Manipulation',
        ]],
    ];

    public function run(): void
    {
        // Create users
        $teachers = $this->createTeachers();
        $students = $this->createStudents();

        // Create groups and assignments
        $this->createGroupsWithAssignments($teachers, $students);
    }

    private function createTeachers()
    {
        // Create demo teacher
        $teachers = collect([
            User::factory()->teacher()->create([
                'email'    => self::DEMO_ACCOUNTS['teacher']['email'],
                'name'     => self::DEMO_ACCOUNTS['teacher']['name'],
                'password' => Hash::make('password'),
            ]),
        ]);

        // Create additional teachers
        return $teachers->merge(
            User::factory()->teacher()->count(4)->create()
        );
    }

    private function createStudents()
    {
        // Create demo student
        $demoStudent = User::factory()->student()->create([
            'email'    => self::DEMO_ACCOUNTS['student']['email'],
            'name'     => self::DEMO_ACCOUNTS['student']['name'],
            'password' => Hash::make('password'),
        ]);

        // Create additional students
        return collect([$demoStudent])->merge(
            User::factory()->student()->count(49)->create()
        );
    }

    private function createGroupsWithAssignments($teachers, $students)
    {
        $teacherIndex = 0;
        $assignedStudents = collect();

        foreach (self::GROUP_TEMPLATES as $courseName => $courseData) {
            foreach ($courseData['sections'] as $section) {
                $groupName = $courseName . ($section ? " - Section {$section}" : '');

                // Create group
                $group = Group::create(['name' => $groupName]);

                // Assign teacher
                $teacher = $teachers[$teacherIndex % $teachers->count()];
                $group->users()->attach($teacher->id);
                ++$teacherIndex;

                // Assign students (20-25 per group)
                $studentCount = rand(20, 25);
                $groupStudents = $this->assignStudentsToGroup(
                    $students,
                    $assignedStudents,
                    $studentCount,
                    $group->name === array_key_first(self::GROUP_TEMPLATES) // Ensure demo student in first group
                );

                $group->users()->attach($groupStudents->pluck('id'));
                $assignedStudents = $assignedStudents->merge($groupStudents);

                // Create assignments
                $this->createAssignmentsForGroup($group, $teacher, $courseData['topics']);
            }
        }
    }

    private function assignStudentsToGroup($allStudents, $assignedStudents, $count, $isFirstGroup)
    {
        if ($isFirstGroup) {
            // Ensure demo student is in the first group
            $demoStudent = $allStudents->firstWhere('email', self::DEMO_ACCOUNTS['student']['email']);
            $otherStudents = $allStudents->where('email', '!=', self::DEMO_ACCOUNTS['student']['email'])
                ->diff($assignedStudents)
                ->random($count - 1);

            return collect([$demoStudent])->merge($otherStudents);
        }

        $availableStudents = $allStudents->diff($assignedStudents);

        return $availableStudents->random(min($count, $availableStudents->count()));
    }

    private function createAssignmentsForGroup($group, $teacher, $topics)
    {
        $assignmentCount = rand(2, 3);

        for ($i = 0; $i < $assignmentCount; ++$i) {
            $publishedAt = now()->subDays(rand(1, 30));

            $assignment = Assignment::create([
                'group_id'     => $group->id,
                'user_id'      => $teacher->id,
                'title'        => 'Assignment ' . ($i + 1) . ': ' . fake()->randomElement($topics),
                'description'  => 'Complete the following exercises to demonstrate your understanding of the concepts covered this week.',
                'published_at' => $publishedAt,
                'deadline_at'  => $publishedAt->copy()->addDays(rand(7, 14)),
            ]);

            $this->createQuestionsForAssignment($assignment, $group);
        }
    }

    private function createQuestionsForAssignment($assignment, $group)
    {
        $questionCount = rand(3, 6);

        for ($i = 0; $i < $questionCount; ++$i) {
            $question = $this->createQuestion($assignment);
            $this->createSubmissionsForQuestion($question, $group->students, $assignment->published_at);
        }
    }

    private function createQuestion($assignment)
    {
        $type = fake()->randomElement(QuestionType::cases());

        $baseData = [
            'assignment_id'             => $assignment->id,
            'language'                  => fake()->randomElement([QuestionLanguage::Python, QuestionLanguage::Python]), // Favor Python
            'type'                      => $type,
            'level'                     => fake()->randomElement(QuestionLevel::cases()),
            'estimated_answer_duration' => fake()->numberBetween(120, 480),
            'topic'                     => fake()->randomElement([
                'Variables and Data Types',
                'Control Flow',
                'Functions',
                'Arrays and Lists',
                'Object-Oriented Programming',
            ]),
            'tags'        => fake()->randomElements(['basics', 'intermediate', 'advanced', 'algorithms'], rand(2, 3)),
            'question'    => $this->getQuestionText($type),
            'explanation' => fake()->optional(0.7)->sentence(),
            'code'        => $this->getCodeSnippet(),
        ];

        // Type-specific data
        switch ($type) {
            case QuestionType::MultipleChoice:
                $options = ['return x + y', 'return x * y', 'return x - y', 'return x / y'];
                $baseData['options'] = $options;
                $baseData['answer'] = fake()->randomElement($options);
                break;

            case QuestionType::FillInTheBlanks:
                $baseData['answer'] = fake()->words(3, true);
                break;

            default: // CodeExplanation
                $baseData['answer'] = fake()->sentence();
        }

        return Question::create($baseData);
    }

    private function getQuestionText($type): string
    {
        $questions = [
            QuestionType::MultipleChoice->value => [
                'What is the output of the following code?',
                'Which statement is correct?',
                'Select the correct implementation:',
            ],
            QuestionType::FillInTheBlanks->value => [
                'Complete the missing parts of this function.',
                'Fill in the blanks to make this code work correctly.',
            ],
            QuestionType::CodeExplanation->value => [
                'Explain what this code does.',
                'What is the output and why?',
                'Analyze the following code:',
            ],
        ];

        return fake()->randomElement($questions[$type->value] ?? ['Answer the following question:']);
    }

    private function getCodeSnippet(): ?string
    {
        $snippets = [
            "def add(x, y):\n    return x + y",
            "for i in range(10):\n    print(i)",
            "lst = [1, 2, 3]\nresult = sum(lst)",
            "def factorial(n):\n    if n <= 1:\n        return 1\n    return n * factorial(n-1)",
            null, // Sometimes no code
        ];

        return fake()->randomElement($snippets);
    }

    private function createSubmissionsForQuestion($question, $students, $publishedAt)
    {
        foreach ($students as $student) {
            // 85% submission rate
            if (!fake()->boolean(85)) {
                continue;
            }

            $isCorrect = fake()->boolean(70); // 70% correct rate

            Submission::create([
                'question_id' => $question->id,
                'user_id'     => $student->id,
                'answer'      => $this->generateStudentAnswer($question, $isCorrect),
                'feedback'    => $this->generateFeedback($isCorrect, $question->topic),
                'is_correct'  => $isCorrect,
                'created_at'  => $publishedAt->copy()->addDays(rand(1, 7)),
                'updated_at'  => $publishedAt->copy()->addDays(rand(1, 7)),
            ]);
        }
    }

    private function generateStudentAnswer($question, $isCorrect)
    {
        // If correct and has an answer, use it (for multiple choice)
        if ($isCorrect && $question->answer && QuestionType::MultipleChoice === $question->type) {
            return $question->answer;
        }

        // Otherwise generate based on type
        return match ($question->type) {
            QuestionType::MultipleChoice  => fake()->randomElement($question->options ?? ['Option A']),
            QuestionType::FillInTheBlanks => fake()->words(3, true),
            default                       => fake()->sentence(),
        };
    }

    private function generateFeedback($isCorrect, $topic): ?string
    {
        if ($isCorrect) {
            return fake()->optional(0.3)->randomElement([
                'Great work!',
                'Excellent understanding!',
                'Perfect!',
            ]);
        }

        return fake()->optional(0.8)->randomElement([
            "Review the concept of {$topic}",
            'Close, but check your logic.',
            'Please review the course material.',
        ]);
    }
}
