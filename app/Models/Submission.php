<?php

namespace App\Models;

use App\Jobs\CalculateSubmissionScoreJob;
use App\Jobs\SyncSubmisionToCanvasJob;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Bus;

/**
 * @property string                          $id
 * @property null|string                     $lti_id
 * @property string                          $question_id
 * @property string                          $user_id
 * @property string                          $answer
 * @property null|string                     $feedback
 * @property bool                            $is_correct
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property \App\Models\Question            $question
 * @property \App\Models\User                $user
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
            Bus::chain([
                new CalculateSubmissionScoreJob($submission),
                new SyncSubmisionToCanvasJob($submission),
            ])->dispatch();
        });

        static::updated(function (self $submission) {
            Bus::chain([
                new CalculateSubmissionScoreJob($submission),
                new SyncSubmisionToCanvasJob($submission),
            ])->dispatch();
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }
}
