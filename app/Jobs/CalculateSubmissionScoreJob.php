<?php

namespace App\Jobs;

use App\Actions\QuestionGradeAction;
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

    public function __construct(protected Submission $submission)
    {
    }

    public function handle(): void
    {
        try {
            // Get related models
            $question = $this->submission->question;
            $assignment = $question->assignment;

            // Extract answer from submission (handles both string and array formats)
            $submissionAnswer = is_array($this->submission->answer)
                ? ($this->submission->answer['answer'] ?? $this->submission->answer['selected_option'] ?? '')
                : (string) $this->submission->answer;

            // Use AI to grade the submission
            $gradeAction = new QuestionGradeAction();
            $gradeResult = $gradeAction->handle($assignment, $question, $submissionAnswer);

            // Store AI grading results
            $this->submission->updateQuietly([
                'answer' => $gradeResult['answer'],
                'feedback' => $gradeResult['feedback'],
                'score' => $gradeResult['score'],
                'is_correct' => $gradeResult['score'] == $question->score_max,
            ]);

        } catch (\Exception $e) {
            // Fallback if AI grading fails
            $this->submission->updateQuietly([
                'score_max' => $this->submission->question->score_max,
                'score' => 0,
                'feedback' => 'Automatic grading temporarily unavailable.',
                'is_correct' => false,
            ]);
        }
    }
}
