<?php
/**
 * Fix&Go — Logout Endpoint
 * POST /backend/logout.php
 *
 * Destroys session, clears remember-me cookie and DB token.
 * Accepts both POST (fetch from JS) and GET (direct link).
 */

require_once __DIR__ . '/helpers.php';

startSecureSession();

// Capture user ID before destroying the session
$userId = (int)($_SESSION['user_id'] ?? 0);

// Determine if this is a session expiry (sent by session-timeout.js)
$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$reason = $body['reason'] ?? ($_GET['reason'] ?? 'manual');
$action = ($reason === 'timeout') ? 'session_expired' : 'logout';

// Record logout before destroying session
if ($userId) {
    try {
        $pdo = require __DIR__ . '/db.php';
        logUserActivity($pdo, $userId, $action);
    } catch (\Throwable $e) {
        // Never let logging break logout
    }
}

// Clear remember-me token from DB
if (!empty($_COOKIE['fg_remember'])) {
    if (!isset($pdo)) $pdo = require __DIR__ . '/db.php';
    $tokenHash = hash('sha256', $_COOKIE['fg_remember']);
    $pdo->prepare('DELETE FROM remember_tokens WHERE token_hash = ?')->execute([$tokenHash]);
    clearRememberMeCookie();
}

// Destroy session
$_SESSION = [];
session_destroy();

// If called via fetch (JS), return JSON; otherwise redirect
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
          (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if ($isAjax || $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} else {
    header('Location: ../index.html?logout=true');
}
exit;
