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
            'course'     => $course,
            'assignment' => $assignment,
        ]);
    }

    public function updateAssignmentQuestions(Request $request, $assignmentId)
    {
        $validated = $request->validate([
            'title'                                 => 'required|string|max:255',
            'description'                           => 'nullable|string',
            'questions'                             => 'required|array',
            'questions.*.id'                        => 'required|integer|exists:questions,id',
            'questions.*.language'                  => 'required|string',
            'questions.*.type'                      => 'required|string',
            'questions.*.level'                     => 'required|string',
            'questions.*.estimated_answer_duration' => 'required|integer',
            'questions.*.question'                  => 'required|string',
            'questions.*.code'                      => 'nullable|string',
            'questions.*.options'                   => 'nullable|array',
        ]);

        $assignment = Assignment::where('lti_id', $assignmentId)->firstOrFail();
        $assignment->update([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
        ]);

        foreach ($validated['questions'] as $questionData) {
            $question = Question::where('id', $questionData['id'])
                ->where('assignment_id', $assignment->id)
                ->first();

            if (!$question) {
                continue;
            }

            $question->update([
                'language'           => $questionData['language'],
                'type'               => $questionData['type'],
                'level'              => $questionData['level'],
                'estimated_duration' => $questionData['estimated_answer_duration'],
                'question'           => $questionData['question'],
                'code'               => $questionData['code'] ?? null,
                'options'            => $questionData['options'] ?? [],
            ]);
        }

        return redirect()->route('teacher.dashboard')->with('success', 'Assignment updated successfully.');
    }
}
