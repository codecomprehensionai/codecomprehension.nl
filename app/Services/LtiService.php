<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class LtiService
{
    /**
     * Get user information from LTI context
     */
    public function getUserInfo(?array $ltiContext = null): array
    {
        $context = $ltiContext ?? session('lti_context');

        if (!$context) {
            return [];
        }

        return [
            'id' => $context['sub'] ?? null,
            'name' => $context['name'] ?? $context['given_name'] . ' ' . ($context['family_name'] ?? ''),
            'email' => $context['email'] ?? null,
            'roles' => $this->extractRoles($context),
            'picture' => $context['picture'] ?? null,
        ];
    }

    /**
     * Extract and normalize LTI roles
     */
    public function extractRoles(?array $ltiContext = null): array
    {
        $context = $ltiContext ?? session('lti_context');

        if (!$context) {
            return [];
        }

        $roles = $context['https://purl.imsglobal.org/spec/lti/claim/roles'] ?? [];

        $normalizedRoles = [];
        foreach ($roles as $role) {
            if (str_contains($role, 'Instructor')) {
                $normalizedRoles[] = 'instructor';
                dd($context);
            } elseif (str_contains($role, 'Student')) {
                $normalizedRoles[] = 'student';
            } elseif (str_contains($role, 'Administrator')) {
                $normalizedRoles[] = 'administrator';
            } elseif (str_contains($role, 'TeachingAssistant')) {
                $normalizedRoles[] = 'teaching_assistant';
            }
        }

        return array_unique($normalizedRoles);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role, ?array $ltiContext = null): bool
    {
        return in_array($role, $this->extractRoles($ltiContext));
    }

    /**
     * Check if AGS (Assignment and Grade Services) is supported
     */
    public function supportsAgs(?array $ltiContext = null): bool
    {
        $context = $ltiContext ?? session('lti_context');
        return isset($context['https://purl.imsglobal.org/spec/lti-ags/claim/endpoint']);
    }

    /**
     * Get course/context information
     */
    public function getCourseInfo(?array $ltiContext = null): array
    {
        $context = $ltiContext ?? session('lti_context');

        if (!$context) {
            return [];
        }

        $contextClaim = $context['https://purl.imsglobal.org/spec/lti/claim/context'] ?? [];

        return [
            'id' => $contextClaim['id'] ?? null,
            'label' => $contextClaim['label'] ?? null,
            'title' => $contextClaim['title'] ?? null,
            'type' => $contextClaim['type'] ?? null,
        ];
    }

    /**
     * Get resource link information
     */
    public function getResourceLinkInfo(?array $ltiContext = null): array
    {
        $context = $ltiContext ?? session('lti_context');

        if (!$context) {
            return [];
        }

        $resourceLink = $context['https://purl.imsglobal.org/spec/lti/claim/resource_link'] ?? [];

        return [
            'id' => $resourceLink['id'] ?? null,
            'title' => $resourceLink['title'] ?? null,
            'description' => $resourceLink['description'] ?? null,
        ];
    }


    /**
     * Get AGS endpoints if available
     */
    public function getAgsEndpoints(?array $ltiContext = null): array
    {
        $context = $ltiContext ?? session('lti_context');

        if (!$context || !$this->supportsAgs($context)) {
            return [];
        }

        $agsEndpoint = $context['https://purl.imsglobal.org/spec/lti-ags/claim/endpoint'] ?? [];

        return [
            'lineitem' => $agsEndpoint['lineitem'] ?? null,
            'lineitems' => $agsEndpoint['lineitems'] ?? null,
            'scope' => $agsEndpoint['scope'] ?? [],
        ];
    }

    /**
     * Send a grade back to the LMS (if AGS is supported)
     */
    public function sendGrade(float $score, float $maxScore = 100.0, ?string $comment = null): bool
    {
        if (!$this->supportsAgs()) {
            Log::warning('Attempted to send grade but AGS is not supported in current LTI context');
            return false;
        }

        $endpoints = $this->getAgsEndpoints();
        $lineitemUrl = $endpoints['lineitem'] ?? null;

        if (!$lineitemUrl) {
            Log::error('No lineitem URL available for grade passback');
            return false;
        }

        try {
            // Get access token for AGS
            $accessToken = $this->getAgsAccessToken();
            if (!$accessToken) {
                return false;
            }

            // Prepare grade data
            $gradeData = [
                'scoreGiven' => $score,
                'scoreMaximum' => $maxScore,
                'activityProgress' => 'Completed',
                'gradingProgress' => 'FullyGraded',
                'timestamp' => now()->toISOString(),
                'userId' => session('lti_user_id'),
            ];

            if ($comment) {
                $gradeData['comment'] = $comment;
            }

            // Send grade to LMS
            $response = Http::withToken($accessToken)
                ->post($lineitemUrl . '/scores', $gradeData);

            if ($response->successful()) {
                Log::info('Grade sent successfully', ['score' => $score, 'maxScore' => $maxScore]);
                return true;
            } else {
                Log::error('Failed to send grade', [
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending grade: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get access token for AGS operations
     */
    private function getAgsAccessToken(): ?string
    {
        try {
            $platform = config('lti.platforms.canvas');
            $clientId = $platform['client_id'];
            $tokenUrl = $platform['auth_token_url'];

            // Generate JWT for client credentials
            $privateKeyPath = config('lti.key_chain.private_key_file');
            $privateKey = file_get_contents($privateKeyPath);

            $payload = [
                'iss' => $clientId,
                'sub' => $clientId,
                'aud' => $tokenUrl,
                'iat' => time(),
                'exp' => time() + 300, // 5 minutes
                'jti' => bin2hex(random_bytes(16)),
            ];

            $jwt = JWT::encode($payload, $privateKey, 'RS256');

            // Request access token
            $response = Http::asForm()->post($tokenUrl, [
                'grant_type' => 'client_credentials',
                'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                'client_assertion' => $jwt,
                'scope' => 'https://purl.imsglobal.org/spec/lti-ags/scope/score',
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('Failed to get AGS access token', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while getting AGS access token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate if current user can perform administrative actions
     */
    public function canAdminister(?array $ltiContext = null): bool
    {
        return $this->hasRole('instructor', $ltiContext) ||
            $this->hasRole('administrator', $ltiContext);
    }

    /**
     * Get platform information
     */
    public function getPlatformInfo(?array $ltiContext = null): array
    {
        $context = $ltiContext ?? session('lti_context');

        if (!$context) {
            return [];
        }

        return [
            'issuer' => $context['iss'] ?? null,
            'client_id' => $context['aud'] ?? null,
            'deployment_id' => $context['https://purl.imsglobal.org/spec/lti/claim/deployment_id'] ?? null,
            'version' => $context['https://purl.imsglobal.org/spec/lti/claim/version'] ?? null,
            'message_type' => $context['https://purl.imsglobal.org/spec/lti/claim/message_type'] ?? null,
        ];
    }
}
