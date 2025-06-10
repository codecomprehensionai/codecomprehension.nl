<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentGroup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'group_id',
    ];

    /**
     * Get the student that belongs to the group.
     *
     * @return BelongsTo<Student, StudentGroup>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'user_id');
    }

    /**
     * Get the group that the student belongs to.
     *
     * @return BelongsTo<Group, StudentGroup>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
