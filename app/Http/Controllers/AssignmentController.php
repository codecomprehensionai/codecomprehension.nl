<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LtiService;
use App\Models\Assignment;
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    protected $ltiService;

    public function __construct(LtiService $ltiService)
    {
        $this->ltiService = $ltiService;
        $this->middleware('lti');
    }

    /**
     * Display assignments for the current LTI context
     */
    public function index(Request $request)
    {
        $courseInfo = $this->ltiService->getCourseInfo();
        $userInfo = $this->ltiService->getUserInfo();
        $canAdminister = $this->ltiService->canAdminister();

        // Get assignments for this course context
        $assignments = Assignment::where('lti_context_id', $courseInfo['id'] ?? null)
            ->when(!$canAdminister, function ($query) use ($userInfo) {
                // Students only see published assignments
                return $query->where('published', true);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'assignments' => $assignments,
            'course' => $courseInfo,
            'can_administer' => $canAdminister,
            'user' => $userInfo,
        ]);
    }

    /**
     * Create a new assignment (instructors only)
     */
    public function store(Request $request)
    {
        if (!$this->ltiService->canAdminister()) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'language_id' => 'required|exists:languages,id',
            'max_score' => 'numeric|min:0',
            'due_date' => 'nullable|date',
            'published' => 'boolean',
        ]);

        $courseInfo = $this->ltiService->getCourseInfo();
        $userInfo = $this->ltiService->getUserInfo();

        $assignment = Assignment::create([
            'title' => $request->title,
            'description' => $request->description,
            'language_id' => $request->language_id,
            'lti_context_id' => $courseInfo['id'] ?? null,
            'lti_resource_link_id' => $this->ltiService->getResourceLinkInfo()['id'] ?? null,
            'created_by_user_id' => $userInfo['id'] ?? null,
            'max_score' => $request->max_score ?? 100,
            'due_date' => $request->due_date,
            'published' => $request->published ?? false,
        ]);

        return response()->json([
            'success' => true,
            'assignment' => $assignment,
            'message' => 'Assignment created successfully'
        ], 201);
    }

    /**
     * Show assignment details
     */
    public function show(Request $request, $id)
    {
        $courseInfo = $this->ltiService->getCourseInfo();
        $canAdminister = $this->ltiService->canAdminister();

        $assignment = Assignment::where('id', $id)
            ->where('lti_context_id', $courseInfo['id'] ?? null)
            ->first();

        if (!$assignment) {
            return response()->json(['error' => 'Assignment not found'], 404);
        }

        // Check if student can access unpublished assignment
        if (!$assignment->published && !$canAdminister) {
            return response()->json(['error' => 'Assignment not available'], 403);
        }

        return response()->json([
            'assignment' => $assignment,
            'can_administer' => $canAdminister,
            'supports_grade_passback' => $this->ltiService->supportsAgs(),
        ]);
    }

    /**
     * Submit assignment and send grade back to LMS
     */
    public function submit(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string',
            'language' => 'required|string',
        ]);

        $courseInfo = $this->ltiService->getCourseInfo();
        $userInfo = $this->ltiService->getUserInfo();

        $assignment = Assignment::where('id', $id)
            ->where('lti_context_id', $courseInfo['id'] ?? null)
            ->where('published', true)
            ->first();

        if (!$assignment) {
            return response()->json(['error' => 'Assignment not found'], 404);
        }

        // Here you would typically:
        // 1. Save the submission
        // 2. Run code analysis/tests
        // 3. Calculate a score
        // 4. Send grade back to LMS

        // For demonstration, let's simulate a score
        $score = rand(70, 100); // Replace with actual analysis
        $maxScore = $assignment->max_score ?? 100;

        // Send grade back to LMS if AGS is supported
        if ($this->ltiService->supportsAgs()) {
            $gradeSuccess = $this->ltiService->sendGrade(
                $score,
                $maxScore,
                "Code comprehension analysis completed. Score: {$score}/{$maxScore}"
            );

            if ($gradeSuccess) {
                Log::info('Grade sent to LMS', [
                    'assignment_id' => $id,
                    'user_id' => $userInfo['id'],
                    'score' => $score,
                    'max_score' => $maxScore
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'score' => $score,
            'max_score' => $maxScore,
            'grade_sent_to_lms' => $this->ltiService->supportsAgs(),
            'message' => 'Assignment submitted successfully'
        ]);
    }
}
