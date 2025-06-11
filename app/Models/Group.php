<?php

namespace App\Models;

use App\Enums\UserType;
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
     * Get the users in this group.
     *
     * @return BelongsToMany<User, Group>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_users')
            ->using(GroupUser::class)
            ->withTimestamps();
    }

    /**
     * Get the teachers in this group.
     *
     * @return BelongsToMany<User, Group>
     */
    public function teachers(): BelongsToMany
    {
        // TODO: test
        return $this->users()->where('type', UserType::Teacher);
    }

    /**
     * Get the students in this group.
     *
     * @return BelongsToMany<User, Group>
     */
    public function students(): BelongsToMany
    {
        // TODO: test
        return $this->users()->where('type', UserType::Student);
    }
}
