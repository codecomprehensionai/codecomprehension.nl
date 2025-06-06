<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class GroupController extends Controller
{
    /**
     * Get all groups with their assignments, students, and teachers
     */
    public function index(): JsonResponse
    {
        $groups = Group::with(['assignments', 'students', 'teachers'])->get();

        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
    }

    /**
     * Get a specific group by ID
     */
    public function show($id): JsonResponse
    {
        $group = Group::with(['assignments', 'students', 'teachers'])->find($id);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $group
        ]);
    }

    /**
     * Create a new group
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'group_name' => 'required|string|max:255'
            ]);

            $group = Group::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Group created successfully',
                'data' => $group->load(['assignments', 'students', 'teachers'])
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
     * Update an existing group
     */
    public function update(Request $request, $id): JsonResponse
    {
        $group = Group::find($id);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'group_name' => 'sometimes|required|string|max:255'
            ]);

            $group->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Group updated successfully',
                'data' => $group->load(['assignments', 'students', 'teachers'])
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Delete a group
     */
    public function destroy($id): JsonResponse
    {
        $group = Group::find($id);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        }

        $group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Group deleted successfully'
        ]);
    }

    /**
     * Get assignments for a specific group
     */
    public function assignments($id): JsonResponse
    {
        $group = Group::with('assignments')->find($id);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $group->assignments
        ]);
    }

    /**
     * Get students in a specific group
     */
    public function students($id): JsonResponse
    {
        $group = Group::with(['students.user'])->find($id);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $group->students
        ]);
    }

    /**
     * Get teachers for a specific group
     */
    public function teachers($id): JsonResponse
    {
        $group = Group::with(['teachers.user'])->find($id);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $group->teachers
        ]);
    }
}
