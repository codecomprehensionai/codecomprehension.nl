<?php

namespace Tests\Unit\Services;

use App\Services\CanvasApiService;
use App\Services\ScoreCalculationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ServicesIntegrationTest extends TestCase
{
    private CanvasApiService $canvasApiService;
    private ScoreCalculationService $scoreService;
    private string $baseUrl = 'https://canvas.test.com';
    private string $accessToken = 'test-token';

    protected function setUp(): void
    {
        parent::setUp();
        
        config(['canvas.base_url' => $this->baseUrl]);
        config(['canvas.access_token' => $this->accessToken]);
        
        $this->canvasApiService = new CanvasApiService();
        $this->scoreService = new ScoreCalculationService();
    }

    public function test_complete_grading_workflow(): void
    {
        // Arrange
        $courseId = 123;
        $assignmentId = 456;
        $userId = 789;
        $correctAnswers = 8;
        $totalQuestions = 10;
        $maxPoints = 100.0;

        // Calculate score using ScoreCalculationService
        $percentageScore = $this->scoreService->calculatePercentageScore($correctAnswers, $totalQuestions);
        $finalScore = $this->scoreService->convertToPoints($percentageScore, $maxPoints);
        $comment = $this->scoreService->generateComment($percentageScore, $correctAnswers, $totalQuestions);

        // Mock Canvas API responses
        Http::fake([
            "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" => Http::sequence()
                ->push(['workflow_state' => 'unsubmitted'], 200) // getSubmission
                ->push(['id' => 999, 'grade' => $finalScore], 200), // gradeSubmission
            "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions" => Http::response(['id' => 999], 200) // createSubmission
        ]);

        Log::shouldReceive('info')->times(2);

        // Act
        $success = $this->canvasApiService->submitAndGrade(
            $courseId,
            $assignmentId,
            $userId,
            $finalScore,
            $comment,
            [
                'submission' => [
                    'body' => "Assignment completed with score: {$correctAnswers}/{$totalQuestions} ({$percentageScore}%)"
                ]
            ]
        );

        // Assert
        $this->assertTrue($success);
        $this->assertEquals(80.0, $percentageScore);
        $this->assertEquals(80.0, $finalScore);
        $this->assertStringContainsString('8/10 (80%)', $comment);
        $this->assertStringContainsString('Good job!', $comment);

        // Verify API calls were made correctly
        Http::assertSent(function ($request) use ($finalScore, $comment) {
            return $request->method() === 'PUT' &&
                   $request['submission']['posted_grade'] === $finalScore &&
                   $request['comment']['text_comment'] === $comment;
        });
    }

    public function test_perfect_score_workflow(): void
    {
        // Arrange
        $courseId = 123;
        $assignmentId = 456;
        $userId = 789;
        $correctAnswers = 10;
        $totalQuestions = 10;
        $maxPoints = 50.0;

        // Calculate perfect score
        $percentageScore = $this->scoreService->calculatePercentageScore($correctAnswers, $totalQuestions);
        $finalScore = $this->scoreService->convertToPoints($percentageScore, $maxPoints);
        $comment = $this->scoreService->generateComment($percentageScore, $correctAnswers, $totalQuestions);

        // Mock Canvas API responses for existing submission
        Http::fake([
            "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" => Http::sequence()
                ->push(['id' => 999, 'workflow_state' => 'submitted'], 200) // getSubmission - already exists
                ->push(['id' => 999, 'grade' => $finalScore], 200) // gradeSubmission
        ]);

        Log::shouldReceive('info')->once();

        // Act
        $success = $this->canvasApiService->submitAndGrade(
            $courseId,
            $assignmentId,
            $userId,
            $finalScore,
            $comment
        );

        // Assert
        $this->assertTrue($success);
        $this->assertEquals(100.0, $percentageScore);
        $this->assertEquals(50.0, $finalScore); // 100% of 50 points
        $this->assertStringContainsString('10/10 (100%)', $comment);
        $this->assertStringContainsString('Excellent work!', $comment);

        // Should only call getSubmission and gradeSubmission (no createSubmission)
        Http::assertSentCount(2);
    }

    public function test_failing_score_workflow(): void
    {
        // Arrange
        $courseId = 123;
        $assignmentId = 456;
        $userId = 789;
        $correctAnswers = 2;
        $totalQuestions = 10;
        $maxPoints = 100.0;

        // Calculate failing score
        $percentageScore = $this->scoreService->calculatePercentageScore($correctAnswers, $totalQuestions);
        $finalScore = $this->scoreService->convertToPoints($percentageScore, $maxPoints);
        $comment = $this->scoreService->generateComment($percentageScore, $correctAnswers, $totalQuestions);

        // Mock Canvas API responses
        Http::fake([
            "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" => Http::sequence()
                ->push(['workflow_state' => 'unsubmitted'], 200)
                ->push(['id' => 999, 'grade' => $finalScore], 200),
            "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions" => Http::response(['id' => 999], 200)
        ]);

        Log::shouldReceive('info')->times(2);

        // Act
        $success = $this->canvasApiService->submitAndGrade(
            $courseId,
            $assignmentId,
            $userId,
            $finalScore,
            $comment
        );

        // Assert
        $this->assertTrue($success);
        $this->assertEquals(20.0, $percentageScore);
        $this->assertEquals(20.0, $finalScore);
        $this->assertStringContainsString('2/10 (20%)', $comment);
        $this->assertStringContainsString('Consider reviewing the material.', $comment);
    }

    public function test_canvas_api_failure_with_calculated_score(): void
    {
        // Arrange
        $courseId = 123;
        $assignmentId = 456;
        $userId = 789;
        $correctAnswers = 7;
        $totalQuestions = 10;

        // Calculate score normally
        $percentageScore = $this->scoreService->calculatePercentageScore($correctAnswers, $totalQuestions);
        $finalScore = $this->scoreService->convertToPoints($percentageScore);

        // Mock Canvas API failure
        Http::fake([
            "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" => Http::response(['error' => 'Unauthorized'], 401)
        ]);

        Log::shouldReceive('error')->once();

        // Act
        $success = $this->canvasApiService->submitAndGrade(
            $courseId,
            $assignmentId,
            $userId,
            $finalScore
        );

        // Assert
        $this->assertFalse($success);
        $this->assertEquals(70.0, $percentageScore); // Score calculation should still work
        $this->assertEquals(70.0, $finalScore);
    }

    public function test_zero_division_protection_in_workflow(): void
    {
        // Arrange
        $courseId = 123;
        $assignmentId = 456;
        $userId = 789;
        $correctAnswers = 5;
        $totalQuestions = 0; // This should be handled gracefully

        // Calculate score with zero total questions
        $percentageScore = $this->scoreService->calculatePercentageScore($correctAnswers, $totalQuestions);
        $finalScore = $this->scoreService->convertToPoints($percentageScore);
        $comment = $this->scoreService->generateComment($percentageScore, $correctAnswers, $totalQuestions);

        // Mock Canvas API
        Http::fake([
            "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" => Http::sequence()
                ->push(['workflow_state' => 'unsubmitted'], 200)
                ->push(['id' => 999, 'grade' => $finalScore], 200),
            "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions" => Http::response(['id' => 999], 200)
        ]);

        Log::shouldReceive('info')->times(2);

        // Act
        $success = $this->canvasApiService->submitAndGrade(
            $courseId,
            $assignmentId,
            $userId,
            $finalScore,
            $comment
        );

        // Assert
        $this->assertTrue($success);
        $this->assertEquals(0.0, $percentageScore); // Should handle division by zero
        $this->assertEquals(0.0, $finalScore);
        $this->assertStringContainsString('5/0 (0%)', $comment); // Should handle gracefully
    }

    public function test_custom_max_points_workflow(): void
    {
        // Arrange
        $courseId = 123;
        $assignmentId = 456;
        $userId = 789;
        $correctAnswers = 9;
        $totalQuestions = 12;
        $maxPoints = 75.0; // Custom max points

        // Calculate score with custom max points
        $percentageScore = $this->scoreService->calculatePercentageScore($correctAnswers, $totalQuestions);
        $finalScore = $this->scoreService->convertToPoints($percentageScore, $maxPoints);
        $comment = $this->scoreService->generateComment($percentageScore, $correctAnswers, $totalQuestions);

        // Mock Canvas API
        Http::fake([
            "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}" => Http::sequence()
                ->push(['workflow_state' => 'unsubmitted'], 200)
                ->push(['id' => 999, 'grade' => $finalScore], 200),
            "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions" => Http::response(['id' => 999], 200)
        ]);

        Log::shouldReceive('info')->times(2);

        // Act
        $success = $this->canvasApiService->submitAndGrade(
            $courseId,
            $assignmentId,
            $userId,
            $finalScore,
            $comment
        );

        // Assert
        $this->assertTrue($success);
        $this->assertEquals(75.0, $percentageScore); // 9/12 = 75%
        $this->assertEquals(56.25, $finalScore); // 75% of 75 points = 56.25
        $this->assertStringContainsString('9/12 (75%)', $comment);
        $this->assertStringContainsString('Well done!', $comment);

        // Verify the correct score was sent to Canvas
        Http::assertSent(function ($request) use ($finalScore) {
            return $request->method() === 'PUT' && 
                   isset($request['submission']) && 
                   $request['submission']['posted_grade'] === $finalScore;
        });
    }
}
