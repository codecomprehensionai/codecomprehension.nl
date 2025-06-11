<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupUser extends Pivot
{
    /* https://laravel.com/docs/11.x/eloquent-relationships#custom-pivot-models-and-incrementing-ids */
    public $incrementing = true;

    protected $table = 'group_users';

    /**
     * @return BelongsTo<Group, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
