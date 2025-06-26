<?php

/**
 * This service handles JSON Web Token (JWT) generation and validation.
 * It uses an ECDSA key pair to sign and verify tokens. The service
 * provides methods to create new key pairs, sign tokens with
 * specific claims, and verify the authenticity and validity of
 * incoming tokens.
 */

namespace App\Services\Jwt;

use App\Models\JwtKey;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\Validator;

class JwtService
{
    private Signer $signer;

    public function __construct(protected JwtKey $key)
    {
        $this->signer = new Sha256;
    }

    /**
     * @throws Exception
     */
    public static function generateKeyPair(): array
    {
        $config = [
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name'       => 'prime256v1',
            'private_key_bits' => 384,
        ];

        $resource = openssl_pkey_new($config);
        if (!$resource || !openssl_pkey_export($resource, $privateKey)) {
            throw new Exception('JwtService: unable to generate private key');
        }

        $details = openssl_pkey_get_details($resource);
        if (empty($details['key'])) {
            throw new Exception('JwtService: unable to extract public key');
        }

        return [
            'private_key' => $privateKey,
            'public_key'  => $details['key'],
        ];
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
        if (blank($this->key->private_key)) {
            throw new InvalidArgumentException('JwtService: private key is required for signing');
        }

        $privateKey = InMemory::plainText($this->key->private_key);

        if (is_string($aud)) {
            $aud = [$aud];
        }

        $builder = new Builder(new JoseEncoder, ChainedFormatter::withUnixTimestampDates())
            ->withHeader('kid', $this->key->id)
            ->issuedBy(config('app.url'))
            ->relatedTo($sub)
            ->permittedFor(...$aud)
            ->expiresAt(DateTimeImmutable::createFromInterface($exp))
            ->canOnlyBeUsedAfter($nbf ? DateTimeImmutable::createFromInterface($nbf) : new DateTimeImmutable)
            ->issuedAt(new DateTimeImmutable)
            ->identifiedBy($jti ?? Str::lower(Str::ulid()));

        foreach ($claims as $key => $value) {
            $builder = $builder->withClaim($key, $value);
        }

        return $builder->getToken($this->signer, $privateKey)->toString();
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
        if (blank($this->key->public_key)) {
            throw new InvalidArgumentException('JwtService: public key is required for verification');
        }

        $publicKey = InMemory::plainText($this->key->public_key);
        $token = new Parser(new JoseEncoder)->parse($input);

        $validator = new Validator;

        if (!$validator->validate($token, new SignedWith($this->signer, $publicKey))) {
            throw new Exception('JwtService: invalid signature');
        }

        if ($expectedIss && !$validator->validate($token, new IssuedBy($expectedIss))) {
            throw new Exception('JwtService: invalid issuer');
        }

        if ($expectedSub && !$validator->validate($token, new RelatedTo($expectedSub))) {
            throw new Exception('JwtService: invalid subject');
        }

        if (!$validator->validate($token, new PermittedFor(config('app.url')))) {
            throw new Exception('JwtService: invalid audience');
        }

        if (!$validator->validate($token, new StrictValidAt(new JwtClock, DateInterval::createFromDateString('60 seconds')))) {
            throw new Exception('JwtService: token is not valid at this time');
        }

        return $token->claims()->all();
    }
}
