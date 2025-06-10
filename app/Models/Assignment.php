<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'level',
        'due_date',
        'estimated_time',
        'test',
        'language_id',
        'questions',
        'group_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'test'      => 'array',
        'questions' => 'array',
    ];

    /**
     * Get the language that owns the assignment.
     *
     * @return BelongsTo<Language, Assignment>
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the group that owns the assignment.
     *
     * @return BelongsTo<Group, Assignment>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
