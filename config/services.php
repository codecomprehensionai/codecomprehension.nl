<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'lti' => [
        'canvas' => [
            'client_id' => env('LTI_CANVAS_CLIENT_ID'),
            'auth_login_url' => env('LTI_CANVAS_AUTH_LOGIN_URL', 'https://canvas.test.instructure.com/api/lti/authorize_redirect'),
            'auth_token_url' => env('LTI_CANVAS_AUTH_TOKEN_URL', 'https://canvas.test.instructure.com/login/oauth2/token'),
            'key_set_url' => env('LTI_CANVAS_KEY_SET_URL', 'https://canvas.test.instructure.com/api/lti/security/jwks'),
            'deployment_id' => env('LTI_CANVAS_DEPLOYMENT_ID', '1'),
        ],
    ],

];
