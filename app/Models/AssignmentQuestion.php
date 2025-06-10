<?php

namespace App\Models;

use App\Enums\AssignmentQuestionLanguage;
use App\Enums\AssignmentQuestionLevel;
use App\Enums\AssignmentQuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentQuestion extends Model
{
    /** @use HasFactory<\Database\Factories\AssignmentQuestionFactory> */
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
        'level' => AssignmentQuestionLevel::class,
        'type' => AssignmentQuestionType::class,
        'language' => AssignmentQuestionLanguage::class,
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
     * @return HasMany<AssignmentQuestionSubmission, AssignmentQuestion>
     */
    public function submission(): HasMany
    {
        return $this->hasMany(AssignmentQuestionSubmission::class);
    }
}
