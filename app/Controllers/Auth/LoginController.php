<?php
namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\OtpModel;

class LoginController extends Controller
{
    private UserModel $users;
    private OtpModel  $otps;

    public function __construct()
    {
        parent::__construct();
        $this->users = new UserModel();
        $this->otps  = new OtpModel();
    }

    // POST /api/login
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(false, 'Method not allowed.', [], 405);
        }

        $config = require APP_ROOT . '/app/Core/config.php';
        $maxAttempts    = (int)($config['login_max_attempts']    ?? 3);
        $lockoutSeconds = (int)($config['login_lockout_seconds'] ?? 900);

        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';
        $remember = !empty($_POST['rememberMe']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
            $this->json(false, 'Invalid email or password.', [], 401);
        }

        // Dummy hash for constant-time comparison
        $dummyHash = '$2y$12$invalidhashtopreventtimingattacks000000000000000000000';
        $user      = $this->users->findByEmail($email);
        $hash      = $user ? $user['password_hash'] : $dummyHash;
        $passOk    = password_verify($password, $hash);

        // Lockout check
        if ($user && !empty($user['locked_until'])) {
            $lockedUntil = strtotime($user['locked_until']);
            if ($lockedUntil > time()) {
                $mins = ceil(($lockedUntil - time()) / 60);
                $this->json(false, "Account locked. Try again in {$mins} minute(s).", [
                    'locked' => true, 'seconds_left' => $lockedUntil - time(),
                ], 429);
            }
        }

        if (!$user || !$passOk) {
            if ($user) {
                $newAttempts = (int)$user['login_attempts'] + 1;
                $remaining   = $maxAttempts - $newAttempts;
                if ($newAttempts >= $maxAttempts) {
                    $lockedUntil = date('Y-m-d H:i:s', time() + $lockoutSeconds);
                    $this->users->incrementLoginAttempts($user['id'], $newAttempts, $lockedUntil);
                    $this->users->logActivity($user['id'], 'login_failed');
                    $this->json(false, 'Account locked for ' . ceil($lockoutSeconds / 60) . ' minutes.', [
                        'locked' => true, 'seconds_left' => $lockoutSeconds,
                    ], 429);
                }
                $this->users->incrementLoginAttempts($user['id'], $newAttempts);
                $this->users->logActivity($user['id'], 'login_failed');
                $msg = $remaining === 1
                    ? 'Invalid credentials. <strong>1 attempt remaining</strong>.'
                    : "Invalid credentials. {$remaining} attempts remaining.";
                $this->json(false, $msg, ['remaining' => $remaining, 'max_attempts' => $maxAttempts], 401);
            }
            $this->json(false, 'Invalid email or password.', [], 401);
        }

        if ($user['is_banned']) {
            $this->json(false, 'Account suspended: ' . ($user['banned_reason'] ?: 'Terms violation.'), [], 403);
        }
        if (!$user['is_active']) {
            $this->json(false, 'Account inactive. Contact support.', [], 403);
        }

        $this->users->resetLoginAttempts($user['id']);

        // Generate OTP
        $otp       = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpHash   = password_hash($otp, PASSWORD_BCRYPT);
        $expiresAt = date('Y-m-d H:i:s', time() + ($config['otp_expiry'] ?? 600));
        $purpose   = $user['is_verified'] ? 'login' : 'verify';

        $this->otps->create($user['id'], $otpHash, $purpose, $expiresAt);

        // Send OTP email
        \ = file_exists(APP_ROOT . '/backend/mailer.php')
    ? APP_ROOT . '/backend/mailer.php'
    : APP_ROOT . '/fixandgo/backend/mailer.php';
require_once \;
        $sent = sendOTPEmail($user['email'], $user['first_name'], $otp, $purpose);

        if (!$sent) {
            $this->json(false, 'Could not send verification code. Try again.', [], 500);
        }

        $_SESSION['pending_email']    = $email;
        $_SESSION['pending_purpose']  = $purpose;
        $_SESSION['pending_remember'] = $remember;

        if (!$user['is_verified']) {
            $this->json(false, 'Verify your email first. A code was sent.', ['redirect' => 'otp.html'], 403);
        }

        $this->json(true, 'Verification code sent to ' . $email, ['redirect' => 'otp.html']);
    }

    // POST /api/logout
    public function logout(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->users->logActivity((int)$_SESSION['user_id'], 'logout');
        }
        session_unset();
        session_destroy();
        $this->json(true, 'Logged out successfully.');
    }
}
