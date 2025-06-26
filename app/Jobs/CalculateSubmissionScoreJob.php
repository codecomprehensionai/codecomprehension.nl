<?php

namespace App\Jobs;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateSubmissionScoreJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected Submission $submission) {}

    public function handle(): void
    {
        // TODO: integrate with LLM
        $scoreMax = random_int(80, 100);
        $score = random_int(0, $scoreMax);

        $this->submission->updateQuietly([
            'score' => $score,
        ]);
    }
}
