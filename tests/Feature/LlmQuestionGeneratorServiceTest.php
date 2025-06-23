<?php

use App\Data\QuestionData;
use App\Enums\QuestionLanguage;
use App\Enums\QuestionLevel;
use App\Enums\QuestionType;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\JwtKey;
use App\Models\Question;
use App\Services\LlmQuestionGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(classAndTraits: RefreshDatabase::class);

it('can call generateQuestion', function () {
    Http::allowStrayRequests();
    config(['llm.timeout' => 300]);

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
        'name' => 'test-key'
    ]);

    $questionData = QuestionData::fromArray([
        'language' => 'python',
        'type' => 'multiple_choice',
        'level' => 'beginner',
        'estimated_answer_duration' => 5,
        'topic' => 'variables',
        'tags' => ['python', 'basics'],
        'question' => 'What is a variable?',
    ]);

    $service = new LlmQuestionGeneratorService;
    $result = $service->generateQuestion($assignment, $questionData, 'test prompt');

    logger()->info('Generated question result:', ['result' => $result]);
    expect($result)->not->toBeNull();
});

it('can call updateQuestion', function () {
    Http::allowStrayRequests();
    config(['llm.timeout' => 300]);

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
        'private_key' => config('llm.testing_private_key', null),
    ]);

    $service = new LlmQuestionGeneratorService;

    $updateData = QuestionData::fromArray([
        'language' => 'python',
        'type' => 'multiple_choice',
        'level' => 'intermediate',
        'estimated_answer_duration' => 7,
        'topic' => 'functions',
        'tags' => ['python', 'functions'],
        'question' => 'Updated question about functions',
    ]);

    $result = $service->updateQuestion(
        $assignment,
        $existingQuestion,
        $updateData,
        'Update this question to focus on Python functions instead of variables'
    );

    logger()->info('Updated question result:', ['result' => $result]);
    expect($result)->not->toBeNull();
});

it('returns null if generateQuestion fails', function () {
    Http::fake([
        '*' => Http::response(['success' => false], 400),
    ]);

    $course = Course::factory()->create();
    $assignment = Assignment::factory()->create(['course_id' => $course->id]);
    JwtKey::factory()->create();

    $questionData = QuestionData::fromArray([
        'language' => 'python',
        'type' => 'multiple_choice',
        'level' => 'beginner',
        'estimated_answer_duration' => 3,
        'question' => 'Test question',
    ]);

    $service = new LlmQuestionGeneratorService;
    $result = $service->generateQuestion($assignment, $questionData, 'test prompt');
    expect($result)->toBeNull();
});

it('returns null if updateQuestion fails', function () {
    Http::fake([
        '*' => Http::response(['success' => false], 400),
    ]);

    $course = Course::factory()->create();
    $assignment = Assignment::factory()->create(['course_id' => $course->id]);
    $question = Question::factory()->create(['assignment_id' => $assignment->id]);
    JwtKey::factory()->create();

    $updateData = QuestionData::fromArray([
        'language' => 'python',
        'type' => 'multiple_choice',
        'level' => 'intermediate',
        'estimated_answer_duration' => 5,
        'question' => 'Updated question',
    ]);

    $service = new LlmQuestionGeneratorService;
    $result = $service->updateQuestion($assignment, $question, $updateData, 'prompt');
    expect($result)->toBeNull();
});

it('can check /health endpoint with isAvailable', function () {
    Http::allowStrayRequests();
    $service = new LlmQuestionGeneratorService;
    expect($service->isAvailable())->toBeTrue();
});

