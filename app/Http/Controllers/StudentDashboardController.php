<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentDashboardController
{
    /**
     * Display the student dashboard with all required data
     */
    public function index(Request $request): Response
    {
        $course = session('lti_course');
        $assignmentId = session('lti_assignment')->ltiId;
        $assignment = Assignment::where('lti_id', $assignmentId)
            ->with(['questions', 'questions.submissions'])
            ->first();
        if ($assignment) {
            $assignment->questions->each(function ($question) {
                $question->makeHidden('answer');
            });
        }

        return Inertia::render('app/DashboardPage', [
            'course'     => $course,
            'assignment' => $assignment,
        ]);
    }
}
