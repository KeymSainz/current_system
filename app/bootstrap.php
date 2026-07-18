<?php
/**
 * Fix&Go — Application Bootstrap
 * Loaded by index.php before routing.
 */

// ── Constants ─────────────────────────────────────────────────────────────
define('APP_ROOT', dirname(__DIR__));
define('APP_DIR',  __DIR__);

// ── Autoloader ────────────────────────────────────────────────────────────
require_once APP_DIR . '/Core/Autoloader.php';

// ── Session ───────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
               || ((int)($_SERVER['SERVER_PORT'] ?? 80) === 443);

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ── CORS headers (API calls only) ─────────────────────────────────────────
$origin  = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed = ['https://fixandgo.freedev.app'];
if (preg_match('#^https?://localhost(:\d+)?$#', $origin)) {
    $allowed[] = $origin;
}
if (in_array($origin, $allowed, true)) {
    header('Access-Control-Allow-Origin: '  . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Load routes ───────────────────────────────────────────────────────────
$router = require APP_DIR . '/routes.php';
