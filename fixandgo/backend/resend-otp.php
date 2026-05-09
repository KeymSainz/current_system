<?php
/**
 * Fix&Go — Resend OTP Endpoint
 * POST /backend/resend-otp.php
 *
 * Security:
 *  - CSRF validation
 *  - Rate limiting (3 resends / 10 min per IP)
 *  - Invalidates previous OTP before issuing new one
 */

require_once __DIR__ . '/helpers.php';

startSecureSession();
setCORSHeaders();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Method not allowed.', [], 405);
}

validateCSRF();

$pdo    = require __DIR__ . '/db.php';
$config = require __DIR__ . '/config.php';
$ip     = getClientIP();

if (!checkRateLimit($pdo, $ip, 'resend_otp', 3, 600)) {
    jsonResponse(false, 'Too many resend requests. Please wait before trying again.', [], 429);
}

$email   = trim($_POST['email']   ?? $_SESSION['pending_email'] ?? '');
$purpose = trim($_POST['purpose'] ?? 'verify');

if (!validateEmail($email)) {
    jsonResponse(false, 'Invalid email address.', [], 422);
}

$stmt = $pdo->prepare('SELECT id, first_name FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    // Don't reveal whether email exists
    jsonResponse(true, 'If this email is registered, a new code has been sent.');
}

// Invalidate old OTPs
$pdo->prepare('DELETE FROM otp_tokens WHERE user_id = ? AND purpose = ?')
    ->execute([$user['id'], $purpose]);

// Generate new OTP
$otp       = generateOTP();
$otpHash   = password_hash($otp, PASSWORD_BCRYPT);
$expiresAt = date('Y-m-d H:i:s', time() + $config['otp_expiry']);

$pdo->prepare(
    'INSERT INTO otp_tokens (user_id, otp_hash, purpose, expires_at, attempts) VALUES (?, ?, ?, ?, 0)'
)->execute([$user['id'], $otpHash, $purpose, $expiresAt]);

require_once __DIR__ . '/mailer.php';
sendOTPEmail($email, $user['first_name'], $otp, $purpose);

jsonResponse(true, 'A new verification code has been sent to your email.');
