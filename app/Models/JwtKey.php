<?php

namespace App\Models;

use App\Services\Jwt\JwtService;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string                          $id
 * @property null|string                     $name
 * @property mixed                           $public_key
 * @property null|mixed                      $private_key
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JwtKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JwtKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JwtKey query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JwtKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JwtKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JwtKey whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JwtKey wherePrivateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JwtKey wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JwtKey whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class JwtKey extends Model
{
    use HasUlids;

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

    /**
     * Ensures that the token:>
     * return $this->service()->sign($sub, $aud, $exp, $nbf, $jti, $claims);
     * }
     * /**
     * Verifies that the token:
     * - is signed by the current key
     * - is issued by the expected issuer (if provided)
     * - is related to the expected subject (if provided)
     * - is intended for this app (audience = config('app.url'))
     * - is valid in time window
     *
     * @throws InvalidArgumentException if public key is blank
     * @throws Exception
     */
    public function verify(string $input, ?string $expectedIss = null, ?string $expectedSub = null): array
    {
        return $this->service()->verify($input, $expectedIss, $expectedSub);
    }

    protected function service(): JwtService
    {
        return new JwtService($this);
    }

    protected function casts(): array
    {
        return [
            'public_key'  => 'encrypted',
            'private_key' => 'encrypted',
        ];
    }
}
