<?php

return [
    'base_url' => env('LLM_BASE_URL', 'https://llm.codecomprehension.nl/'),
    'timeout' => env('LLM_TIMEOUT', 120), // timeout in seconds (5 minutes for AI processing)
];