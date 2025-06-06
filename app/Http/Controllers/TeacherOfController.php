<?php

namespace App\Http\Controllers;

use App\Models\TeacherOf;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TeacherOfController extends Controller
{
    /**
     * Get all teacher-group relationships
     */
    public function index(): JsonResponse
    {
        $teacherOf = TeacherOf::with(['teacher.user', 'group'])->get();

        return response()->json([
            'success' => true,
            'data' => $teacherOf
        ]);
    }

    /**
     * Get a specific teacher-group relationship by ID
     */
    public function show($id): JsonResponse
    {
        $teacherOf = TeacherOf::with(['teacher.user', 'group'])->find($id);

        if (!$teacherOf) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher group relationship not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $teacherOf
        ]);
    }

    /**
     * Assign a teacher to a group
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'teacher_id' => 'required|integer|exists:teachers,user_id',
                'group_id' => 'required|integer|exists:groups,id'
            ]);

            // Check if relationship already exists
            $exists = TeacherOf::where('teacher_id', $validated['teacher_id'])
                               ->where('group_id', $validated['group_id'])
                               ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher is already assigned to this group'
                ], 409);
            }

            $teacherOf = TeacherOf::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Teacher assigned to group successfully',
                'data' => $teacherOf->load(['teacher.user', 'group'])
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
     * Remove a teacher from a group
     */
    public function destroy($id): JsonResponse
    {
        $teacherOf = TeacherOf::find($id);

        if (!$teacherOf) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher group relationship not found'
            ], 404);
        }

        $teacherOf->delete();

        return response()->json([
            'success' => true,
            'message' => 'Teacher removed from group successfully'
        ]);
    }

    /**
     * Remove teacher from group by teacher_id and group_id
     */
    public function removeTeacherFromGroup(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'teacher_id' => 'required|integer|exists:teachers,user_id',
                'group_id' => 'required|integer|exists:groups,id'
            ]);

            $teacherOf = TeacherOf::where('teacher_id', $validated['teacher_id'])
                                  ->where('group_id', $validated['group_id'])
                                  ->first();

            if (!$teacherOf) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher is not assigned to this group'
                ], 404);
            }

            $teacherOf->delete();

            return response()->json([
                'success' => true,
                'message' => 'Teacher removed from group successfully'
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
