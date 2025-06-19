<?php

return [
    'base_url' => env('LLM_BASE_URL', 'https://llm.codecomprehension.nl/'),
    'timeout' => env('LLM_TIMEOUT', 120), // timeout in seconds (5 minutes for AI processing)

    'testing_private_key' => '-----BEGIN EC PRIVATE KEY-----
MHcCAQEEIMleHcofmYvzF8xz9efKwvzztMjqFSNaVmaKRBvbEUZaoAoGCCqGSM49
AwEHoUQDQgAEpQeMXHYkFzjuw1tZ7bTrnhYuEYWYdzm9+cLIxke09ZWw5KYe7ttY
UCTDbE4eZwP1MTsPr7TLKAdLL0zC8UA6Xw==
-----END EC PRIVATE KEY-----
',

    'testing_public_key' => '-----BEGIN PUBLIC KEY-----
MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEpQeMXHYkFzjuw1tZ7bTrnhYuEYWY
dzm9+cLIxke09ZWw5KYe7ttYUCTDbE4eZwP1MTsPr7TLKAdLL0zC8UA6Xw==
-----END PUBLIC KEY-----
',
];