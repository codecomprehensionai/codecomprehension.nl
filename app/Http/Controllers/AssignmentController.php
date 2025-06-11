<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AssignmentController extends Controller
{
    /**
     * Create a new assignment
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id'             => 'required|integer|unique:assignments,id',
                'title'          => 'required|string',
                'level'          => 'required|integer|min:1',
                'due_date'       => 'required|date',
                'estimated_time' => 'required|integer|min:1',
                'test'           => 'required|array',
                'language_id'    => 'required|integer|exists:languages,id',
                'questions'      => 'required|array',
                'group_id'       => 'nullable|integer|exists:groups,id',
            ]);

            $assignment = Assignment::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Assignment created successfully',
                'data'    => $assignment->load(['language', 'group']),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    /**
     * Get a specific assignment by ID
     */
    public function show($id): JsonResponse
    {
        $assignment = Assignment::with(['language', 'group'])->find($id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $assignment,
        ]);
    }

    /**
     * Update an existing assignment
     */
    public function update(Request $request, $id): JsonResponse
    {
        $assignment = Assignment::find($id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found',
            ], 404);
        }

        try {
            $validated = $request->validate([
                'title'          => 'sometimes|required|string',
                'level'          => 'sometimes|required|integer|min:1',
                'due_date'       => 'sometimes|required|date',
                'estimated_time' => 'sometimes|required|integer|min:1',
                'test'           => 'sometimes|required|array',
                'language_id'    => 'sometimes|required|integer|exists:languages,id',
                'questions'      => 'sometimes|required|array',
                'group_id'       => 'nullable|integer|exists:groups,id',
            ]);

            $assignment->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Assignment updated successfully',
                'data'    => $assignment->load(['language', 'group']),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    /**
     * Delete an assignment
     */
    public function destroy($id): JsonResponse
    {
        $assignment = Assignment::find($id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found',
            ], 404);
        }

        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assignment deleted successfully',
        ]);
    }

    /**
     * Get all assignments or filter by group/language
     */
    public function home(Request $request): JsonResponse
    {
        $query = Assignment::with(['language', 'group']);

        // Filter by group if provided
        if ($request->has('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        // Filter by language if provided
        if ($request->has('language_id')) {
            $query->where('language_id', $request->language_id);
        }

        // Filter by level if provided
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        $assignments = $query->get();

        return response()->json([
            'success' => true,
            'data'    => $assignments,
        ]);
    }
}
