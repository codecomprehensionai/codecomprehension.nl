<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property string $id
 * @property string $lti_id
 * @property string $lti_lineitem_endpoint
 * @property string $course_id
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $deadline_at
 * @property string|null $score_max
 * @property string|null $score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course $course
 * @property-read int $estimated_answer_duration
 * @property-read array $languages
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @property-read array $tags
 * @property-read array $topics
 * @method static \Database\Factories\AssignmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereDeadlineAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereLtiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereLtiLineitemEndpoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereScoreMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Assignment extends Model
{
    /** @use HasFactory<\Database\Factories\AssignmentFactory> */
    use HasFactory;

    use HasUlids;

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
            'deadline_at' => 'datetime',
        ];
    }

    protected function estimatedAnswerDuration(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->questions
                ->sum('estimated__answer_duration'),
        );
    }

    protected function topics(): Attribute
    {
        return Attribute::make(
            get: fn (): array => $this->questions
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
            get: fn (): array => $this->questions
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
            get: fn (): array => $this->questions
                ->pluck('language')
                ->filter()
                ->unique()
                ->values()
                ->toArray(),
        );
    }
}
