<?php

return [
    'paths' => ['api/public/*'],

    'allowed_methods' => ['GET', 'OPTIONS'],

    'allowed_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('PUBLIC_API_CORS_ALLOWED_ORIGINS', '*')),
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Accept'],

    'exposed_headers' => [],

    'max_age' => 600,

    'supports_credentials' => false,
];
