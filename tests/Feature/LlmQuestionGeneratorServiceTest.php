<?php

use App\Services\LlmQuestionGeneratorService;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\JwtKey;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can call generateQuestion', function () {
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
        'public_key' => 'test-public-key',
        'private_key' => 'test-private-key',
    ]);
    
    $service = new LlmQuestionGeneratorService();
    $result = $service->generateQuestion($assignment, [], 'test prompt');
    
    logger()->info('Generated question result:', ['result' => $result]);
    expect($result)->not->toBeNull();
});
