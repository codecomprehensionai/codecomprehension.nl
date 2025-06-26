<?php

/**
 * Data object representing an LTI (Learning Tools Interoperability) assignment payload.
 *
 * This class is used to encapsulate assignment-related data received via LTI-compliant
 * launch requests. It is compatible with Spatie Laravel Data for validation, transformation,
 * and serialization. The class also provides a static constructor to extract data directly
 * from a decoded LTI JWT payload.
 */

namespace App\Data;

use Spatie\LaravelData\Data;

class LtiAssignmentData extends Data
{
    public function __construct(
        public string $ltiId,
        public string $ltiLineitemEndpoint,
        public string $title,
        public ?string $description,
    ) {}

    public static function fromJwt(object $payload): self
    {
        return new self(
            ltiId: $payload->{'https://purl.imsglobal.org/spec/lti/claim/resource_link'}->id,
            ltiLineitemEndpoint: $payload->{'https://purl.imsglobal.org/spec/lti-ags/claim/endpoint'}->lineitem,
            title: $payload->{'https://purl.imsglobal.org/spec/lti/claim/resource_link'}->title,
            description: $payload->{'https://purl.imsglobal.org/spec/lti/claim/resource_link'}->description,
        );
    }
}
