<?php

namespace App\Jobs;

use App\Actions\SubmissionGradeAction;
use App\Models\Submission;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SubmissionGradeJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    // TODO: max execution time 3 minutes

    public function __construct(protected Submission $submission) {}

    public function handle(): void
    {
        $data = app(SubmissionGradeAction::class)->handle($this->submission);

        $this->submission->updateQuietly([
            'feedback' => $data->feedback,
            'score'    => $data->score,
        ]);
    }
}
