<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    /**
     * Display the main dashboard with current course and assignment context
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        // Try to get the most recent course and assignment from database
        // This approach assumes the most recently created course/assignment 
        // for this user is the current context from LTI
        
        $currentCourse = null;
        $currentAssignment = null;
        
        if ($user->type->value === 'student') {
            // For students, get the most recent assignment they have access to
            // Check if user has groups relationship
            if (method_exists($user, 'groups')) {
                $groups = $user->groups()->get();
                $groupIds = $groups->pluck('id');
                
                $assignment = Assignment::whereIn('group_id', $groupIds)
                    ->with('course')
                    ->latest()
                    ->first();
                    
                if ($assignment) {
                    $currentAssignment = $assignment;
                    $currentCourse = $assignment->course;
                }
            } else {
                // Fallback: get the most recent assignment
                $assignment = Assignment::with('course')->latest()->first();
                if ($assignment) {
                    $currentAssignment = $assignment;
                    $currentCourse = $assignment->course;
                }
            }
        } else {
            // For teachers, get their most recent course and assignment
            $currentCourse = Course::latest()->first();
            $currentAssignment = Assignment::where('user_id', $user->id)
                ->with('course')
                ->latest()
                ->first();
                
            if ($currentAssignment) {
                $currentCourse = $currentAssignment->course;
            }
        }
        
        return Inertia::render('dashboard', [
            'currentCourse' => $currentCourse ? [
                'id' => $currentCourse->id,
                'title' => $currentCourse->title,
                'lti_id' => $currentCourse->lti_id ?? null,
            ] : null,
            'currentAssignment' => $currentAssignment ? [
                'id' => $currentAssignment->id,
                'title' => $currentAssignment->title,
                'description' => $currentAssignment->description,
                'lti_id' => $currentAssignment->lti_id ?? null,
                'deadline_at' => $currentAssignment->deadline_at,
            ] : null,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'type' => $user->type->value,
            ],
        ]);
    }
} 