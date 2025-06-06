<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\LtiService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\JWK;

class LtiController extends Controller
{
    /**
     * Handle OIDC initiation request
     */
    public function oidcInitiation(Request $request)
    {
        try {
            // Get all Canvas-specific parameters
            $issuer = $request->get('iss');
            $loginHint = $request->get('login_hint');
            $ltiMessageHint = $request->get('lti_message_hint');
            $targetLinkUri = $request->get('target_link_uri');
            $clientId = $request->get('client_id');
            $deploymentId = $request->get('deployment_id');
            $canvasRegion = $request->get('canvas_region');
            $canvasEnvironment = $request->get('canvas_environment');

            // Log all received parameters for debugging
            Log::info('LTI OIDC Initiation Request', [
                'iss' => $issuer,
                'login_hint' => $loginHint,
                'lti_message_hint' => $ltiMessageHint,
                'target_link_uri' => $targetLinkUri,
                'client_id' => $clientId,
                'deployment_id' => $deploymentId,
                'canvas_region' => $canvasRegion,
                'canvas_environment' => $canvasEnvironment,
            ]);

            // Validate required parameters
            if (!$issuer || !$loginHint || !$clientId) {
                Log::error('Missing required OIDC parameters', [
                    'iss' => $issuer,
                    'login_hint' => $loginHint,
                    'client_id' => $clientId,
                ]);
                return response()->json([
                    'error' => 'invalid_request',
                    'error_description' => 'Missing required parameters: iss, login_hint, and client_id are required'
                ], 400);
            }

            // Get platform configuration
            $platforms = config('lti.platforms');
            $platform = null;

            // Find platform by client_id or issuer
            foreach ($platforms as $platformConfig) {
                if (
                    $platformConfig['client_id'] === $clientId ||
                    (isset($platformConfig['issuer']) && $platformConfig['issuer'] === $issuer)
                ) {
                    $platform = $platformConfig;
                    break;
                }
            }

            if (!$platform) {
                Log::error('Unknown LTI platform', [
                    'client_id' => $clientId,
                    'issuer' => $issuer
                ]);
                return response()->json([
                    'error' => 'invalid_client',
                    'error_description' => 'Unknown platform or client_id'
                ], 400);
            }

            // Generate state and nonce for security
            $state = bin2hex(random_bytes(16));
            $nonce = bin2hex(random_bytes(16));

            // Store all relevant data in session for validation in launch
            session([
                'lti_state' => $state,
                'lti_nonce' => $nonce,
                'lti_target_link_uri' => $targetLinkUri,
                'lti_message_hint' => $ltiMessageHint,
                'lti_deployment_id' => $deploymentId,
                'lti_canvas_region' => $canvasRegion,
                'lti_canvas_environment' => $canvasEnvironment,
                'lti_issuer' => $issuer,
                'lti_client_id' => $clientId,
            ]);

            // Determine redirect URI based on the target_link_uri or use default
            $redirectUri = url('/auth/callback');

            // Build authorization URL parameters according to OIDC spec
            $authParams = [
                'response_type' => 'id_token',
                'scope' => 'openid',
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'login_hint' => $loginHint,
                'state' => $state,
                'response_mode' => 'form_post',
                'nonce' => $nonce,
                'prompt' => 'none',
            ];

            // Add lti_message_hint if provided
            if ($ltiMessageHint) {
                $authParams['lti_message_hint'] = $ltiMessageHint;
            }

            $authUrl = $platform['auth_login_url'] . '?' . http_build_query($authParams);

            Log::info('Redirecting to Canvas auth URL', ['url' => $authUrl]);

            return redirect($authUrl);
        } catch (\Exception $e) {
            Log::error('LTI OIDC Initiation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'error' => 'server_error',
                'error_description' => 'Internal server error during OIDC initiation'
            ], 500);
        }
    }

    /**
     * Handle LTI launch request (Step 3: Authentication Response)
     */
    public function launch(Request $request)
    {
        try {
            $idToken = $request->get('id_token');
            $state = $request->get('state');
            $ltiStorageTarget = $request->get('lti_storage_target'); // For Safari cookie workaround

            Log::info('LTI Launch Request', [
                'state' => $state,
                'has_id_token' => !empty($idToken),
                'lti_storage_target' => $ltiStorageTarget,
            ]);

            // Validate state parameter (CSRF protection)
            $expectedState = session('lti_state');
            if ($state !== $expectedState) {
                Log::error('Invalid state parameter', [
                    'received' => $state,
                    'expected' => $expectedState
                ]);
                return view('lti.error', [
                    'error' => 'Invalid State',
                    'message' => 'The launch request contains an invalid state parameter. This may indicate a security issue or expired session.',
                ]);
            }

            if (!$idToken) {
                Log::error('Missing id_token in launch request');
                return view('lti.error', [
                    'error' => 'Missing ID Token',
                    'message' => 'The launch request is missing the required id_token parameter.',
                ]);
            }

            // Decode and validate JWT with Canvas public keys
            $payload = $this->validateJWT($idToken);

            if (!$payload) {
                Log::error('JWT validation failed');
                return view('lti.error', [
                    'error' => 'Invalid JWT Token',
                    'message' => 'The provided JWT token could not be validated.',
                ]);
            }

            // Validate nonce (replay attack protection)
            $expectedNonce = session('lti_nonce');
            if (!isset($payload['nonce']) || $payload['nonce'] !== $expectedNonce) {
                Log::error('Invalid nonce', [
                    'received' => $payload['nonce'] ?? 'missing',
                    'expected' => $expectedNonce
                ]);
                return view('lti.error', [
                    'error' => 'Invalid Nonce',
                    'message' => 'The launch request contains an invalid nonce. This may indicate a security issue.',
                ]);
            }

            // Validate issuer matches expected Canvas issuer
            $expectedIssuer = session('lti_issuer');
            if (!isset($payload['iss']) || $payload['iss'] !== $expectedIssuer) {
                Log::error('Invalid issuer', [
                    'received' => $payload['iss'] ?? 'missing',
                    'expected' => $expectedIssuer
                ]);
                return view('lti.error', [
                    'error' => 'Invalid Issuer',
                    'message' => 'The JWT token was issued by an unexpected platform.',
                ]);
            }

            // Validate audience (client_id)
            $expectedClientId = session('lti_client_id');
            $audience = $payload['aud'] ?? null;
            if ($audience !== $expectedClientId) {
                Log::error('Invalid audience', [
                    'received' => $audience,
                    'expected' => $expectedClientId
                ]);
                return view('lti.error', [
                    'error' => 'Invalid Audience',
                    'message' => 'The JWT token has an invalid audience.',
                ]);
            }

            // Store comprehensive LTI context in session
            session([
                'lti_context' => $payload,
                'lti_user_id' => $payload['sub'] ?? null,
                'lti_context_id' => $payload['https://purl.imsglobal.org/spec/lti/claim/context']['id'] ?? null,
                'lti_resource_link_id' => $payload['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id'] ?? null,
                'lti_deployment_id' => $payload['https://purl.imsglobal.org/spec/lti/claim/deployment_id'] ?? null,
                'lti_message_type' => $payload['https://purl.imsglobal.org/spec/lti/claim/message_type'] ?? null,
                'lti_version' => $payload['https://purl.imsglobal.org/spec/lti/claim/version'] ?? null,
                'lti_roles' => $payload['https://purl.imsglobal.org/spec/lti/claim/roles'] ?? [],
                'lti_storage_target' => $ltiStorageTarget, // Store for Safari compatibility
            ]);

            // Clear temporary session data
            session()->forget([
                'lti_state',
                'lti_nonce',
                'lti_issuer',
                'lti_client_id'
            ]);

            Log::info('LTI Launch successful', [
                'user_id' => $payload['sub'] ?? 'unknown',
                'context_id' => $payload['https://purl.imsglobal.org/spec/lti/claim/context']['id'] ?? 'unknown',
                'message_type' => $payload['https://purl.imsglobal.org/spec/lti/claim/message_type'] ?? 'unknown',
            ]);

            // Redirect to the target URI (Step 4: Resource Display)
            $targetUri = session('lti_target_link_uri', route('lti.tool'));
            session()->forget('lti_target_link_uri');

            return redirect($targetUri);
        } catch (\Exception $e) {
            Log::error('LTI Launch Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return view('lti.error', [
                'error' => 'Launch Error',
                'message' => 'An error occurred during the LTI launch process.',
            ]);
        }
    }

    /**
     * Main tool interface
     */
    public function tool(Request $request)
    {
        $ltiContext = session('lti_context');

        if (!$ltiContext) {
            return response('No LTI context found. Please launch from your LMS.', 403);
        }

        $ltiService = new LtiService();

        // Get structured information using LtiService
        $userInfo = $ltiService->getUserInfo();
        $courseInfo = $ltiService->getCourseInfo();
        $resourceLinkInfo = $ltiService->getResourceLinkInfo();
        $platformInfo = $ltiService->getPlatformInfo();
        $roles = $ltiService->extractRoles();
        $canAdminister = $ltiService->canAdminister();
        $supportsAgs = $ltiService->supportsAgs();

        return view('lti.tool', compact(
            'ltiContext',
            'userInfo',
            'courseInfo',
            'resourceLinkInfo',
            'platformInfo',
            'roles',
            'canAdminister',
            'supportsAgs'
        ));
    }

    /**
     * Provide public JWK set
     */
    public function jwks(Request $request)
    {
        try {
            $publicKeyPath = config('lti.key_chain.public_key_file');

            if (!file_exists($publicKeyPath)) {
                Log::error('Public key file not found', ['path' => $publicKeyPath]);
                return response()->json(['error' => 'Public key not found'], 404);
            }

            $publicKey = file_get_contents($publicKeyPath);
            $keyResource = openssl_pkey_get_public($publicKey);
            $keyDetails = openssl_pkey_get_details($keyResource);

            $jwk = [
                'kty' => 'RSA',
                'use' => 'sig',
                'kid' => 'lti-key-id',
                'n' => rtrim(strtr(base64_encode($keyDetails['rsa']['n']), '+/', '-_'), '='),
                'e' => rtrim(strtr(base64_encode($keyDetails['rsa']['e']), '+/', '-_'), '='),
            ];

            return response()->json([
                'keys' => [$jwk]
            ]);
        } catch (\Exception $e) {
            Log::error('JWK Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Validate JWT token with Canvas public keys
     */
    private function validateJWT($jwt)
    {
        try {
            // Get JWT header to find the key ID
            $parts = explode('.', $jwt);
            if (count($parts) !== 3) {
                Log::error('Invalid JWT format - expected 3 parts');
                return false;
            }

            $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
            if (!$header) {
                Log::error('Invalid JWT header - could not decode');
                return false;
            }

            // Get the issuer from the payload to identify the platform
            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
            if (!$payload || !isset($payload['iss'])) {
                Log::error('Invalid JWT payload or missing issuer');
                return false;
            }

            $issuer = $payload['iss'];
            Log::info('Validating JWT from issuer', ['issuer' => $issuer]);

            // Get platform configuration
            $platforms = config('lti.platforms');
            $platform = null;

            // Find platform by issuer or client_id
            foreach ($platforms as $platformConfig) {
                if (
                    isset($platformConfig['issuer']) && $platformConfig['issuer'] === $issuer ||
                    strpos($issuer, 'canvas') !== false ||
                    (isset($payload['aud']) && $payload['aud'] === $platformConfig['client_id'])
                ) {
                    $platform = $platformConfig;
                    break;
                }
            }

            if (!$platform) {
                Log::error('Platform not found for issuer', ['issuer' => $issuer]);
                return false;
            }

            // Fetch the platform's public keys with caching
            $cacheKey = 'lti_jwks_' . md5($platform['key_set_url']);
            $jwks = cache()->remember($cacheKey, 3600, function () use ($platform) {
                try {
                    Log::info('Fetching JWK set from Canvas', ['url' => $platform['key_set_url']]);

                    $response = Http::timeout(10)->get($platform['key_set_url']);
                    if (!$response->successful()) {
                        Log::error('Failed to fetch platform JWK set', [
                            'url' => $platform['key_set_url'],
                            'status' => $response->status()
                        ]);
                        return null;
                    }

                    $jwks = $response->json();
                    if (!isset($jwks['keys']) || !is_array($jwks['keys'])) {
                        Log::error('Invalid JWK set format - missing keys array');
                        return null;
                    }

                    Log::info('Successfully fetched JWK set', ['key_count' => count($jwks['keys'])]);
                    return $jwks;
                } catch (\Exception $e) {
                    Log::error('Exception while fetching JWK set', ['error' => $e->getMessage()]);
                    return null;
                }
            });

            if (!$jwks) {
                Log::error('Could not retrieve JWK set');
                return false;
            }

            // Convert JWK set to keys array for Firebase JWT
            $keys = JWK::parseKeySet($jwks);

            if (empty($keys)) {
                Log::error('No valid keys found in JWK set');
                return false;
            }

            // Validate the JWT with algorithm allowlist
            JWT::$leeway = 60; // Allow 60 seconds of clock skew
            $decoded = JWT::decode($jwt, $keys);

            // Convert to array for easier handling
            $payload = json_decode(json_encode($decoded), true);

            // Additional validation
            if (!isset($payload['iat']) || !isset($payload['exp'])) {
                Log::error('JWT missing required time claims');
                return false;
            }

            // Validate message type
            $messageType = $payload['https://purl.imsglobal.org/spec/lti/claim/message_type'] ?? null;
            if ($messageType !== 'LtiResourceLinkRequest') {
                Log::warning('Unexpected LTI message type', ['message_type' => $messageType]);
            }

            // Validate LTI version
            $ltiVersion = $payload['https://purl.imsglobal.org/spec/lti/claim/version'] ?? null;
            if ($ltiVersion !== '1.3.0') {
                Log::warning('Unexpected LTI version', ['version' => $ltiVersion]);
            }

            Log::info('JWT validation successful', [
                'sub' => $payload['sub'] ?? 'unknown',
                'iss' => $payload['iss'],
                'aud' => $payload['aud'],
                'message_type' => $messageType,
                'version' => $ltiVersion
            ]);

            return $payload;
        } catch (\Firebase\JWT\ExpiredException $e) {
            Log::error('JWT has expired', ['error' => $e->getMessage()]);
            return false;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            Log::error('JWT signature validation failed', ['error' => $e->getMessage()]);
            return false;
        } catch (\Exception $e) {
            Log::error('JWT validation failed', [
                'error' => $e->getMessage(),
                'issuer' => $issuer ?? 'unknown',
                'platform_url' => $platform['key_set_url'] ?? 'not set'
            ]);
            return false;
        }
    }

    /**
     * Tool configuration endpoint
     */
    public function config(Request $request)
    {
        $config = config('lti.tool');

        return response()->json($config);
    }

    /**
     * API: Get user information
     */
    public function getUserInfo(Request $request)
    {
        $ltiService = new LtiService();

        return response()->json([
            'user' => $ltiService->getUserInfo(),
            'roles' => $ltiService->extractRoles(),
            'can_administer' => $ltiService->canAdminister(),
        ]);
    }

    /**
     * API: Get course information
     */
    public function getCourseInfo(Request $request)
    {
        $ltiService = new LtiService();

        return response()->json([
            'course' => $ltiService->getCourseInfo(),
            'resource_link' => $ltiService->getResourceLinkInfo(),
            'platform' => $ltiService->getPlatformInfo(),
            'supports_ags' => $ltiService->supportsAgs(),
            'ags_endpoints' => $ltiService->getAgsEndpoints(),
        ]);
    }

    /**
     * API: Send grade back to LMS
     */
    public function sendGrade(Request $request)
    {
        $request->validate([
            'score' => 'required|numeric|min:0',
            'max_score' => 'numeric|min:0.1',
            'comment' => 'string|max:1000',
        ]);

        $ltiService = new LtiService();

        $success = $ltiService->sendGrade(
            $request->input('score'),
            $request->input('max_score', 100.0),
            $request->input('comment')
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Grade sent successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send grade'
            ], 500);
        }
    }
}
