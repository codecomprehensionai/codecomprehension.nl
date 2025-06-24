<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string                                                              $id
 * @property string                                                              $lti_id
 * @property string                                                              $lti_lineitem_endpoint
 * @property string                                                              $course_id
 * @property string                                                              $title
 * @property null|string                                                         $description
 * @property null|\Illuminate\Support\Carbon                                     $deadline_at
 * @property null|string                                                         $score_max
 * @property null|string                                                         $score
 * @property null|\Illuminate\Support\Carbon                                     $created_at
 * @property null|\Illuminate\Support\Carbon                                     $updated_at
 * @property \App\Models\Course                                                  $course
 * @property int                                                                 $estimated_answer_duration
 * @property array                                                               $languages
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property null|int                                                            $questions_count
 * @property array                                                               $tags
 * @property array                                                               $topics
 *
 * @method static \Database\Factories\AssignmentFactory                    factory($count = null, $state = [])
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
 *
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
