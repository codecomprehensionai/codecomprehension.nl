<?php

namespace App\Models;

use App\Services\Jwt\JwtService;
use Illuminate\Database\Eloquent\Model;

class JwtKey extends Model
{
    protected $hidden = [
        'private_key',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $key) {
            if (blank($key->private_key)) {
                $keyPair = $key->service()->generateKeyPair();

                $key->private_key = $keyPair['private_key'];
                $key->public_key = $keyPair['public_key'];
            }
        });
    }

    public function service(): JwtService
    {
        return new JwtService($this);
    }

    protected function casts(): array
    {
        return [
            'private_key' => 'encrypted',
        ];
    }
}
