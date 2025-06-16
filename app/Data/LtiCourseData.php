<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class LtiCourseData extends Data
{
    public function __construct(
        #[Required]
        public string $ltiId,
        #[Required]
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
