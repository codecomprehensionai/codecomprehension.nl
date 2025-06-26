<?php

namespace App\Models;

use App\Enums\AssignmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAssignmentStatus extends Model
{
    /*
     * To check if a user has submitted a whole assignment, we keep track of
     * this using the AssignmentStatus enum. For every user and assignment combo
     * the AssignmentStatus is kept track of.
     */

    /**
     * The user that the AssignmentStatus belongs to.
     *
     * @return BelongsTo<User, UserAssignmentStatus>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The assignment that AssignmentStatus belongs to.
     *
     * @return BelongsTo<Assignment, UserAssignmentStatus>
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    protected function casts(): array
    {
        return [
            'assignment_status' => AssignmentStatus::class,
        ];
    }
}
