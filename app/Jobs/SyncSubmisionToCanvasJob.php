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
use Illuminate\Support\Str;

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
            'answer_blob'  => $this->submission->answer_blob,
            'line_item_id' => $this->submission->question->assignment->lti_line_item_id,
            'score_max'    => $this->submission->question->assignment->score_max,
        ];

        $score = app(CanvasAutoGrader::class)->grade($data['answer_blob']);

        $payload = [
            'timestamp'                                     => now()->toIso8601String(),
            'userId'                                        => $this->submission->user->lti_id,
            'scoreGiven'                                    => $score,
            'scoreMaximum'                                  => $data['score_max'],
            'activityProgress'                              => 'Completed',
            'gradingProgress'                               => 'FullyGraded',
            'comment'                                       => 'Graded by CodeComprehension',
            'https://canvas.instructure.com/lti/submission' => [
                'new_submission'            => true,
                'submission_type'           => 'basic_lti_launch',
                'submission_data'           => route('lti.launch', ['attempt' => $this->submission->id]),
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
