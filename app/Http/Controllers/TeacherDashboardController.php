<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Question;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherDashboardController
{

    /**
     * Display the teacher dashboard with all required data
     */
    public function index(Request $request): Response
    {
        $course = session('lti_course');
        $assignmentId = session('lti_assignment')->ltiId;
        $assignment = Assignment::where('lti_id', $assignmentId)->with('questions')->first();

        return Inertia::render('app/DashboardPage', [
            'course' => $course,
            'assignment' => $assignment,
        ]);
    }
}
