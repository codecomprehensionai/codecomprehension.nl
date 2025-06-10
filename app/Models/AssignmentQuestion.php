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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'level',
        'type',
        'language',
        'topic',
        'tags',
        'estimated_duration',
        'question',
        'explanation',
        'answer',
        'code',
        'options',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'level' => QuestionLevel::class,
        'type' => QuestionType::class,
        'language' => QuestionLanguage::class,
        'tags' => 'array',
        'options' => 'array',
    ];

    /**
     * Get the group that owns the assignment.
     *
     * @return BelongsTo<Assignment, Question>
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the submissions for this question.
     *
     * @return HasMany<Submission, Question>
     */
    public function submission(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
