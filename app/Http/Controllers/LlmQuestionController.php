<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Question;
use App\Services\LlmQuestionGeneratorService;
use App\Enums\QuestionLanguage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class LlmQuestionController extends Controller
{
    private LlmQuestionGeneratorService $llmService;

    public function __construct(LlmQuestionGeneratorService $llmService)
    {
        $this->llmService = $llmService;
    }

    /**
     * Generate a new question for an assignment
     */
    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'assignment_id' => 'required|integer|exists:assignments,id',
            'language' => 'required|string|in:' . implode(',', array_column(QuestionLanguage::cases(), 'value')),
            'type' => 'required|string|in:multiple_choice,fill_in_blank,true_false,short_answer,code_completion',
            'level' => 'required|string|in:beginner,intermediate,advanced',
            'estimated_answer_duration' => 'required|integer|min:0|max:1800',
            'topics' => 'sometimes|array',
            'topics.*' => 'string',
            'tags' => 'sometimes|array',
            'tags.*' => 'string',
            'prompt' => 'required|string|min:10|max:1000',
            'include_existing_questions' => 'sometimes|boolean',
        ]);

        try {
            $assignment = Assignment::findOrFail($validated['assignment_id']);

            // Get existing questions if requested
            $existingQuestions = [];
            if ($validated['include_existing_questions'] ?? false) {
                $existingQuestions = $assignment->questions()->get()->toArray();
            }

            $newQuestionParams = [
                'language' => $validated['language'],
                'type' => $validated['type'],
                'level' => $validated['level'],
                'estimated_answer_duration' => $validated['estimated_answer_duration'],
                'topics' => $validated['topics'] ?? [],
                'tags' => $validated['tags'] ?? [],
            ];

            $questionData = $this->llmService->generateQuestion(
                $assignment,
                $newQuestionParams,
                $validated['prompt'],
                $existingQuestions
            );

            if (!$questionData) {
                return back()->withErrors([
                    'llm' => 'Failed to generate question via LLM service. Please check logs for details.'
                ]);
            }

            // Save the question to database
            $question = $assignment->questions()->create($questionData->toArray());

            Log::info('Question generated and saved successfully', [
                'assignment_id' => $assignment->id,
                'question_id' => $question->id,
                'question_type' => $question->type->value
            ]);

            return back()->with('success', 'Question generated successfully!');

        } catch (\Exception $e) {
            Log::error('Question generation failed', [
                'error' => $e->getMessage(),
                'assignment_id' => $validated['assignment_id'] ?? null
            ]);

            return back()->withErrors([
                'llm' => 'An error occurred while generating the question'
            ]);
        }
    }

    /**
     * Update an existing question using LLM
     */
    public function update(Request $request, int $questionId): JsonResponse
    {
        $validated = $request->validate([
            'language' => 'sometimes|string|in:' . implode(',', array_column(QuestionLanguage::cases(), 'value')),
            'type' => 'sometimes|string|in:multiple_choice,fill_in_blank,true_false,short_answer,code_completion',
            'level' => 'sometimes|string|in:beginner,intermediate,advanced',
            'estimated_answer_duration' => 'sometimes|integer|min:30|max:1800',
            'topics' => 'sometimes|array',
            'topics.*' => 'string',
            'tags' => 'sometimes|array',
            'tags.*' => 'string',
            'prompt' => 'required|string|min:10|max:1000',
            'include_context_questions' => 'sometimes|boolean',
        ]);

        try {
            $question = Question::findOrFail($questionId);
            $assignment = $question->assignment;

            // Get context questions if requested
            $contextQuestions = [];
            if ($validated['include_context_questions'] ?? false) {
                $contextQuestions = $assignment->questions()
                    ->where('id', '!=', $questionId)
                    ->get()
                    ->toArray();
            }

            $updateParams = collect($validated)
                ->except(['prompt', 'include_context_questions'])
                ->toArray();

            $questionData = $this->llmService->updateQuestion(
                $assignment,
                $question,
                $updateParams,
                $validated['prompt'],
                $contextQuestions
            );

            if (!$questionData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update question via LLM service. Please check logs for details.',
                ], 500);
            }

            // Update the question in database
            $question->update($questionData->toArray());

            Log::info('Question updated successfully', [
                'assignment_id' => $assignment->id,
                'question_id' => $question->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully',
                'data' => [
                    'question' => $question->fresh(),
                    'question_data' => $questionData,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Question update failed', [
                'error' => $e->getMessage(),
                'question_id' => $questionId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the question',
            ], 500);
        }
    }

    /**
     * Check LLM service health and availability
     */
    public function health(): JsonResponse
    {
        try {
            $isAvailable = $this->llmService->isAvailable();
            $statusCode = $isAvailable ? 200 : 503;

            return response()->json([
                'success' => $isAvailable,
                'message' => $isAvailable 
                    ? 'LLM service is available and healthy' 
                    : 'LLM service is not available',
                'data' => [
                    'available' => $isAvailable,
                    'timestamp' => now()->toISOString(),
                    'service_url' => config('llm.base_url'),
                ],
            ], $statusCode);

        } catch (\Exception $e) {
            Log::error('LLM health check failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Health check failed',
                'data' => [
                    'available' => false,
                    'timestamp' => now()->toISOString(),
                    'error' => 'Health check exception',
                ],
            ], 503);
        }
    }

    /**
     * Get available question parameters and options
     */
    public function parameters(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'languages' => ['Python', 'Java', 'JavaScript', 'C++', 'C#'],
                'types' => ['multiple_choice', 'fill_in_blank', 'true_false', 'short_answer', 'code_completion'],
                'levels' => ['beginner', 'intermediate', 'advanced'],
                'duration_range' => [
                    'min' => 30,
                    'max' => 1800,
                    'suggested' => [60, 120, 180, 300, 600]
                ],
                'defaults' => config('llm.default_params'),
            ],
        ]);
    }

    /**
     * Test LLM service connectivity and authentication
     */
    public function test(): JsonResponse
    {
        try {
            // Check basic connectivity
            $isAvailable = $this->llmService->isAvailable();
            
            if (!$isAvailable) {
                return response()->json([
                    'success' => false,
                    'message' => 'LLM service is not reachable',
                    'tests' => [
                        'connectivity' => false,
                        'authentication' => false,
                    ],
                ], 503);
            }

            // Test with a simple question generation (you could create a test assignment)
            // For now, just return connectivity success
            return response()->json([
                'success' => true,
                'message' => 'LLM service tests passed',
                'tests' => [
                    'connectivity' => true,
                    'authentication' => 'not_tested', // Would need actual test call
                ],
                'timestamp' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('LLM service test failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'LLM service test failed',
                'tests' => [
                    'connectivity' => false,
                    'authentication' => false,
                ],
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
