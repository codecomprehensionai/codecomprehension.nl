<?php

namespace App\Http\Controllers;

use App\Models\StudentGroup;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StudentGroupController extends Controller
{
    /**
     * Get all student-group relationships
     */
    public function index(): JsonResponse
    {
        $studentGroups = StudentGroup::with(['student.user', 'group'])->get();

        return response()->json([
            'success' => true,
            'data' => $studentGroups
        ]);
    }

    /**
     * Get a specific student-group relationship by ID
     */
    public function show($id): JsonResponse
    {
        $studentGroup = StudentGroup::with(['student.user', 'group'])->find($id);

        if (!$studentGroup) {
            return response()->json([
                'success' => false,
                'message' => 'Student group relationship not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $studentGroup
        ]);
    }

    /**
     * Add a student to a group
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|integer|exists:students,user_id',
                'group_id' => 'required|integer|exists:groups,id'
            ]);

            // Check if relationship already exists
            $exists = StudentGroup::where('student_id', $validated['student_id'])
                                  ->where('group_id', $validated['group_id'])
                                  ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is already in this group'
                ], 409);
            }

            $studentGroup = StudentGroup::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Student added to group successfully',
                'data' => $studentGroup->load(['student.user', 'group'])
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
     * Remove a student from a group
     */
    public function destroy($id): JsonResponse
    {
        $studentGroup = StudentGroup::find($id);

        if (!$studentGroup) {
            return response()->json([
                'success' => false,
                'message' => 'Student group relationship not found'
            ], 404);
        }

        $studentGroup->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student removed from group successfully'
        ]);
    }

    /**
     * Remove student from group by student_id and group_id
     */
    public function removeStudentFromGroup(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|integer|exists:students,user_id',
                'group_id' => 'required|integer|exists:groups,id'
            ]);

            $studentGroup = StudentGroup::where('student_id', $validated['student_id'])
                                        ->where('group_id', $validated['group_id'])
                                        ->first();

            if (!$studentGroup) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is not in this group'
                ], 404);
            }

            $studentGroup->delete();

            return response()->json([
                'success' => true,
                'message' => 'Student removed from group successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }
}
