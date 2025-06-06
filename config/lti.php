<?php

return [
    /*
    |--------------------------------------------------------------------------
    | LTI 1.3 Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for LTI 1.3 integration including platform registrations,
    | key sets, and deployment settings.
    |
    */

    'platforms' => [
        'canvas' => [
            'client_id' => env('LTI_CANVAS_CLIENT_ID'),
            'issuer' => env('LTI_CANVAS_ISSUER', 'https://canvas.test.instructure.com'),
            'auth_login_url' => env('LTI_CANVAS_AUTH_LOGIN_URL', 'https://sso.test.canvaslms.com/api/lti/authorize_redirect'),
            'auth_token_url' => env('LTI_CANVAS_AUTH_TOKEN_URL', 'https://canvas.test.instructure.com/login/oauth2/token'),
            'key_set_url' => env('LTI_CANVAS_KEY_SET_URL', 'https://canvas.test.instructure.com/api/lti/security/jwks'),
            'deployment_ids' => [
                env('LTI_CANVAS_DEPLOYMENT_ID', '1'),
            ],
        ],
    ],

    'tool' => [
        'title' => 'Code Comprehension',
        'description' => 'Code comprehension',
        'target_link_uri' => env('APP_URL', 'http://localhost:8000/'),
        'oidc_initiation_url' => env('APP_URL', 'http://localhost:8000') . '/auth/oidc',
        'public_jwk_url' => env('APP_URL', 'http://localhost:8000') . '/auth/jwks',
        'scopes' => [
            'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
            'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly',
        ],
        'extensions' => [
            [
                'domain' => '',
                'tool_id' => '',
                'privacy_level' => 'public',
                'platform' => 'canvas.test.instructure.com',
                'settings' => [
                    'placements' => [
                        [
                            'placement' => 'assignment_selection',
                            'message_type' => 'LtiResourceLinkRequest',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'key_chain' => [
        'private_key_file' => storage_path('app/private/lti_private_key.pem'),
        'public_key_file' => storage_path('app/public/lti_public_key.pem'),
        'key_set_url' => env('APP_URL', 'http://localhost:8000') . '/auth/jwks',
    ],
];
