<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentDashboardController extends Controller
{
    /**
     * Display the student dashboard with all required data
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        // Get student's groups
        $groups = $user->groups()->get();
        $groupIds = $groups->pluck('id');
        
        // Get all assignments from student's groups
        $assignments = Assignment::whereIn('group_id', $groupIds)
            ->with([
                'questions.submissions' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                },
                'questions.submissions.user',
                'group',
                'user', // Teacher who created the assignment
                'questions' => function ($query) {
                    $query->select('id', 'assignment_id', 'language', 'level', 'estimated_answer_duration');
                }
            ])
            ->get()
            ->map(function ($assignment) use ($user, $groupIds) {
                // Get student's submissions for this assignment
                $studentSubmissions = $assignment->questions->flatMap->submissions
                    ->where('user_id', $user->id);
                
                // Calculate student's score for this assignment
                $totalQuestions = $assignment->questions->count();
                $correctAnswers = $studentSubmissions->where('is_correct', true)->count();
                $studentScore = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
                
                // Get all submissions from fellow students for this assignment
                $allSubmissions = $assignment->questions->flatMap->submissions;
                $fellowStudentScores = $allSubmissions
                    ->where('user_id', '!=', $user->id)
                    ->groupBy('user_id')
                    ->map(function ($userSubmissions) use ($totalQuestions) {
                        $userCorrect = $userSubmissions->where('is_correct', true)->count();
                        return $totalQuestions > 0 ? ($userCorrect / $totalQuestions) * 100 : 0;
                    })
                    ->values();
                
                // Calculate average score of fellow students
                $fellowStudentAverage = $fellowStudentScores->isEmpty() ? 0 : $fellowStudentScores->avg();
                
                // Get assignment details
                $averageDifficulty = $assignment->questions->avg('level') ?? 0;
                $languages = $assignment->questions->pluck('language')->unique()->values();
                $estimatedTime = $assignment->questions->sum('estimated_answer_duration');
                
                // Check if student has submitted
                $hasSubmitted = $studentSubmissions->count() > 0;
                $submittedAt = $hasSubmitted ? $studentSubmissions->max('created_at') : null;
                
                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'published_at' => $assignment->published_at,
                    'deadline_at' => $assignment->deadline_at,
                    'group_name' => $assignment->group->name,
                    'teacher_name' => $assignment->user->name,
                    'difficulty' => round($averageDifficulty, 1),
                    'languages' => $languages,
                    'estimated_time' => $estimatedTime,
                    'questions_count' => $totalQuestions,
                    'student_score' => round($studentScore, 1),
                    'fellow_students_average' => round($fellowStudentAverage, 1),
                    'has_submitted' => $hasSubmitted,
                    'submitted_at' => $submittedAt,
                    'is_overdue' => $assignment->deadline_at && $assignment->deadline_at->isPast(),
                ];
            });

        // Get fellow students from all groups
        $fellowStudents = collect();
        foreach ($groups as $group) {
            $groupStudents = $group->students()
                ->where('users.id', '!=', $user->id)
                ->with('user')
                ->get();
            $fellowStudents = $fellowStudents->merge($groupStudents);
        }
        $fellowStudents = $fellowStudents->unique('id');

        return Inertia::render('StudentDashboard', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'type' => $user->type,
            ],
            'assignments' => $assignments,
            'groups' => $groups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                ];
            }),
            'fellow_students' => $fellowStudents->map(function ($student) {
                return [
                    'id' => $student->user->id,
                    'name' => $student->user->name,
                    'email' => $student->user->email,
                ];
            })->values(),
            'statistics' => [
                'total_assignments' => $assignments->count(),
                'completed_assignments' => $assignments->where('has_submitted', true)->count(),
                'average_score' => $assignments->where('has_submitted', true)->avg('student_score') ?? 0,
                'overdue_assignments' => $assignments->where('is_overdue', true)->where('has_submitted', false)->count(),
            ],
        ]);
    }
}
