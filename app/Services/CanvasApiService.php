<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CanvasApiService
{
    private string $baseUrl;
    private string $accessToken;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('canvas.base_url'), '/');
        $this->accessToken = config('canvas.access_token');
    }

    /**
     * Create a submission for a student
     */
    public function createSubmission(int $courseId, int $assignmentId, int $userId, array $submissionData = []): ?array
    {
        $url = "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions";
        
        $defaultData = [
            'submission' => [
                'user_id' => $userId,
                'submission_type' => 'online_text_entry',
                'body' => $submissionData['body'] ?? 'LTI Assignment Submission',
            ]
        ];

        $data = array_merge_recursive($defaultData, $submissionData);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ])->post($url, $data);

            if ($response->successful()) {
                Log::info('Canvas submission created successfully', [
                    'course_id' => $courseId,
                    'assignment_id' => $assignmentId,
                    'user_id' => $userId
                ]);
                return $response->json();
            }

            Log::error('Failed to create Canvas submission', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Canvas API error creating submission', [
                'error' => $e->getMessage(),
                'course_id' => $courseId,
                'assignment_id' => $assignmentId,
                'user_id' => $userId
            ]);
            return null;
        }
    }

    /**
     * Grade a submission
     */
    public function gradeSubmission(int $courseId, int $assignmentId, int $userId, float $score, string $comment = null): ?array
    {
        $url = "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}";
        
        $data = [
            'submission' => [
                'posted_grade' => $score,
            ]
        ];

        // Add comment if provided
        if ($comment) {
            $data['comment'] = [
                'text_comment' => $comment
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ])->put($url, $data);

            if ($response->successful()) {
                Log::info('Canvas submission graded successfully', [
                    'course_id' => $courseId,
                    'assignment_id' => $assignmentId,
                    'user_id' => $userId,
                    'score' => $score
                ]);
                return $response->json();
            }

            Log::error('Failed to grade Canvas submission', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Canvas API error grading submission', [
                'error' => $e->getMessage(),
                'course_id' => $courseId,
                'assignment_id' => $assignmentId,
                'user_id' => $userId,
                'score' => $score
            ]);
            return null;
        }
    }

    /**
     * Get existing submission
     */
    public function getSubmission(int $courseId, int $assignmentId, int $userId): ?array
    {
        $url = "{$this->baseUrl}/api/v1/courses/{$courseId}/assignments/{$assignmentId}/submissions/{$userId}";
        
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
            ])->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Canvas API error getting submission', [
                'error' => $e->getMessage(),
                'course_id' => $courseId,
                'assignment_id' => $assignmentId,
                'user_id' => $userId
            ]);
            return null;
        }
    }

    /**
     * Create submission and grade it in one step
     */
    public function submitAndGrade(int $courseId, int $assignmentId, int $userId, float $score, string $comment = null, array $submissionData = []): bool
    {
        // First, check if submission already exists
        $existingSubmission = $this->getSubmission($courseId, $assignmentId, $userId);
        
        // If no submission exists, create one
        if (!$existingSubmission || $existingSubmission['workflow_state'] === 'unsubmitted') {
            $submission = $this->createSubmission($courseId, $assignmentId, $userId, $submissionData);
            if (!$submission) {
                return false;
            }
        }

        // Grade the submission
        $gradeResult = $this->gradeSubmission($courseId, $assignmentId, $userId, $score, $comment);
        
        return $gradeResult !== null;
    }
}
