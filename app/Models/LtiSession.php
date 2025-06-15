<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LtiSession extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'iss',
        'login_hint',
        'message_hint',
        'target_link_uri',
        'client_id',
        'deployment_id',
        'canvas_region',
        'canvas_environment',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $ltiSession) {
            $ltiSession->state = Str::ulid();
            $ltiSession->nonce = Str::ulid();
            $ltiSession->expires_at = now()->addMinutes(10);
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at'    => 'datetime',
        ];
    }
}
