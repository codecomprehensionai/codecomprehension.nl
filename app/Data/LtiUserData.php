<?php

/**
 * Data transfer object for representing user information from an LTI (Learning Tools Interoperability) launch.
 *
 * This class encapsulates user attributes provided by LTI-compliant identity claims and maps them into a
 * structured format using the Spatie Laravel Data package. It provides a static constructor that extracts
 * and transforms relevant fields from a decoded LTI JWT payload.
 */

namespace App\Data;

use App\Enums\UserType;
use Spatie\LaravelData\Data;

class LtiUserData extends Data
{
    public function __construct(
        public string $ltiId,
        public UserType $type,
        public string $name,
        public string $email,
        public ?string $avatarUrl,
        public ?string $locale,
    ) {}

    public static function fromJwt(object $payload): self
    {
        return new self(
            ltiId: $payload->sub,
            type: UserType::fromRoles($payload->{'https://purl.imsglobal.org/spec/lti/claim/roles'}),
            name: $payload->name,
            email: $payload->email,
            avatarUrl: $payload->picture ?? null,
            locale: $payload->locale ?? null,
        );
    }
}
