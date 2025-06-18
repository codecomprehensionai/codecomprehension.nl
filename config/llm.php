<?php

return [
    /*
    |--------------------------------------------------------------------------
    | LLM API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for LLM API integration for question generation
    |
    */

    'base_url' => env('LLM_API_BASE_URL', 'http://localhost:8000'), // TODO: Change for production
    'timeout' => env('LLM_API_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Default Question Parameters
    |--------------------------------------------------------------------------
    |
    | Default values for question generation requests
    |
    */

    'default_params' => [
        'language' => 'Python',
        'type' => 'multiple_choice',
        'level' => 'beginner',
        'estimated_answer_duration' => 180,
        'topics' => [],
        'tags' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for handling failures and retries
    |
    */

    'retry' => [
        'attempts' => env('LLM_RETRY_ATTEMPTS', 3),
        'delay' => env('LLM_RETRY_DELAY', 1000), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Check Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for monitoring LLM service availability
    |
    */

    'health_check' => [
        'timeout' => env('LLM_HEALTH_CHECK_TIMEOUT', 5),
        'cache_ttl' => env('LLM_HEALTH_CACHE_TTL', 60), // seconds
    ],
];
