<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class LanguageController extends Controller
{
    /**
     * Get all languages with their assignments
     */
    public function index(): JsonResponse
    {
        $languages = Language::with('assignments')->get();

        return response()->json([
            'success' => true,
            'data' => $languages
        ]);
    }

    /**
     * Get a specific language by ID
     */
    public function show($id): JsonResponse
    {
        $language = Language::with('assignments')->find($id);

        if (!$language) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $language
        ]);
    }

    /**
     * Create a new language
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'language_name' => 'required|string|max:255'
            ]);

            $language = Language::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Language created successfully',
                'data' => $language->load('assignments')
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
     * Update an existing language
     */
    public function update(Request $request, $id): JsonResponse
    {
        $language = Language::find($id);

        if (!$language) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'language_name' => 'sometimes|required|string|max:255'
            ]);

            $language->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Language updated successfully',
                'data' => $language->load('assignments')
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
     * Delete a language
     */
    public function destroy($id): JsonResponse
    {
        $language = Language::find($id);

        if (!$language) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found'
            ], 404);
        }

        $language->delete();

        return response()->json([
            'success' => true,
            'message' => 'Language deleted successfully'
        ]);
    }

    /**
     * Get assignments for a specific language
     */
    public function assignments($id): JsonResponse
    {
        $language = Language::with('assignments')->find($id);

        if (!$language) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $language->assignments
        ]);
    }
}
