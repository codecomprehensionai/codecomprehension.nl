<?php

namespace App\Jobs;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SubmitJob implements ShouldQueue
{
    // TODO: Make this
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, Assignment $assignment, array $submissions)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
    }
}
