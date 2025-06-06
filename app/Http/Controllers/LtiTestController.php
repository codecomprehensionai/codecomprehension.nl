<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\LtiService;

class LtiTestController extends Controller
{
    protected $ltiService;

    public function __construct(LtiService $ltiService)
    {
        $this->ltiService = $ltiService;
    }

    /**
     * LTI Testing Dashboard
     */
    public function dashboard()
    {
        return view('lti.test-dashboard');
    }

    /**
     * Simulate OIDC initiation for testing
     */
    public function simulateOidc(Request $request)
    {
        // Simulate parameters that Canvas would send
        $params = [
            'iss' => 'https://canvas.test.instructure.com',
            'login_hint' => 'test_user_123',
            'target_link_uri' => url('/lti'),
            'client_id' => config('lti.platforms.canvas.client_id'),
            'lti_message_hint' => 'test_message_hint',
        ];

        // Redirect to OIDC initiation with simulated parameters
        return redirect()->to('/auth/oidc?' . http_build_query($params));
    }

    /**
     * Test the tool interface without LTI context
     */
    public function testTool()
    {
        // Create mock LTI context for testing
        $mockContext = [
            'sub' => 'test_user_123',
            'name' => 'Test User',
            'given_name' => 'Test',
            'family_name' => 'User',
            'email' => 'test@example.com',
            'https://purl.imsglobal.org/spec/lti/claim/roles' => [
                'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor'
            ],
            'https://purl.imsglobal.org/spec/lti/claim/context' => [
                'id' => 'test_course_123',
                'label' => 'TEST101',
                'title' => 'Test Course for LTI Development',
                'type' => ['http://purl.imsglobal.org/vocab/lis/v2/course#CourseOffering']
            ],
            'https://purl.imsglobal.org/spec/lti/claim/tool_platform' => [
                'name' => 'Canvas Test Instance',
                'version' => '1.0.0',
                'guid' => 'canvas.test.instructure.com'
            ],
            'https://purl.imsglobal.org/spec/lti-ags/claim/endpoint' => [
                'scope' => [
                    'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                    'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly'
                ],
                'lineitems' => 'https://canvas.test.instructure.com/api/lti/courses/123/line_items'
            ]
        ];

        // Store mock context in session
        session(['lti_context' => $mockContext]);

        return redirect()->route('lti.tool');
    }

    /**
     * API endpoint to test grade passback
     */
    public function testGradePassback(Request $request)
    {
        try {
            $result = $this->ltiService->sendGrade(
                $request->input('user_id', 'test_user_123'),
                $request->input('score', 85),
                $request->input('max_score', 100),
                $request->input('comment', 'Test grade submission')
            );

            return response()->json([
                'success' => true,
                'message' => 'Grade sent successfully',
                'result' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Grade passback test failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Grade passback failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear test session data
     */
    public function clearSession()
    {
        session()->forget(['lti_context', 'lti_state', 'lti_nonce', 'lti_target_link_uri']);
        return redirect()->route('lti.test.dashboard')->with('success', 'Session cleared');
    }
}
