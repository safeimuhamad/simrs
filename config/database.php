<?php

return [
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'dbname' => getenv('DB_DATABASE') ?: 'sim-rs',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset' => getenv('DB_CHARSET') ?: 'utf8mb4'
];
