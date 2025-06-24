<?php

namespace App\Models;

use App\Services\Jwt\JwtService;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

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
     * Ensures that the token:
     * - is issued by this app (issuer = config('app.url'))
     * - has a required subject
     * - has a required audience
     * - has a required expiration
     * - has an optional not-before time
     * - has a custom token jti or an auto-generated one
     * - has optional additional claims
     *
     * @throws InvalidArgumentException if private key is blank
     */
    public function sign(string $sub, string|array $aud, DateTimeInterface $exp, ?DateTimeInterface $nbf = null, ?string $jti = null, ?array $claims = []): string
    {
        return $this->service()->sign($sub, $aud, $exp, $nbf, $jti, $claims);
    }

    /**
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
