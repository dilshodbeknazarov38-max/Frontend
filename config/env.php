<?php
return [
    'app_name' => 'CPA Landing Manager',
    'app_url' => 'https://your-domain.com',
    'default_locale' => 'uz',
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'cpa_landing',
        'username' => 'db_user',
        'password' => 'secret',
        'charset' => 'utf8mb4',
    ],
    'security' => [
        'session_name' => 'cpa_session',
        'csrf_key' => 'change_this_random_key',
    ],
    'api' => [
        'key' => 'change_this_api_key',
        'rate_limit' => [
            'max_requests' => 60,
            'decay_seconds' => 60,
        ],
    ],
];
