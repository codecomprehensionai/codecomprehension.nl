<?php

/**
 * Enum representing user types within an LTI context.
 *
 * Distinguishes between Student and Teacher roles using semantic LTI role URIs.
 * Includes a helper method to determine the correct UserType
 * based on an array of LTI-defined roles from a launch payload.
 */

namespace App\Enums;

enum UserType: string
{
    case Student = 'student';
    case Teacher = 'teacher';

    public static function fromRoles(array $roles = []): self
    {
        $teacherRoles = [
            'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
            'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Instructor',
        ];

        foreach ($roles as $role) {
            if (in_array($role, $teacherRoles, true)) {
                return self::Teacher;
            }
        }

        return self::Student;
    }
}
