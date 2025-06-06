<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get all users with their roles
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['student', 'teacher']);

        // Filter by role if provided
        if ($request->has('role')) {
            if ($request->role === 'student') {
                $query->whereHas('student');
            } elseif ($request->role === 'teacher') {
                $query->whereHas('teacher');
            }
        }

        $users = $query->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Get a specific user by ID
     */
    public function show($id): JsonResponse
    {
        $user = User::with(['student', 'teacher'])->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Create a new user
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'nullable|in:student,teacher'
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $role = $validated['role'] ?? null;
            unset($validated['role']);

            $user = User::create($validated);

            // Create role-specific record if role is specified
            if ($role === 'student') {
                $user->student()->create(['user_id' => $user->id]);
            } elseif ($role === 'teacher') {
                $user->teacher()->create(['user_id' => $user->id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user->load(['student', 'teacher'])
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
     * Update an existing user
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|required|string|min:8'
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user->load(['student', 'teacher'])
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
     * Delete a user
     */
    public function destroy($id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
