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

        //TODO: Multiple choice questions

        // open ended:
        // if quesiton_type code_explanation:

        //llm request:

        //parse response



        $this->submission->updateQuietly([
            'score_max' => $scoreMax,
            'score'     => $score,
        ]);
    }
}
