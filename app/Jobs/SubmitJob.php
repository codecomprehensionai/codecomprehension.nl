<?php

namespace App\Jobs;

use App\Enums\AssignmentStatus;
use App\Models\Assignment;
use App\Models\User;
use Bus;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/* This job makes sure that every submission get's graded. Afterwards it sets
 * the database entry for this assignment and user to GRADED. During this 
 * process the assignment status is SUBMITTED.
 */
class SubmitJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected User $user, protected Assignment $assignment, protected array $submissions)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Set the assignmentstatus of this user for this assignment to submitted
        $this->assignment->assignmentStatuses()->updateOrCreate(
            ['user_id' => $this->user->id],
            ['status' => AssignmentStatus::SUBMITTED]
        );

        // Create jobs for grading the submissions
        $jobs = [];
        foreach ($this->submissions as $submission) {
            $jobs[] = new CalculateSubmissionScoreJob($submission);
        }

        // Batch the jobs and afterwards set assignment status to GRADED
        Bus::batch($jobs)
            ->then(function (Batch $batch) {
                $this->afterGradingCallback();
            })
            ->name('Grade all submission')
            ->dispatch();

    }

    private function afterGradingCallback()
    {
        // Update assignment status to graded
        $this->assignment->assignmentStatuses()->updateOrCreate(
            ['user_id' => $this->user->id],
            ['status' => AssignmentStatus::GRADED]
        );

        // Update send grade to canvas
        SyncAssignmentToCanvasJob::dispatch($this->assignment, $this->user);
    }
}
