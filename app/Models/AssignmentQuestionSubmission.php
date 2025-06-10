<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    /** @use HasFactory<\Database\Factories\SubmissionFactory> */
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
     * @return BelongsTo<Question, Submission>
     */
    public function Question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the student who submitted the question.
     *
     * @return BelongsTo<Student, Submission>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
