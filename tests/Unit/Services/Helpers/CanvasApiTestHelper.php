<?php

namespace Tests\Unit\Services\Helpers;

use Illuminate\Support\Facades\Http;

class CanvasApiTestHelper
{
    /**
     * Create a successful submission response
     */
    public static function successfulSubmissionResponse(int $submissionId, int $userId, int $assignmentId): array
    {
        return [
            'id' => $submissionId,
            'user_id' => $userId,
            'assignment_id' => $assignmentId,
            'workflow_state' => 'submitted',
            'submission_type' => 'online_text_entry',
            'submitted_at' => now()->toISOString(),
        ];
    }

    /**
     * Create a successful grading response
     */
    public static function successfulGradingResponse(int $submissionId, float $grade): array
    {
        return [
            'id' => $submissionId,
            'grade' => $grade,
            'workflow_state' => 'graded',
            'graded_at' => now()->toISOString(),
        ];
    }

    /**
     * Create an unsubmitted submission response
     */
    public static function unsubmittedSubmissionResponse(): array
    {
        return [
            'workflow_state' => 'unsubmitted',
            'submission_type' => null,
            'body' => null,
        ];
    }

    /**
     * Create an error response
     */
    public static function errorResponse(string $message, int $statusCode = 400): array
    {
        return [
            'error' => $message,
            'status' => $statusCode,
        ];
    }

    /**
     * Mock Canvas API for a complete submission and grading workflow
     */
    public static function mockCompleteWorkflow(
        string $baseUrl,
        int $courseId,
        int $assignmentId,
        int $userId,
        bool $submissionExists = false,
        bool $createSubmissionSuccess = true,
        bool $gradingSuccess = true,
        float $grade = 85.0
    ): void {
        $getSubmissionUrl = "{$baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}";
        $createSubmissionUrl = "{$baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions";

        $responses = [];

        // First call - getSubmission
        if ($submissionExists) {
            $responses[$getSubmissionUrl] = Http::sequence()
                ->push(self::successfulSubmissionResponse(999, $userId, $assignmentId), 200);
        } else {
            $responses[$getSubmissionUrl] = Http::sequence()
                ->push(self::unsubmittedSubmissionResponse(), 200);
        }

        // Second call - createSubmission (if needed)
        if (!$submissionExists) {
            if ($createSubmissionSuccess) {
                $responses[$createSubmissionUrl] = Http::response(
                    self::successfulSubmissionResponse(999, $userId, $assignmentId),
                    200
                );
            } else {
                $responses[$createSubmissionUrl] = Http::response(
                    self::errorResponse('Failed to create submission'),
                    400
                );
            }
        }

        // Third call - gradeSubmission
        if ($gradingSuccess) {
            if (isset($responses[$getSubmissionUrl])) {
                $responses[$getSubmissionUrl] = $responses[$getSubmissionUrl]->push(
                    self::successfulGradingResponse(999, $grade),
                    200
                );
            } else {
                $responses[$getSubmissionUrl] = Http::response(
                    self::successfulGradingResponse(999, $grade),
                    200
                );
            }
        } else {
            if (isset($responses[$getSubmissionUrl])) {
                $responses[$getSubmissionUrl] = $responses[$getSubmissionUrl]->push(
                    self::errorResponse('Grading failed'),
                    400
                );
            } else {
                $responses[$getSubmissionUrl] = Http::response(
                    self::errorResponse('Grading failed'),
                    400
                );
            }
        }

        Http::fake($responses);
    }

    /**
     * Assert that a Canvas API request was made with correct parameters
     */
    public static function assertSubmissionCreated(
        string $baseUrl,
        int $courseId,
        int $assignmentId,
        int $userId,
        string $expectedBody = null
    ): void {
        Http::assertSent(function ($request) use ($baseUrl, $courseId, $assignmentId, $userId, $expectedBody) {
            $correctUrl = $request->url() === "{$baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions";
            $correctMethod = $request->method() === 'POST';
            $correctUserId = $request['submission']['user_id'] === $userId;
            $hasAuthHeader = str_starts_with($request->header('Authorization')[0] ?? '', 'Bearer ');

            $bodyMatches = true;
            if ($expectedBody !== null) {
                $bodyMatches = $request['submission']['body'] === $expectedBody;
            }

            return $correctUrl && $correctMethod && $correctUserId && $hasAuthHeader && $bodyMatches;
        });
    }

    /**
     * Assert that a Canvas grading request was made with correct parameters
     */
    public static function assertGradingSubmitted(
        string $baseUrl,
        int $courseId,
        int $assignmentId,
        int $userId,
        float $expectedGrade,
        string $expectedComment = null
    ): void {
        Http::assertSent(function ($request) use ($baseUrl, $courseId, $assignmentId, $userId, $expectedGrade, $expectedComment) {
            $correctUrl = $request->url() === "{$baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}";
            $correctMethod = $request->method() === 'PUT';
            $correctGrade = $request['submission']['posted_grade'] === $expectedGrade;
            $hasAuthHeader = str_starts_with($request->header('Authorization')[0] ?? '', 'Bearer ');

            $commentMatches = true;
            if ($expectedComment !== null) {
                $commentMatches = ($request['comment']['text_comment'] ?? null) === $expectedComment;
            }

            return $correctUrl && $correctMethod && $correctGrade && $hasAuthHeader && $commentMatches;
        });
    }
}
