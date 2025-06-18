<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class LtiAssignmentData extends Data
{
    public function __construct(
        public string $ltiId,
        public string $ltiLineItemEndpoint,
        public string $title,
        public ?string $description,
    ) {}

    public static function fromJwt(object $payload): self
    {
        return new self(
            ltiId: $payload->{'https://purl.imsglobal.org/spec/lti/claim/resource_link'}->id,
            ltiLineItemEndpoint: $payload->{'https://purl.imsglobal.org/spec/lti-ags/claim/endpoint'}->lineitem,
            title: $payload->{'https://purl.imsglobal.org/spec/lti/claim/resource_link'}->title,
            description: $payload->{'https://purl.imsglobal.org/spec/lti/claim/resource_link'}->description,
        );
    }
}
