<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\AssignmentStatus;
use App\Models\Assignment;
use App\Models\User;
use LibDNS\Records\Question;

final readonly class SubmitAndGradeAction
{
    // TODO: Link to QuestionGradeAction
    public function handle(User $user, Assignment $assignment, array $questions)
    {
        // Set the assignmentstatus of this user for this assignment to submitted
        $assignment->assignmentStatuses()->updateOrCreate(
            ['user_id' => $user->id],
            ['status' => AssignmentStatus::SUBMITTED]
        );

        // Launch job to grade all questions


        // When job comes back set status to graded
    }
}