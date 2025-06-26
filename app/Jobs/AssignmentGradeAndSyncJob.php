<?php

namespace App\Jobs;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;

class AssignmentGradeAndSyncJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    // TODO: max execution time 3 minutes

    public function __construct(protected Assignment $assignment, protected Collection $submissions) {}

    public function handle(): void
    {
        $assignment = $this->assignment;
        $submissions = $this->submissions;
        $user = $submissions->first()->user;

        Bus::batch($submissions->map(fn (Submission $submission) => new SubmissionGradeJob($submission)))
            ->then(function () use ($assignment, $user) {
                dispatch(new SyncAssignmentToCanvasJob($assignment, $user));
            })
            ->dispatch();
    }
}
