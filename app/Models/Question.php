<?php

namespace App\Models;

use App\Enums\QuestionLanguage;
use App\Enums\QuestionLevel;
use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (Question $question) {
            QuestionLLMGenerateJob::dispatch($question, 'prompt');
        });
    }

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
            'options'  => 'array',
        ];
    }
}
