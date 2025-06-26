<?php

namespace App\Jobs;

use App\Actions\CanvasGenerateTokenAction;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncAssignmentToCanvasJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected Assignment $assignment, protected User $user) {}

    public function handle(): void
    {
        $score = Submission::where('user_id', $this->user->id)
            ->whereIn('question_id', $this->assignment->questions->pluck('id'))
            ->sum('score');

        $scoreMax = $this->assignment->questions->sum('score_max');

        $submissionId = Str::uuid();

        $data = [
            'timestamp'                                     => now()->toIso8601String(),
            'userId'                                        => $this->user->lti_id,
            'scoreGiven'                                    => $score,
            'scoreMaximum'                                  => $scoreMax,
            'activityProgress'                              => 'Completed',
            'gradingProgress'                               => 'FullyGraded',
            'comment'                                       => 'Graded by CodeComprehension',
            'https://canvas.instructure.com/lti/submission' => [
                'new_submission'            => true,
                'submission_type'           => 'basic_lti_launch',
                'submission_data'           => config('services.canvas.endpoint') . '/launch?lti_submission_id=' . $submissionId,
                'prioritize_non_tool_grade' => true,
            ],
        ];

        $token = app(CanvasGenerateTokenAction::class)->handle();

        Http::withToken($token)
            ->post(sprintf('%s/scores', $this->assignment->lti_lineitem_endpoint), $data)
            ->throw();
    }
}
