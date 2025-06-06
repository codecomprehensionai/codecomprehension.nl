<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGroup extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'student_groups';

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
        'student_id',
        'group_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'student_id' => 'integer',
        'group_id' => 'integer',
    ];

    /**
     * Get the student that belongs to the group.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user_id');
    }

    /**
     * Get the group that the student belongs to.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
