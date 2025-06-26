<?php

namespace App\Models;

use App\Enums\QuestionLanguage;
use App\Enums\QuestionLevel;
use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string                                                                $id
 * @property string                                                                $assignment_id
 * @property QuestionLanguage                                                      $language
 * @property QuestionType                                                          $type
 * @property QuestionLevel                                                         $level
 * @property int                                                                   $estimated_answer_duration
 * @property null|string                                                           $topic
 * @property null|array<array-key, mixed>                                          $tags
 * @property null|string                                                           $question
 * @property null|string                                                           $explanation
 * @property null|string                                                           $code
 * @property null|array<array-key, mixed>                                          $options
 * @property null|string                                                           $answer
 * @property null|\Illuminate\Support\Carbon                                       $created_at
 * @property null|\Illuminate\Support\Carbon                                       $updated_at
 * @property \App\Models\Assignment                                                $assignment
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submissions
 * @property null|int                                                              $submissions_count
 *
 * @method static \Database\Factories\QuestionFactory                    factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereAssignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builtitleder<static>|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEstimatedAnswerDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builtitleder<static>|Question whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereTopic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory;

    use HasUlids;

    /**
     * The assignment that the question belongs to.
     *
     * @return BelongsTo<Assignment, Question>
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * The submissions that belong to the question.
     *
     * @return HasMany<Submission, Question>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'language' => QuestionLanguage::class,
            'type'     => QuestionType::class,
            'level'    => QuestionLevel::class,
            'tags'     => 'array',
        ];
    }
}
