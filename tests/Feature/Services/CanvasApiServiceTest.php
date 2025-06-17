<?php

use App\Services\CanvasApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->baseUrl = 'https://canvas.test.com';
    $this->accessToken = 'test-token';
    
    $this->canvasApiService = new CanvasApiService($this->baseUrl, $this->accessToken);
});

test('create submission success', function () {
    $courseId = 123;
    $assignmentId = 456;
    $userId = 789;
    $submissionData = ['body' => 'Test submission'];
    
    $expectedResponse = [
        'id' => 999,
        'user_id' => $userId,
        'assignment_id' => $assignmentId,
        'workflow_state' => 'submitted'
    ];

    Http::fake([
        "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions" => Http::response($expectedResponse, 200)
    ]);

    Log::shouldReceive('info')
        ->once()
        ->with('Canvas submission created successfully', [
            'course_id' => $courseId,
            'assignment_id' => $assignmentId,
            'user_id' => $userId
        ]);

    $result = $this->canvasApiService->createSubmission($courseId, $assignmentId, $userId, $submissionData);

    expect($result)->toBe($expectedResponse);
    
    Http::assertSent(function ($request) use ($courseId, $assignmentId, $userId) {
        return $request->url() === "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions" &&
               $request->method() === 'POST' &&
               $request->header('Authorization')[0] === "Bearer {$this->accessToken}" &&
               $request->header('Content-Type')[0] === 'application/json' &&
               $request['submission']['user_id'] === $userId;
    });
});

test('create submission with default data', function () {
    $courseId = 123;
    $assignmentId = 456;
    $userId = 789;
    
    $expectedResponse = ['id' => 999];

    Http::fake([
        "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions" => Http::response($expectedResponse, 200)
    ]);

    Log::shouldReceive('info')->once();

    $result = $this->canvasApiService->createSubmission($courseId, $assignmentId, $userId);

    expect($result)->toBe($expectedResponse);
    
    Http::assertSent(function ($request) {
        return $request['submission']['submission_type'] === 'online_text_entry' &&
               $request['submission']['body'] === 'LTI Assignment Submission';
    });
});

test('create submission failure', function () {
    $courseId = 123;
    $assignmentId = 456;
    $userId = 789;

    Http::fake([
        "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions" => Http::response(['error' => 'Unauthorized'], 401)
    ]);

    Log::shouldReceive('error')
        ->once()
        ->with('Failed to create Canvas submission', [
            'status' => 401,
            'response' => '{"error":"Unauthorized"}'
        ]);

    $result = $this->canvasApiService->createSubmission($courseId, $assignmentId, $userId);

    expect($result)->toBeNull();
});

test('create submission exception', function () {
    $courseId = 123;
    $assignmentId = 456;
    $userId = 789;

    Http::fake(function () {
        throw new \Exception('Network error');
    });

    Log::shouldReceive('error')
        ->once()
        ->with('Canvas API error creating submission', [
            'error' => 'Network error',
            'course_id' => $courseId,
            'assignment_id' => $assignmentId,
            'user_id' => $userId
        ]);

    $result = $this->canvasApiService->createSubmission($courseId, $assignmentId, $userId);

    expect($result)->toBeNull();
});

test('grade submission success', function () {
    $courseId = 123;
    $assignmentId = 456;
    $userId = 789;
    $score = 85.5;
    $comment = 'Great work!';
    
    $expectedResponse = [
        'id' => 999,
        'grade' => $score,
        'workflow_state' => 'graded'
    ];

    Http::fake([
        "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" => Http::response($expectedResponse, 200)
    ]);

    Log::shouldReceive('info')
        ->once()
        ->with('Canvas submission graded successfully', [
            'course_id' => $courseId,
            'assignment_id' => $assignmentId,
            'user_id' => $userId,
            'score' => $score
        ]);

    $result = $this->canvasApiService->gradeSubmission($courseId, $assignmentId, $userId, $score, $comment);

    expect($result)->toBe($expectedResponse);
    
    Http::assertSent(function ($request) use ($courseId, $assignmentId, $userId, $score, $comment) {
        return $request->url() === "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" &&
               $request->method() === 'PUT' &&
               $request['submission']['posted_grade'] === $score &&
               $request['comment']['text_comment'] === $comment;
    });
});

