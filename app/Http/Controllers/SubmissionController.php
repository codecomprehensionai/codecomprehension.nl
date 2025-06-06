<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SubmissionController extends Controller
{
    /**
     * Get all submissions with student and teacher details
     */
    public function index(Request $request): JsonResponse
    {
        $query = Submission::with(['student.user', 'teacher.user']);

        // Filter by student if provided
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by teacher if provided
        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        $submissions = $query->get();

        return response()->json([
            'success' => true,
            'data' => $submissions
        ]);
    }

    /**
     * Get a specific submission by ID
     */
    public function show($id): JsonResponse
    {
        $submission = Submission::with(['student.user', 'teacher.user'])->find($id);

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Submission not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $submission
        ]);
    }

    /**
     * Create a new submission
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'answer' => 'required|array',
                'correct_answer' => 'required|integer',
                'student_id' => 'required|integer|exists:students,user_id',
                'teacher_id' => 'required|integer|exists:teachers,user_id'
            ]);

            $submission = Submission::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Submission created successfully',
                'data' => $submission->load(['student.user', 'teacher.user'])
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
     * Update an existing submission
     */
    public function update(Request $request, $id): JsonResponse
    {
        $submission = Submission::find($id);

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Submission not found'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'answer' => 'sometimes|required|array',
                'correct_answer' => 'sometimes|required|integer',
                'student_id' => 'sometimes|required|integer|exists:students,user_id',
                'teacher_id' => 'sometimes|required|integer|exists:teachers,user_id'
            ]);

            $submission->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Submission updated successfully',
                'data' => $submission->load(['student.user', 'teacher.user'])
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
     * Delete a submission
     */
    public function destroy($id): JsonResponse
    {
        $submission = Submission::find($id);

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Submission not found'
            ], 404);
        }

        $submission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Submission deleted successfully'
        ]);
    }
}
