<?php
/**
 * Fix&Go — OTP Verification Endpoint
 * POST /backend/verify-otp.php
 *
 * Handles three purposes:
 *  'verify' — new account email verification → logs user in
 *  'login'  — 2FA on login → logs user in
 *  'reset'  — password reset OTP → authorises password change
 *
 * Security:
 *  - CSRF validation
 *  - Max 3 attempts per OTP
 *  - OTP stored as bcrypt hash (never plaintext)
 *  - Expiry check
 *  - Constant-time comparison via password_verify
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

$email   = trim($_POST['email']   ?? $_SESSION['pending_email'] ?? '');
$otp     = trim($_POST['otp']     ?? '');
// Accept purpose from POST, fall back to what login.php stored in session
$purpose = trim($_POST['purpose'] ?? $_SESSION['pending_purpose'] ?? 'verify');

if (!validateEmail($email) || strlen($otp) !== 6 || !ctype_digit($otp)) {
    jsonResponse(false, 'Invalid request.', [], 422);
}

// ── Fetch User ────────────────────────────────────────────────────────────
$stmt = $pdo->prepare(
    'SELECT id, first_name, last_name, email, role, is_verified,
            avatar_url
     FROM users WHERE email = ? LIMIT 1'
);
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    jsonResponse(false, 'Account not found.', [], 404);
}

// ── Fetch OTP Record ──────────────────────────────────────────────────────
$stmt = $pdo->prepare(
    'SELECT id, otp_hash, expires_at, attempts
     FROM otp_tokens
     WHERE user_id = ? AND purpose = ?
     ORDER BY created_at DESC LIMIT 1'
);
$stmt->execute([$user['id'], $purpose]);
$record = $stmt->fetch();

if (!$record) {
    jsonResponse(false, 'No verification code found. Please request a new one.', [], 404);
}

// ── Expiry Check ──────────────────────────────────────────────────────────
if (strtotime($record['expires_at']) < time()) {
    jsonResponse(false, 'This code has expired. Please request a new one.', [], 410);
}

// ── Attempt Limit ─────────────────────────────────────────────────────────
if ((int) $record['attempts'] >= $config['otp_max_attempts']) {
    jsonResponse(false, 'Too many incorrect attempts. Please request a new code.', [], 429);
}

// ── Verify OTP (constant-time via bcrypt) ─────────────────────────────────
if (!password_verify($otp, $record['otp_hash'])) {
    $pdo->prepare('UPDATE otp_tokens SET attempts = attempts + 1 WHERE id = ?')
        ->execute([$record['id']]);

    $remaining = $config['otp_max_attempts'] - ((int) $record['attempts'] + 1);
    $msg = $remaining > 0
        ? "Incorrect code. $remaining attempt(s) remaining."
        : 'Incorrect code. No more attempts. Please request a new code.';

    jsonResponse(false, $msg, [], 401);
}

// ── OTP correct — delete it so it can't be reused ────────────────────────
$pdo->prepare('DELETE FROM otp_tokens WHERE id = ?')->execute([$record['id']]);

// ── Handle each purpose ───────────────────────────────────────────────────

if ($purpose === 'verify') {
    // ── New account email verification ───────────────────────────────
    $pdo->prepare('UPDATE users SET is_verified = 1 WHERE id = ?')->execute([$user['id']]);

    session_regenerate_id(true);
    $_SESSION['user_id']        = $user['id'];
    $_SESSION['user_role']      = $user['role'];
    $_SESSION['user_name']      = $user['first_name'];
    $_SESSION['_last_activity'] = time();
    unset($_SESSION['pending_email'], $_SESSION['pending_purpose']);

    // Record login event
    logUserActivity($pdo, (int)$user['id'], 'login');

    jsonResponse(true, 'Email verified! Welcome to Fix&Go.', [
        'redirect' => '../index.php',
        'user'     => [
            'id'        => $user['id'],
            'firstName' => $user['first_name'],
            'lastName'  => $user['last_name'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'verified'  => true,
            'avatar_url' => $user['avatar_url'] ?? null,
        ],
    ]);

} elseif ($purpose === 'login') {
    // ── 2FA login OTP verified — create full session ──────────────────
    $remember = $_SESSION['pending_remember'] ?? false;

    session_regenerate_id(true);
    $_SESSION['user_id']        = $user['id'];
    $_SESSION['user_role']      = $user['role'];
    $_SESSION['user_name']      = $user['first_name'];
    $_SESSION['_last_activity'] = time();
    unset($_SESSION['pending_email'], $_SESSION['pending_purpose'], $_SESSION['pending_remember']);

    // Handle Remember Me
    if ($remember) {
        $config2 = require __DIR__ . '/config.php';
        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expires   = time() + $config2['remember_lifetime'];

        $pdo->prepare(
            'INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)'
        )->execute([$user['id'], $tokenHash, date('Y-m-d H:i:s', $expires)]);

        setRememberMeCookie($token, $config2['remember_lifetime']);
    }

    // Record login event
    logUserActivity($pdo, (int)$user['id'], 'login');

    jsonResponse(true, 'Login successful! Welcome back, ' . $user['first_name'] . '.', [
        'redirect' => '../index.php',
        'user'     => [
            'id'        => $user['id'],
            'firstName' => $user['first_name'],
            'lastName'  => $user['last_name'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'verified'  => true,
            'avatar_url' => $user['avatar_url'] ?? null,
        ],
    ]);

} else {
    // ── Password reset — authorise the reset step ─────────────────────
    $_SESSION['reset_authorized_email'] = $email;
    unset($_SESSION['pending_email'], $_SESSION['pending_purpose']);

    jsonResponse(true, 'Code verified. You may now set a new password.', [
        'redirect' => 'forgot-password.php',
    ]);
}
