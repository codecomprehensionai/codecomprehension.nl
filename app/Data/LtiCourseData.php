<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class LtiCourseData extends Data
{
    public function __construct(
        public string $ltiId,
        public string $title,
    ) {}

    public static function fromJwt(object $payload): self
    {
        return new self(
            ltiId: $payload->{'https://purl.imsglobal.org/spec/lti/claim/context'}->id,
            title: $payload->{'https://purl.imsglobal.org/spec/lti/claim/context'}->title,
        );
    }
}
