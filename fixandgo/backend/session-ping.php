<?php
/**
 * Fix&Go — Session Ping / Heartbeat
 *
 * Called by the frontend every ~4 minutes while the user is active.
 * Resets the idle timer so the session stays alive.
 *
 * GET  → returns session status + seconds remaining
 * POST → resets the idle timer (user activity ping)
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
setCORSHeaders();
header('Content-Type: application/json');
header('Cache-Control: no-store');

$timeout = 600; // 10 minutes — must match helpers.php

// Not logged in
if (empty($_SESSION['user_id'])) {
    echo json_encode([
        'loggedIn'         => false,
        'expired'          => true,
        'secondsRemaining' => 0,
    ]);
    exit;
}

$lastActivity     = $_SESSION['_last_activity'] ?? time();
$elapsed          = time() - $lastActivity;
$secondsRemaining = max(0, $timeout - $elapsed);

// POST = user is active, reset the timer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['_last_activity'] = time();
    $secondsRemaining = $timeout;
}

echo json_encode([
    'loggedIn'         => true,
    'expired'          => false,
    'secondsRemaining' => $secondsRemaining,
    'timeoutSeconds'   => $timeout,
]);
