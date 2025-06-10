<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'test' => 'array',
        'questions' => 'array',
    ];

    /**
     * Get the language that owns the assignment.
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the group that owns the assignment.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
