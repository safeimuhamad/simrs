<?php

return [
    'app_url' => getenv('APP_URL') ?: 'http://localhost/sim-rs',
    'allowed_hosts' => ['localhost'],
    'host_paths' => [
        'localhost' => '/sim-rs'
    ]
];
