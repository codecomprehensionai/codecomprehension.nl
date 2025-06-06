<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assignments';

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
        'id' => 'integer',
        'title' => 'string',
        'level' => 'integer',
        'due_date' => 'datetime',
        'estimated_time' => 'integer',
        'test' => 'array',
        'language_id' => 'integer',
        'questions' => 'array',
        'group_id' => 'integer',
    ];

    /**
     * Get the language that owns the assignment.
     */
    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    /**
     * Get the group that owns the assignment.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
