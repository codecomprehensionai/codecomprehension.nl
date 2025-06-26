<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\AssignmentStatus;
use App\Jobs\CalculateSubmissionScoreJob;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;

final readonly class SubmitAndGradeAction
{
    // TODO: Link to QuestionGradeAction
    public function handle(User $user, Assignment $assignment, array $submissions)
    {
        // Set the assignmentstatus of this user for this assignment to submitted
        $assignment->assignmentStatuses()->updateOrCreate(
            ['user_id' => $user->id],
            ['status' => AssignmentStatus::SUBMITTED]
        );

        // Launch job that first grades all questions and wait for it 
        $jobs = [];
        foreach ($submissions as $submission) {
            $job = new CalculateSubmissionScoreJob($submission);
            dispatch($job);
            $jobs[] = $job;
        }


        foreach ($jobs as $job) {
            $job->handle();
        }



        // When job comes back set status to graded
        // TODO: Add logic for this code that waits for everything to be graded...
        $assignment->assignmentStatuses()->updateOrCreate(
            ['user_id' => $user->id],
            ['status' => AssignmentStatus::GRADED]
        );
    }
}