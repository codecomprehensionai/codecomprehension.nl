<?php

namespace App\Jobs;

use App\Models\Assignment;
use App\Models\User;
use App\Services\Canvas\CanvasTokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncAssignmentToCanvasJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected Assignment $assignment,
        protected User $user
    ) {}

    public function handle(): void
    {
        // Calculate total assignment score for the user
        $scoreData = $this->assignment->calculateTotalScoreForUser($this->user);
        
        // Only sync if the user has answered at least one question
        if ($scoreData['question_count'] === 0) {
            Log::info("No submissions found for user {$this->user->id} in assignment {$this->assignment->id}");
            return;
        }

        // Check if assignment is complete
        $isComplete = $this->assignment->isCompleteForUser($this->user);
        
        // Prepare Canvas API data
        $data = [
            'timestamp' => now()->toIso8601String(),
            'userId' => $this->user->lti_id,
            'scoreGiven' => $scoreData['score'],
            'scoreMaximum' => $scoreData['score_max'],
            'activityProgress' => $isComplete ? 'Completed' : 'InProgress',
            'gradingProgress' => $isComplete ? 'FullyGraded' : 'Pending',
            'comment' => $this->buildComment($scoreData, $isComplete),
            'https://canvas.instructure.com/lti/submission' => [
                'new_submission' => false, // This is an update to existing submission
                'submission_type' => 'basic_lti_launch',
                'submission_data' => config('services.canvas.endpoint') . '/launch?lti_assignment_id=' . $this->assignment->lti_id,
                'prioritize_non_tool_grade' => true,
            ],
        ];

        try {
            // Get Canvas access token
            $token = CanvasTokenService::get();

            // Send grade to Canvas
            $response = Http::withToken($token)
                ->post($this->assignment->lti_lineitem_endpoint, $data)
                ->throw();

            Log::info("Successfully synced assignment {$this->assignment->id} for user {$this->user->id} to Canvas", [
                'score' => $scoreData['score'],
                'max_score' => $scoreData['score_max'],
                'percentage' => $scoreData['percentage'],
                'is_complete' => $isComplete,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to sync assignment {$this->assignment->id} for user {$this->user->id} to Canvas", [
                'error' => $e->getMessage(),
                'score_data' => $scoreData,
            ]);
            
            // Re-throw to trigger job retry
            throw $e;
        }
    }

    /**
     * Build a descriptive comment for Canvas.
     */
    private function buildComment(array $scoreData, bool $isComplete): string
    {
        $comment = "Graded by CodeComprehension - ";
        
        if ($isComplete) {
            $comment .= "Assignment Complete";
        } else {
            $comment .= "Partial Submission";
        }
        
        $comment .= " ({$scoreData['question_count']}/{$scoreData['total_questions']} questions answered)";
        
        if ($scoreData['score_max'] > 0) {
            $comment .= " - {$scoreData['percentage']}%";
        }
        
        return $comment;
    }
} 