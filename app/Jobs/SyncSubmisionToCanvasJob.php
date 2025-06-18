<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Services\Canvas\CanvasTokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SyncSubmisionToCanvasJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected Submission $submission) {}

    public function handle(): void
    {
        // TODO: calculate score
        $scoreMax = random_int(80, 100);
        $scoreGiven = random_int(0, $scoreMax);

        $payload = [
            'timestamp'                                     => now()->toIso8601String(),
            'userId'                                        => $this->submission->user->lti_id,
            'scoreGiven'                                    => $scoreGiven,
            'scoreMaximum'                                  => $scoreMax,
            'activityProgress'                              => 'Completed',
            'gradingProgress'                               => 'FullyGraded',
            'comment'                                       => 'Graded by CodeComprehension',
            'https://canvas.instructure.com/lti/submission' => [
                'new_submission'            => $this->submission->updated_at->is($this->submission->created_at),
                'submission_type'           => 'basic_lti_launch',
                'submission_data'           => sprintf('%s/launch?lti_submission_id=%s', config('services.canvas.endpoint'), $this->submission->lti_id),
                'prioritize_non_tool_grade' => true,
            ],
        ];

        // TODO: properly inject service class
        $token = CanvasTokenService::get();

        Http::withToken($token)
            ->post($this->submission->assignment->lti_lineitem_endpoint, $payload)
            ->throw();
    }
}
