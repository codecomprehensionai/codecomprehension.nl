<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class LtiAssignmentData extends Data
{
    public function __construct(
        #[Required]
        public string $ltiId,
        #[Required]
        public string $title,
        public ?string $description,
    ) {}

    public static function fromJwt(object $payload): self
    {
        return new self(
            ltiId: $payload->{'https://purl.imsglobal.org/spec/lti/claim/resource_link'}->id,
            title: $payload->{'https://purl.imsglobal.org/spec/lti/claim/resource_link'}->title,
            description: $payload->{'https://purl.imsglobal.org/spec/lti/claim/resource_link'}->description,
        );
    }
}
