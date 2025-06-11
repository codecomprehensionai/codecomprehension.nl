<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherDashboardController extends Controller
{
    /**
     * Display the teacher dashboard with all required data
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        // Get teacher's groups with students
        $groups = $user->groups()->with([
            'students' => function ($query) {
                $query->with('user');
            }
        ])->get();
        
        // Get all students from teacher's groups
        $allStudents = $groups->flatMap(function ($group) {
            return $group->students;
        })->unique('id');
        
        // Get assignments created by this teacher with questions and submissions
        $assignments = Assignment::where('user_id', $user->id)
            ->with([
                'questions.submissions.user',
                'group.students.user',
                'questions' => function ($query) {
                    $query->select('id', 'assignment_id', 'language', 'level', 'estimated_answer_duration');
                }
            ])
            ->get()
            ->map(function ($assignment) {
                // Calculate assignment statistics
                $totalStudents = $assignment->group->students->count();
                $submittedStudents = $assignment->questions
                    ->flatMap->submissions
                    ->pluck('user_id')
                    ->unique()
                    ->count();
                
                // Calculate average score
                $submissions = $assignment->questions->flatMap->submissions;
                $averageScore = $submissions->isEmpty() ? 0 : 
                    $submissions->where('is_correct', true)->count() / $submissions->count() * 100;
                
                // Get difficulty (average level of questions)
                $averageDifficulty = $assignment->questions->avg('level') ?? 0;
                
                // Get languages used
                $languages = $assignment->questions->pluck('language')->unique()->values();
                
                // Get estimated time
                $estimatedTime = $assignment->questions->sum('estimated_answer_duration');
                
                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'published_at' => $assignment->published_at,
                    'deadline_at' => $assignment->deadline_at,
                    'group_name' => $assignment->group->name,
                    'difficulty' => round($averageDifficulty, 1),
                    'languages' => $languages,
                    'estimated_time' => $estimatedTime,
                    'total_students' => $totalStudents,
                    'submitted_students' => $submittedStudents,
                    'average_score' => round($averageScore, 1),
                    'questions_count' => $assignment->questions->count(),
                ];
            });

        return Inertia::render('TeacherDashboard', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'type' => $user->type,
            ],
            'students' => $allStudents->map(function ($student) {
                return [
                    'id' => $student->user->id,
                    'name' => $student->user->name,
                    'email' => $student->user->email,
                ];
            })->values(),
            'assignments' => $assignments,
            'groups' => $groups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'students_count' => $group->students->count(),
                ];
            }),
        ]);
    }
}
