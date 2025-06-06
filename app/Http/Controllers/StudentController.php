<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    /**
     * Get all students with their groups and submissions
     */
    public function index(): JsonResponse
    {
        $students = Student::with(['user', 'groups', 'submissions'])->get();

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    /**
     * Get a specific student by user ID
     */
    public function show($id): JsonResponse
    {
        $student = Student::with(['user', 'groups', 'submissions'])->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    /**
     * Create a new student
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id|unique:students,user_id'
            ]);

            $student = Student::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Student created successfully',
                'data' => $student->load(['user', 'groups', 'submissions'])
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
     * Delete a student
     */
    public function destroy($id): JsonResponse
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully'
        ]);
    }

    /**
     * Get groups for a specific student
     */
    public function groups($id): JsonResponse
    {
        $student = Student::with('groups')->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student->groups
        ]);
    }

    /**
     * Get submissions for a specific student
     */
    public function submissions($id): JsonResponse
    {
        $student = Student::with(['submissions.teacher'])->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student->submissions
        ]);
    }
}
