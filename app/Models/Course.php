<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    /**
     * The assignments that belong to the course.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}
