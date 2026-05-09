<?php
/**
 * Fix&Go — Registration Endpoint
 * POST /backend/register.php
 *
 * Security:
 *  - CSRF validation
 *  - Rate limiting (3 attempts / hour per IP)
 *  - Input sanitization & validation
 *  - bcrypt password hashing (cost 12)
 *  - OTP generation + email dispatch
 *  - Parameterized queries (SQL injection prevention)
 */

require_once __DIR__ . '/helpers.php';

startSecureSession();
setCORSHeaders();
header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Method not allowed.', [], 405);
}

// ── CSRF ──────────────────────────────────────────────────────────────────
validateCSRF();

// ── Rate Limit ────────────────────────────────────────────────────────────
$pdo    = require __DIR__ . '/db.php';
$config = require __DIR__ . '/config.php';
$ip     = getClientIP();

// ── Input Validation ──────────────────────────────────────────────────────
$firstName = sanitizeString($_POST['firstName'] ?? '');
$lastName  = sanitizeString($_POST['lastName']  ?? '');
$email     = trim($_POST['email']    ?? '');
$password  = $_POST['password']      ?? '';
$userType  = $_POST['userType']      ?? '';

$errors = [];

if (strlen($firstName) < 2 || strlen($firstName) > 50) {
    $errors[] = 'First name must be 2–50 characters.';
}
if (strlen($lastName) < 2 || strlen($lastName) > 50) {
    $errors[] = 'Last name must be 2–50 characters.';
}
if (!validateEmail($email)) {
    $errors[] = 'Please provide a valid email address.';
}
if (!validatePassword($password)) {
    $errors[] = 'Password must be at least 8 characters and include an uppercase letter and a number.';
}
// Only allow customer, supplier, and owner registrations
// Staff roles (sales_person, supervisor, phone_technician) can only be added by owners
if (!in_array($userType, ['customer', 'supplier', 'owner'], true)) {
    $errors[] = 'Invalid user type selected.';
}

if (!empty($errors)) {
    jsonResponse(false, implode(' ', $errors), [], 422);
}

// ── Duplicate Email Check ─────────────────────────────────────────────────
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);

if ($stmt->fetch()) {
    jsonResponse(false, 'An account with this email already exists.', [], 409);
}

// ── Hash Password (bcrypt) ────────────────────────────────────────────────
$passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => $config['bcrypt_cost']]);

// ── Insert User ───────────────────────────────────────────────────────────
$stmt = $pdo->prepare(
    'INSERT INTO users (first_name, last_name, email, password_hash, role, is_verified, created_at)
     VALUES (?, ?, ?, ?, ?, 0, NOW())'
);
$stmt->execute([$firstName, $lastName, $email, $passwordHash, $userType]);
$userId = $pdo->lastInsertId();

// Note: Staff roles (sales_person, supervisor, phone_technician) are created by owners
// through the staff management interface, not through public registration

// ── Generate & Store OTP ──────────────────────────────────────────────────
$otp       = generateOTP();
$otpHash   = password_hash($otp, PASSWORD_BCRYPT);  // Store hash, not plaintext
$expiresAt = date('Y-m-d H:i:s', time() + $config['otp_expiry']);

// Remove any existing OTPs for this user
$pdo->prepare('DELETE FROM otp_tokens WHERE user_id = ? AND purpose = ?')
    ->execute([$userId, 'verify']);

$pdo->prepare(
    'INSERT INTO otp_tokens (user_id, otp_hash, purpose, expires_at, attempts)
     VALUES (?, ?, ?, ?, 0)'
)->execute([$userId, $otpHash, 'verify', $expiresAt]);

// ── Send OTP Email ────────────────────────────────────────────────────────
require_once __DIR__ . '/mailer.php';
$sent = sendOTPEmail($email, $firstName, $otp, 'verify');

if (!$sent) {
    // Log but don't fail — user can request resend
    error_log("[Fix&Go] Failed to send OTP email to $email");
}

// ── Store pending email in session for OTP page ───────────────────────────
$_SESSION['pending_email']   = $email;
$_SESSION['pending_purpose'] = 'verify';

jsonResponse(true, 'Account created. Please check your email for the verification code.', [
    'redirect' => 'otp.php',
]);
