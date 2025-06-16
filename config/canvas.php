<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Canvas LMS Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Canvas LMS API integration
    |
    */

    'base_url' => env('CANVAS_BASE_URL', 'https://uvadlo-dev.test.instructure.com'),
    'access_token' => env('CANVAS_ACCESS_TOKEN'),
    
    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    */
    
    'timeout' => env('CANVAS_API_TIMEOUT', 30),
    'retry_attempts' => env('CANVAS_API_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('CANVAS_API_RETRY_DELAY', 1000), // milliseconds
];
