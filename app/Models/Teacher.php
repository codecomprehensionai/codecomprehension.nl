<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
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
     * Get the user that owns the teacher.
     *
     * @return BelongsTo<User, Teacher>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the groups that this teacher teaches.
     *
     * @return BelongsToMany<Group, Teacher>
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'teacher_of', 'teacher_id', 'group_id');
    }

    /**
     * Get the submissions graded by this teacher.
     *
     * @return HasMany<Submission, Teacher>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
