<?php
/**
 * Fix&Go — CSRF Token Endpoint
 * GET /backend/csrf-token.php
 *
 * Returns a fresh CSRF token for the current session.
 * Called by the frontend on page load before any form submission.
 */

require_once __DIR__ . '/helpers.php';

startSecureSession();
setCORSHeaders();
header('Content-Type: application/json');
header('Cache-Control: no-store');

echo json_encode(['token' => generateCSRFToken()]);
