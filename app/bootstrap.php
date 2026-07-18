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

// ── Enforce session idle timeout ──────────────────────────────────────────
if (!empty($_SESSION['user_id'])) {
    $timeout      = 600; // 10 minutes
    $lastActivity = $_SESSION['_last_activity'] ?? time();

    if ((time() - $lastActivity) > $timeout) {
        session_unset();
        session_destroy();

        // If this is an API call, return JSON
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($uri, '/api/') !== false) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Session expired. Please log in again.', 'expired' => true]);
            exit;
        }
    } else {
        $_SESSION['_last_activity'] = time();
    }
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
