<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';

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
        'group_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'group_name' => 'string',
    ];

    /**
     * Get the assignments for the group.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'group_id');
    }

    /**
     * Get the students in this group.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_groups', 'group_id', 'student_id');
    }

    /**
     * Get the teachers of this group.
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_of', 'group_id', 'teacher_id');
    }
}
