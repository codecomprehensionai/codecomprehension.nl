<?php

return [
    'base_url' => env('LLM_BASE_URL', 'https://llm.codecomprehension.nl/'),
    'timeout' => env('LLM_TIMEOUT', 120), // timeout in seconds (5 minutes for AI processing)

    'testing_private_key' => env('LLM_TESTING_PRIVATE_KEY', ''),
    'testing_public_key' => env('LLM_TESTING_PUBLIC_KEY', ''),
];