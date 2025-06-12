<?php

namespace App\Utility;

class LtiPayloadHelper
{
    public static function extractUserType($payload): string
    {
        $roles = $payload->{'https://purl.imsglobal.org/spec/lti/claim/roles'} ?? [];

        $teacherRoles = [
            'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
            'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Instructor',
        ];

        foreach ($roles as $role) {
            if (in_array($role, $teacherRoles)) {
                return 'teacher';
            }
        }

        return 'student';
    }
}
