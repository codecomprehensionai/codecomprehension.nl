<?php

/**
 * Data transfer object for representing LTI (Learning Tools Interoperability) course information.
 *
 * This class captures core data associated with an LTI course context as defined by IMS Global standards.
 * It is intended to streamline access to course-level metadata from incoming LTI launch payloads and
 * integrates with the Spatie Laravel Data package for easy validation, casting, and transformation.
 */

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
