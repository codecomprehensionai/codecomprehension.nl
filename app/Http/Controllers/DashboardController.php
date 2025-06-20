<?php

namespace App\Http\Controllers;

use App\Models\Assignment;

class DashboardController
{
    public function student(Assignment $assignment)
    {
        return 'student, assignment: ' . $assignment->title;
    }

    public function teacher(Assignment $assignment)
    {
        return 'teacher, assignment: ' . $assignment->title;
    }
}
