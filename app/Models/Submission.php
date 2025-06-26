<?php

namespace App\Models;

use App\Jobs\CalculateSubmissionScoreJob;
use App\Jobs\SyncSubmisionToCanvasJob;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Bus;

class Submission extends Model
{
    /** @use HasFactory<\Database\Factories\SubmissionFactory> */
    use HasFactory;

    use HasUlids;

    protected static function booted(): void
    {
        // TODO: naar kijken
        // static::created(function (self $submission) {
        //     Bus::chain([
        //         new CalculateSubmissionScoreJob($submission),
        //         new SyncSubmisionToCanvasJob($submission),
        //     ])->dispatch();
        // });

        // static::updated(function (self $submission) {
        //     Bus::chain([
        //         new CalculateSubmissionScoreJob($submission),
        //         new SyncSubmisionToCanvasJob($submission),
        //     ])->dispatch();
        // });
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
}
