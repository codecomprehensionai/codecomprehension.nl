<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    /** @use HasFactory<\Database\Factories\GroupFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the assignments for the group.
     *
     * @return HasMany<Assignment, Group>
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get the students in this group.
     *
     * @return BelongsToMany<Student, Group>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'group_students')
            ->using(GroupStudent::class)
            ->withTimestamps();
    }

    /**
     * Get the teachers of this group.
     *
     * @return BelongsToMany<Teacher, Group>
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'group_teachers')
            ->using(GroupTeacher::class)
            ->withTimestamps();
    }
}
