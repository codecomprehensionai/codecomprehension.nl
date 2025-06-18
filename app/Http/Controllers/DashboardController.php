<?php

namespace App\Http\Controllers;

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
        
        // Get LTI context data from session (set during LTI launch)
        $currentCourse = $this->getCurrentCourseFromSession($request);
        $currentAssignment = $this->getCurrentAssignmentFromSession($request);
        
        return Inertia::render('dashboard', [
            'currentCourse' => $currentCourse,
            'currentAssignment' => $currentAssignment,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'type' => $user->type->value,
            ],
        ]);
    }
  
    /**
     * Get current course data from LTI session context
     */
    private function getCurrentCourseFromSession(Request $request): ?array
    {
        return $request->session()->get('lti.course');
    }
    
    /**
     * Get current assignment data from LTI session context
     */
    private function getCurrentAssignmentFromSession(Request $request): ?array
    {
        return $request->session()->get('lti.assignment');
    }
} 
