<?php

namespace App\Models;

use App\Jobs\SyncSubmisionToCanvasJob;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasUlids;

    /** @use HasFactory<\Database\Factories\SubmissionFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (self $submission) {
            SyncSubmisionToCanvasJob::dispatch($submission);
        });

        static::updated(function (self $submission) {
            SyncSubmisionToCanvasJob::dispatch($submission);
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
