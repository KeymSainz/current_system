<?php
/**
 * Fix&Go — Password Reset Endpoint
 * POST /backend/reset-password.php
 *
 * Security:
 *  - CSRF validation
 *  - Session-based authorization (OTP must be verified first)
 *  - Password strength validation
 *  - bcrypt hashing
 *  - Session invalidation after reset
 */

require_once __DIR__ . '/helpers.php';

startSecureSession();
setCORSHeaders();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Method not allowed.', [], 405);
}

validateCSRF();

// Must have completed OTP verification
if (empty($_SESSION['reset_authorized_email'])) {
    jsonResponse(false, 'Unauthorized. Please complete OTP verification first.', [], 403);
}

$pdo    = require __DIR__ . '/db.php';
$config = require __DIR__ . '/config.php';

$email      = $_SESSION['reset_authorized_email'];
$newPassword = $_POST['newPassword'] ?? '';

if (!validatePassword($newPassword)) {
    jsonResponse(false, 'Password must be at least 8 characters and include uppercase, lowercase, and a number.', [], 422);
}

$hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => $config['bcrypt_cost']]);

$stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
$stmt->execute([$hash, $email]);

if ($stmt->rowCount() === 0) {
    jsonResponse(false, 'Account not found.', [], 404);
}

// Invalidate all remember-me tokens for this user
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();
if ($user) {
    $pdo->prepare('DELETE FROM remember_tokens WHERE user_id = ?')->execute([$user['id']]);
}

// Clear reset session
unset($_SESSION['reset_authorized_email']);

jsonResponse(true, 'Password reset successfully. Please log in with your new password.', [
    'redirect' => 'login.html',
]);
