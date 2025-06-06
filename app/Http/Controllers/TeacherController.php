<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TeacherController extends Controller
{
    /**
     * Get all teachers with their groups and submissions
     */
    public function index(): JsonResponse
    {
        $teachers = Teacher::with(['user', 'groups', 'submissions'])->get();

        return response()->json([
            'success' => true,
            'data' => $teachers
        ]);
    }

    /**
     * Get a specific teacher by user ID
     */
    public function show($id): JsonResponse
    {
        $teacher = Teacher::with(['user', 'groups', 'submissions'])->find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $teacher
        ]);
    }

    /**
     * Create a new teacher
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id|unique:teachers,user_id'
            ]);

            $teacher = Teacher::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Teacher created successfully',
                'data' => $teacher->load(['user', 'groups', 'submissions'])
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Delete a teacher
     */
    public function destroy($id): JsonResponse
    {
        $teacher = Teacher::find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }

        $teacher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Teacher deleted successfully'
        ]);
    }

    /**
     * Get groups taught by a specific teacher
     */
    public function groups($id): JsonResponse
    {
        $teacher = Teacher::with('groups')->find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $teacher->groups
        ]);
    }

    /**
     * Get submissions graded by a specific teacher
     */
    public function submissions($id): JsonResponse
    {
        $teacher = Teacher::with(['submissions.student'])->find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $teacher->submissions
        ]);
    }
}
