<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\AssignmentStatus;
use App\Jobs\CalculateSubmissionScoreJob;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;


/* This action makes sure that every submission get's graded. Afterwards it sets
 * the database entry for this assignment and user to GRADED. During this 
 * process the assignment status is SUBMITTED.
 */
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
                // Set AssignmentStatus to graded in DB
                $assignment->assignmentStatuses()->updateOrCreate(
                    ['user_id' => $user->id],
                    ['status' => AssignmentStatus::GRADED]
                );
            })
            ->name('Grade all submission')
            ->dispatch();
    }
}
