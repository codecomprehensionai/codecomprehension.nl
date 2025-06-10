<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
    ];

    /**
     * Get the user that owns the student.
     *
     * @return BelongsTo<User, Student>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the groups this student belongs to.
     *
     * @return BelongsToMany<Group, Student>
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_students')
            ->using(GroupStudent::class)
            ->withTimestamps();
    }

    /**
     * Get the submissions made by this student.
     *
     * @return HasMany<Submission, Student>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
