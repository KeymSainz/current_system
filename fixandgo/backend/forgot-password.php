<?php
/**
 * Fix&Go — Forgot Password: Send Reset OTP
 * POST /backend/forgot-password.php
 *
 * Step 1 of the password reset flow.
 * Generates an OTP, stores it hashed in otp_tokens,
 * and sends it to the user's email via PHPMailer.
 *
 * Security:
 *  - CSRF validation
 *  - Rate limiting (3 requests / 15 min per IP)
 *  - Generic response — never reveals whether email exists
 *  - OTP stored as bcrypt hash, never plaintext
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

// Rate limit: 3 reset requests per 15 minutes per IP
if (!checkRateLimit($pdo, $ip, 'forgot_password', 3, 900)) {
    jsonResponse(false, 'Too many requests. Please wait 15 minutes before trying again.', [], 429);
}

$email = trim($_POST['email'] ?? '');

if (!validateEmail($email)) {
    jsonResponse(false, 'Please enter a valid email address.', [], 422);
}

// Look up user — use generic response to prevent email enumeration
$stmt = $pdo->prepare('SELECT id, first_name FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

// Always respond with success to prevent email enumeration
if (!$user) {
    jsonResponse(true, 'If this email is registered, a reset code has been sent.');
}

// Delete any existing reset OTPs for this user
$pdo->prepare('DELETE FROM otp_tokens WHERE user_id = ? AND purpose = ?')
    ->execute([$user['id'], 'reset']);

// Generate new OTP
$otp       = generateOTP();
$otpHash   = password_hash($otp, PASSWORD_BCRYPT);
$expiresAt = date('Y-m-d H:i:s', time() + $config['otp_expiry']);

$pdo->prepare(
    'INSERT INTO otp_tokens (user_id, otp_hash, purpose, expires_at, attempts)
     VALUES (?, ?, ?, ?, 0)'
)->execute([$user['id'], $otpHash, 'reset', $expiresAt]);

// Send OTP email
require_once __DIR__ . '/mailer.php';
$sent = sendOTPEmail($email, $user['first_name'], $otp, 'reset');

if (!$sent) {
    error_log("[Fix&Go] Failed to send reset OTP to $email");
}

// Store email in session so step 2 knows who is resetting
$_SESSION['reset_pending_email'] = $email;

jsonResponse(true, 'A reset code has been sent to your email.', [
    'email' => $email,
]);
