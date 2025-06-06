<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'submissions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'answer',
        'correct_answer',
        'student_id',
        'teacher_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'answer' => 'array',
        'correct_answer' => 'integer',
        'student_id' => 'integer',
        'teacher_id' => 'integer',
    ];

    /**
     * Get the student that made the submission.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user_id');
    }

    /**
     * Get the teacher that graded the submission.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'user_id');
    }
}
