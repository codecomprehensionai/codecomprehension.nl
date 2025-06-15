<?php

namespace App\Data;

use App\Enums\UserType;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Data;

class LtiUserData extends Data
{
    public function __construct(
        #[Required]
        public string $ltiId,
        #[Required]
        public UserType $type,
        #[Required]
        public string $name,
        #[Required, Email]
        public string $email,
        #[Url]
        public ?string $avatarUrl,
        public ?string $locale,
    ) {}

    public static function fromJwt(object $payload): self
    {
        return new self(
            ltiId: $payload->{'https://purl.imsglobal.org/spec/lti/claim/lti1p1'}->user_id,
            type: UserType::fromRoles($payload->{'https://purl.imsglobal.org/spec/lti/claim/roles'}),
            name: $payload->name,
            email: $payload->email,
            avatarUrl: $payload->picture ?? null,
            locale: $payload->locale ?? null,
        );
    }
}
