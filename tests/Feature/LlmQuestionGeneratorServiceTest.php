<?php

use App\Services\LlmQuestionGeneratorService;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\JwtKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

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
        'public_key' => '------BEGIN PUBLIC KEY-----
MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEYc6cTyL/zN9j7lXcFDSWC/jq8zJH
K89QjN6RS13zV1WlHekvlrskTYObH1/xE5WQXAjLyEjpjhVz2xwKJAx8Jg==
-----END PUBLIC KEY-----
',
        'private_key' => '-----BEGIN EC PRIVATE KEY-----
MHcCAQEEIPJiaLPjFrhJ9EcmZPKFD1hwY8hskP4/zjOD5dCTADltoAoGCCqGSM49
AwEHoUQDQgAEYc6cTyL/zN9j7lXcFDSWC/jq8zJHK89QjN6RS13zV1WlHekvlrsk
TYObH1/xE5WQXAjLyEjpjhVz2xwKJAx8Jg==
-----END EC PRIVATE KEY-----
',
    ]);

    $service = new LlmQuestionGeneratorService();
    $result = $service->generateQuestion($assignment, [], 'test prompt');

    logger()->info('Generated question result:', ['result' => $result]);
    expect($result)->not->toBeNull();
});