test('grade submission without comment', function () {
    $courseId = 123;
    $assignmentId = 456;
    $userId = 789;
    $score = 85.5;
    
    $expectedResponse = ['id' => 999];

    Http::fake([
        "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" => Http::response($expectedResponse, 200)
    ]);

    Log::shouldReceive('info')->once();

    $result = $this->canvasApiService->gradeSubmission($courseId, $assignmentId, $userId, $score);

    expect($result)->toBe($expectedResponse);
    
    Http::assertSent(function ($request) use ($score) {
        return $request['submission']['posted_grade'] === $score &&
               !isset($request['comment']);
    });
});

test('get submission success', function () {
    $courseId = 123;
    $assignmentId = 456;
    $userId = 789;
    
    $expectedResponse = [
        'id' => 999,
        'user_id' => $userId,
        'workflow_state' => 'submitted'
    ];

    Http::fake([
        "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" => Http::response($expectedResponse, 200)
    ]);

    $result = $this->canvasApiService->getSubmission($courseId, $assignmentId, $userId);

    expect($result)->toBe($expectedResponse);
    
    Http::assertSent(function ($request) use ($courseId, $assignmentId, $userId) {
        return $request->url() === "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" &&
               $request->method() === 'GET';
    });
});

test('get submission not found', function () {
    $courseId = 123;
    $assignmentId = 456;
    $userId = 789;

    Http::fake([
        "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" => Http::response(['error' => 'Not found'], 404)
    ]);

    $result = $this->canvasApiService->getSubmission($courseId, $assignmentId, $userId);

    expect($result)->toBeNull();
});

test('submit and grade creates submission when none exists', function () {
    $courseId = 123;
    $assignmentId = 456;
    $userId = 789;
    $score = 95.0;
    $comment = 'Excellent!';

    Http::fake([
        "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" => Http::sequence()
            ->push(['workflow_state' => 'unsubmitted'], 200) // First call (getSubmission)
            ->push(['id' => 999], 200), // Second call (gradeSubmission)
        "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions" => Http::response(['id' => 999], 200)
    ]);

    Log::shouldReceive('info')->times(2);

    $result = $this->canvasApiService->submitAndGrade($courseId, $assignmentId, $userId, $score, $comment);

    expect($result)->toBeTrue();
    
    // Should have called createSubmission and gradeSubmission
    Http::assertSentCount(3); // getSubmission, createSubmission, gradeSubmission
});

test('submit and grade skips creation when submission exists', function () {
    $courseId = 123;
    $assignmentId = 456;
    $userId = 789;
    $score = 95.0;

    Http::fake([
        "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" => Http::sequence()
            ->push(['id' => 999, 'workflow_state' => 'submitted'], 200) // First call (getSubmission)
            ->push(['id' => 999, 'grade' => $score], 200) // Second call (gradeSubmission)
    ]);

    Log::shouldReceive('info')->once(); // Only for grading

    $result = $this->canvasApiService->submitAndGrade($courseId, $assignmentId, $userId, $score);

    expect($result)->toBeTrue();
    
    // Should only have called getSubmission and gradeSubmission
    Http::assertSentCount(2);
});

test('submit and grade returns false on creation failure', function () {
    $courseId = 123;
    $assignmentId = 456;
    $userId = 789;
    $score = 95.0;

    Http::fake([
        "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" => Http::response(['workflow_state' => 'unsubmitted'], 200),
        "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions" => Http::response(['error' => 'Failed'], 400)
    ]);

    Log::shouldReceive('error')->once();

    $result = $this->canvasApiService->submitAndGrade($courseId, $assignmentId, $userId, $score);

    expect($result)->toBeFalse();
});

test('submit and grade returns false on grading failure', function () {
    $courseId = 123;
    $assignmentId = 456;
    $userId = 789;
    $score = 95.0;

    Http::fake([
        "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" => Http::sequence()
            ->push(['workflow_state' => 'unsubmitted'], 200)
            ->push(['error' => 'Grading failed'], 400),
        "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions" => Http::response(['id' => 999], 200)
    ]);

    Log::shouldReceive('info')->once(); // For creation
    Log::shouldReceive('error')->once(); // For grading failure

    $result = $this->canvasApiService->submitAndGrade($courseId, $assignmentId, $userId, $score);

    expect($result)->toBeFalse();
});
