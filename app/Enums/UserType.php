<?php

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
