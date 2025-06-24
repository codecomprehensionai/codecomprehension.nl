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
        $data = [
            'timestamp'                                     => now()->toIso8601String(),
            'userId'                                        => $this->submission->user->lti_id,
            'scoreGiven'                                    => $this->submission->score,
            'scoreMaximum'                                  => $this->submission->score_max,
            'activityProgress'                              => 'Completed',
            'gradingProgress'                               => 'FullyGraded',
            'comment'                                       => 'Graded by CodeComprehension',
            'https://canvas.instructure.com/lti/submission' => [
                'new_submission'            => $this->submission->updated_at->is($this->submission->created_at),
                'submission_type'           => 'basic_lti_launch',
                'submission_data'           => config('services.canvas.endpoint') . '/launch?lti_submission_id=' . $this->submission->lti_id,
                'prioritize_non_tool_grade' => true,
            ],
        ];

        // TODO: properly inject service class
        $token = CanvasTokenService::get();

        Http::withToken($token)
            ->post($this->submission->assignment->lti_lineitem_endpoint, $data)
            ->throw();
    }
}
