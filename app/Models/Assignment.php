<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    /** @use HasFactory<\Database\Factories\AssignmentFactory> */
    use HasFactory;

    /**
     * The course that the assignment belongs to.
     *
     * @return BelongsTo<Course, Assignment>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * The questions that belong to the assignment.
     *
     * @return HasMany<Question, Assignment>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deadline_at'  => 'datetime',
        ];
    }

    protected function estimatedAnswerDuration(): Attribute
    {
        return Attribute::make(
            get: fn(): int => $this->questions
                ->sum('estimated__answer_duration'),
        );
    }

    protected function topics(): Attribute
    {
        return Attribute::make(
            get: fn(): array => $this->questions
                ->pluck('topic')
                ->filter()
                ->unique()
                ->values()
                ->toArray(),
        );
    }

    protected function tags(): Attribute
    {
        return Attribute::make(
            get: fn(): array => $this->questions
                ->pluck('tags')
                ->filter()
                ->flatten()
                ->unique()
                ->values()
                ->toArray(),
        );
    }

    protected function languages(): Attribute
    {
        return Attribute::make(
            get: fn(): array => $this->questions
                ->pluck('language')
                ->filter()
                ->unique()
                ->values()
                ->toArray(),
        );
    }
}
