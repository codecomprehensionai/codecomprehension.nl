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
        'target_link_uri',
        'client_id',
        'deployment_id',
        'canvas_region',
        'canvas_environment',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $ltiSession) {
            $ltiSession->state = Str::random();
            $ltiSession->nonce = Str::random();
            $ltiSession->expires_at = now()->addMinutes(10);
        });
    }
}
