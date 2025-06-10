<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentQuestionSubmission extends Model
{
    /** @use HasFactory<\Database\Factories\AssignmentQuestionSubmissionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'answer',
        'feedback',
        'is_correct',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * Get the group that owns the assignment.
     *
     * @return BelongsTo<AssignmentQuestion, AssignmentQuestionSubmission>
     */
    public function assignmentQuestion(): BelongsTo
    {
        return $this->belongsTo(AssignmentQuestion::class);
    }

    /**
     * Get the student who submitted the question.
     *
     * @return BelongsTo<Student, AssignmentQuestionSubmission>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
