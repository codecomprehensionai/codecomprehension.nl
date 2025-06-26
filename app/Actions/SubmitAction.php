<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\AssignmentStatus;
use App\Jobs\CalculateSubmissionScoreJob;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

final readonly class SubmitAction
{
    public function handle(User $user, Assignment $assignment, array $submissions)
    {
        // Set the assignmentstatus of this user for this assignment to submitted
        $assignment->assignmentStatuses()->updateOrCreate(
            ['user_id' => $user->id],
            ['status' => AssignmentStatus::SUBMITTED]
        );

        // Create jobs for grading the submissions
        $jobs = [];
        foreach ($submissions as $submission) {
            $jobs[] = new CalculateSubmissionScoreJob($submission);
        }

        // Batch the jobs and afterwards set assignment status to GRADED
        Bus::batch($jobs)
            ->then(function (Batch $batch) use ($user, $assignment) {
                // When job comes back set status to graded
                $assignment->assignmentStatuses()->updateOrCreate(
                    ['user_id' => $user->id],
                    ['status' => AssignmentStatus::GRADED]
                );
            })
            ->name('Grade all submission')
            ->dispatch();
    }
}
