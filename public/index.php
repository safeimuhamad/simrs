<?php

$debug = filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN);
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header_remove('X-Powered-By');

$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $isHttps,
        'samesite' => 'Lax',
    ]);
    session_start();
}

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/Core/' . $class . '.php',
        __DIR__ . '/../app/Controllers/' . $class . '.php',
        __DIR__ . '/../app/Models/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

require_once __DIR__ . '/../app/Core/Helper.php';

$route = trim((string) ($_GET['route'] ?? ''), '/');
if ($route !== '') {
    $_GET['page'] = $route;
}

verifyCsrfRequest();

require_once __DIR__ . '/../routes/web.php';
