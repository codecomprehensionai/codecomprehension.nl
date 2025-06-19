<?php

use App\Services\LlmQuestionGeneratorService;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Question;
use App\Models\JwtKey;
use App\Enums\QuestionLanguage;
use App\Enums\QuestionType;
use App\Enums\QuestionLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(classAndTraits: RefreshDatabase::class);

it('can call generateQuestion', function () {
    // Allow requests to production LLM service

    Http::allowStrayRequests();

    // Override LLM timeout for this test
    config(['llm.timeout' => 300]); // 5 minutes

    $course = Course::create([
        'lti_id' => 'test_course_123',
        'title' => 'Test Course',
    ]);

    $assignment = Assignment::create([
        'lti_id' => 'test_assignment_123',
        'title' => 'Test Assignment',
        'description' => 'Test Description',
        'lti_lineitem_endpoint' => 'http://test.com',
        'course_id' => $course->id,
    ]);

    JwtKey::create([
        'name' => 'test-key',
        'public_key' => config('llm.testing_public_key', null),
        'private_key' => config('llm.testing_private_key', null)
    ]);

    $service = new LlmQuestionGeneratorService();
    $result = $service->generateQuestion($assignment, [], 'test prompt');

    logger()->info('Generated question result:', ['result' => $result]);
    expect($result)->not->toBeNull();
});

it('can call updateQuestion', function () {
    // Allow requests to production LLM service
    Http::allowStrayRequests();

    // Override LLM timeout for this test
    config(['llm.timeout' => 300]); // 5 minutes

    $course = Course::create([
        'lti_id' => 'test_course_456',
        'title' => 'Test Course for Update',
    ]);

    $assignment = Assignment::create([
        'lti_id' => 'test_assignment_456',
        'title' => 'Test Assignment for Update',
        'description' => 'Test Description for Update',
        'lti_lineitem_endpoint' => 'http://test.com',
        'course_id' => $course->id,
    ]);

    // Create an existing question to update
    $existingQuestion = Question::create([
        'assignment_id' => $assignment->id,
        'language' => QuestionLanguage::Python,
        'type' => QuestionType::MultipleChoice,
        'level' => QuestionLevel::Beginner,
        'estimated_answer_duration' => 5,
        'topic' => 'variables',
        'tags' => ['python', 'basics'],
        'question' => 'What is a variable in Python?',
        'explanation' => 'A variable is a container for storing data values.',
        'code' => 'x = 5',
        'options' => ['A container', 'A function', 'A loop', 'A condition'],
        'answer' => 'A container',
    ]);

    JwtKey::create([
        'name' => 'test-key-update',
        'public_key' => config('llm.testing_public_key', null),
        'private_key' => config('llm.testing_private_key', null)
    ]);

    $service = new LlmQuestionGeneratorService();

    // Parameters for updating the question
    $updateParams = [
        'language' => 'python',
        'type' => 'multiple_choice',
        'level' => 'intermediate',
        'estimated_answer_duration' => 7,
        'topics' => ['functions'],
        'tags' => ['python', 'functions']
    ];

    $result = $service->updateQuestion(
        $assignment,
        $existingQuestion,
        $updateParams,
        'Update this question to focus on Python functions instead of variables'
    );

    logger()->info('Updated question result:', ['result' => $result]);
    expect($result)->not->toBeNull();
});
