<?php

namespace App\Models;

use App\Jobs\CalculateSubmissionScoreJob;
use App\Jobs\SyncSubmisionToCanvasJob;
use App\Jobs\SyncAssignmentToCanvasJob;
use App\Models\Assignment;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Bus;

/**
 *
 * @method static \Database\Factories\SubmissionFactory                    factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereFeedback($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereIsCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereLtiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Submission extends Model
{
    /** @use HasFactory<\Database\Factories\SubmissionFactory> */
    use HasFactory;

    use HasUlids;

    protected static function booted(): void
    {
        static::created(function (self $submission) {
            if (!$submission->attempt) {
                $maxAttempt = static::where('question_id', $submission->question_id)
                    ->where('user_id', $submission->user_id)
                    ->max('attempt') ?? 0;

                $submission->attempt = $maxAttempt + 1;
            }

            // After scoring, check if we should sync to Canvas
            static::maybeSyncAssignmentToCanvas($submission);
        });
    }

    /**
     * The question that the submission belongs to.
     *
     * @return BelongsTo<Question, Submission>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * The user that the submission belongs to.
     *
     * @return BelongsTo<User, Submission>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assignment this submission belongs to through the question.
     *
     * @return Assignment
     */
    public function assignment(): Assignment
    {
        return $this->question->assignment;
    }

    /**
     * Check if this submission completes the assignment for the user.
     */
    public function completesAssignmentForUser(): bool
    {
        $assignment = $this->assignment();
        return $assignment->isCompleteForUser($this->user);
    }

    /**
     * Scope to get submissions for a specific assignment.
     */
    public function scopeForAssignment($query, Assignment $assignment)
    {
        return $query->whereIn('question_id', $assignment->questions()->pluck('id'));
    }

    /**
     * Scope to get users with their correct answer counts for specific questions
     */
    public function scopeCorrectCountsByUser($query, $questionIds)
    {
        return $query->whereIn('question_id', $questionIds)
            ->where('is_correct', true)
            ->selectRaw('user_id, COUNT(*) as correct_count')
            ->groupBy('user_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'answer' => 'json',
            'is_correct' => 'boolean',
        ];
    }
}
