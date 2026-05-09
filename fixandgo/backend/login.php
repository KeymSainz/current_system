<?php
/**
 * Fix&Go — Login Endpoint
 * POST /backend/login.php
 *
 * Flow:
 *  1. Validate credentials against DB
 *  2. Track failed attempts — lock account after 3 failures (15 min)
 *  3. On success → reset attempts, generate OTP, email it, redirect to otp.html
 *  4. OTP verified → session created → dashboard
 *
 * Security:
 *  - CSRF validation
 *  - Per-account attempt tracking (3 max, 15-min lockout)
 *  - bcrypt password verification (constant-time)
 *  - OTP sent on every login (2FA)
 *  - Session only created AFTER OTP verified
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

$maxAttempts    = (int)($config['login_max_attempts']    ?? 3);
$lockoutSeconds = (int)($config['login_lockout_seconds'] ?? 900);

// ── Input ─────────────────────────────────────────────────────────────────
$email    = trim($_POST['email']    ?? '');
$password = $_POST['password']      ?? '';
$remember = !empty($_POST['rememberMe']);

if (!validateEmail($email) || empty($password)) {
    jsonResponse(false, 'Invalid email or password.', [], 401);
}

// ── Fetch User ────────────────────────────────────────────────────────────
try {
    $stmt = $pdo->prepare(
        'SELECT id, first_name, last_name, email, password_hash, role,
                is_verified,
                COALESCE(is_banned,  0)    AS is_banned,
                COALESCE(banned_reason, NULL) AS banned_reason,
                COALESCE(is_active,  1)    AS is_active,
                COALESCE(login_attempts, 0) AS login_attempts,
                locked_until
         FROM users WHERE email = ? LIMIT 1'
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();
} catch (Exception $e) {
    // Fallback if new columns don't exist yet
    $stmt = $pdo->prepare(
        'SELECT id, first_name, last_name, email, password_hash, role,
                is_verified, 0 AS is_banned, NULL AS banned_reason,
                is_active, 0 AS login_attempts, NULL AS locked_until
         FROM users WHERE email = ? LIMIT 1'
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();
}

// ── Constant-time hash check (prevents user enumeration via timing) ───────
$dummyHash = '$2y$12$invalidhashtopreventtimingattacks000000000000000000000';
$hash      = $user ? $user['password_hash'] : $dummyHash;
$passOk    = password_verify($password, $hash);

// ── Account lockout check (before revealing if password is correct) ───────
if ($user) {
    $lockedUntil = $user['locked_until'] ? strtotime($user['locked_until']) : 0;

    if ($lockedUntil > time()) {
        $secondsLeft = $lockedUntil - time();
        $minutesLeft = ceil($secondsLeft / 60);
        jsonResponse(false,
            "Account locked due to too many failed attempts. Try again in {$minutesLeft} minute(s).",
            [
                'locked'         => true,
                'seconds_left'   => $secondsLeft,
                'locked_until'   => date('Y-m-d H:i:s', $lockedUntil),
            ],
            429
        );
    }
}

// ── Wrong password ────────────────────────────────────────────────────────
if (!$user || !$passOk) {
    if ($user) {
        // Increment attempt counter
        $newAttempts = (int)$user['login_attempts'] + 1;
        $remaining   = $maxAttempts - $newAttempts;

        if ($newAttempts >= $maxAttempts) {
            // Lock the account
            $lockedUntil = date('Y-m-d H:i:s', time() + $lockoutSeconds);
            $pdo->prepare(
                "UPDATE users SET login_attempts = ?, locked_until = ? WHERE id = ?"
            )->execute([$newAttempts, $lockedUntil, $user['id']]);

            // Log the lockout event
            logUserActivity($pdo, (int)$user['id'], 'login_failed');

            $minutesLeft = ceil($lockoutSeconds / 60);
            jsonResponse(false,
                "Too many failed attempts. Your account has been locked for {$minutesLeft} minutes.",
                [
                    'locked'       => true,
                    'seconds_left' => $lockoutSeconds,
                    'attempts'     => $newAttempts,
                    'max_attempts' => $maxAttempts,
                ],
                429
            );
        } else {
            // Not locked yet — increment and warn
            $pdo->prepare(
                "UPDATE users SET login_attempts = ? WHERE id = ?"
            )->execute([$newAttempts, $user['id']]);

            // Log the failed attempt
            logUserActivity($pdo, (int)$user['id'], 'login_failed');

            $msg = $remaining === 1
                ? "Invalid email or password. <strong>1 attempt remaining</strong> before your account is locked."
                : "Invalid email or password. {$remaining} attempts remaining.";

            jsonResponse(false, $msg, [
                'attempts'     => $newAttempts,
                'remaining'    => $remaining,
                'max_attempts' => $maxAttempts,
            ], 401);
        }
    }

    // User not found — generic message (no attempt tracking for non-existent accounts)
    jsonResponse(false, 'Invalid email or password.', [], 401);
}

// ── Banned account ────────────────────────────────────────────────────────
if ($user['is_banned']) {
    jsonResponse(false,
        'Your account has been suspended. Reason: ' . ($user['banned_reason'] ?: 'Violation of terms.'),
        [], 403
    );
}

// ── Inactive account ──────────────────────────────────────────────────────
if (!$user['is_active']) {
    jsonResponse(false, 'Your account is inactive. Please contact support.', [], 403);
}

// ── Credentials valid — reset attempt counter ─────────────────────────────
$pdo->prepare(
    "UPDATE users SET login_attempts = 0, locked_until = NULL WHERE id = ?"
)->execute([$user['id']]);

// ── Account not yet email-verified ───────────────────────────────────────
if (!$user['is_verified']) {
    $otp       = generateOTP();
    $otpHash   = password_hash($otp, PASSWORD_BCRYPT);
    $expiresAt = date('Y-m-d H:i:s', time() + $config['otp_expiry']);

    $pdo->prepare('DELETE FROM otp_tokens WHERE user_id = ? AND purpose = ?')
        ->execute([$user['id'], 'verify']);
    $pdo->prepare(
        'INSERT INTO otp_tokens (user_id, otp_hash, purpose, expires_at, attempts)
         VALUES (?, ?, ?, ?, 0)'
    )->execute([$user['id'], $otpHash, 'verify', $expiresAt]);

    require_once __DIR__ . '/mailer.php';
    sendOTPEmail($email, $user['first_name'], $otp, 'verify');

    $_SESSION['pending_email']   = $email;
    $_SESSION['pending_purpose'] = 'verify';

    jsonResponse(false, 'Please verify your email first. A new code has been sent.', [
        'redirect' => 'otp.php',
    ], 403);
}

// ── Send login OTP (2FA) ──────────────────────────────────────────────────
$otp       = generateOTP();
$otpHash   = password_hash($otp, PASSWORD_BCRYPT);
$expiresAt = date('Y-m-d H:i:s', time() + $config['otp_expiry']);

$pdo->prepare('DELETE FROM otp_tokens WHERE user_id = ? AND purpose = ?')
    ->execute([$user['id'], 'login']);

$pdo->prepare(
    'INSERT INTO otp_tokens (user_id, otp_hash, purpose, expires_at, attempts)
     VALUES (?, ?, ?, ?, 0)'
)->execute([$user['id'], $otpHash, 'login', $expiresAt]);

require_once __DIR__ . '/mailer.php';
$sent = sendOTPEmail($email, $user['first_name'], $otp, 'login');

if (!$sent) {
    error_log("[Fix&Go] Failed to send login OTP to $email");
    jsonResponse(false, 'Could not send verification code. Please try again.', [], 500);
}

$_SESSION['pending_email']    = $email;
$_SESSION['pending_purpose']  = 'login';
$_SESSION['pending_remember'] = $remember;

jsonResponse(true, 'A verification code has been sent to ' . $email, [
    'redirect' => 'otp.php',
]);
