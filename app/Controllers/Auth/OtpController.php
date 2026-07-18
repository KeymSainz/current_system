<?php
namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\OtpModel;

class OtpController extends Controller
{
    private UserModel $users;
    private OtpModel  $otps;

    public function __construct()
    {
        parent::__construct();
        $this->users = new UserModel();
        $this->otps  = new OtpModel();
    }

    // POST /api/otp/verify
    public function verify(): void
    {
        $config  = require APP_ROOT . '/app/Core/config.php';
        $email   = trim($_POST['email']   ?? $_SESSION['pending_email']   ?? '');
        $otp     = trim($_POST['otp']     ?? '');
        $purpose = trim($_POST['purpose'] ?? $_SESSION['pending_purpose'] ?? 'verify');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($otp) !== 6 || !ctype_digit($otp)) {
            $this->json(false, 'Invalid request.', [], 422);
        }

        $user = $this->users->findByEmail($email);
        if (!$user) {
            $this->json(false, 'Account not found.', [], 404);
        }

        $record = $this->otps->findLatest($user['id'], $purpose);
        if (!$record) {
            $this->json(false, 'No verification code found. Request a new one.', [], 404);
        }
        if (strtotime($record['expires_at']) < time()) {
            $this->json(false, 'Code expired. Request a new one.', [], 410);
        }
        if ((int)$record['attempts'] >= ($config['otp_max_attempts'] ?? 3)) {
            $this->json(false, 'Too many attempts. Request a new code.', [], 429);
        }

        if (!password_verify($otp, $record['otp_hash'])) {
            $this->otps->incrementAttempts($record['id']);
            $remaining = ($config['otp_max_attempts'] ?? 3) - ((int)$record['attempts'] + 1);
            $this->json(false, "Incorrect code. {$remaining} attempt(s) remaining.", [], 401);
        }

        // OTP correct — delete it so it cannot be reused
        $this->otps->delete($record['id']);

        // ── Password reset: just authorise the reset step, don't log in ──
        if ($purpose === 'reset') {
            $_SESSION['reset_authorized_email'] = $email;
            unset($_SESSION['pending_email'], $_SESSION['pending_purpose']);

            $this->json(true, 'Code verified. You may now set a new password.', [
                'redirect' => 'forgot-password.php',
            ]);
        }

        // ── Login / Verify: create a full session ─────────────────────────
        $remember = $_SESSION['pending_remember'] ?? false;

        session_regenerate_id(true);
        $_SESSION['user_id']        = $user['id'];
        $_SESSION['user_role']      = $user['role'];
        $_SESSION['user_name']      = $user['first_name'];
        $_SESSION['_last_activity'] = time();
        unset($_SESSION['pending_email'], $_SESSION['pending_purpose'], $_SESSION['pending_remember']);

        if ($purpose === 'verify') {
            $this->users->setVerified($user['id']);
        }

        $this->users->logActivity($user['id'], 'login');
        $this->users->updateLastLogin($user['id']);

        // ── Remember Me ────────────────────────────────────────────────────
        if ($remember) {
            $token     = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $lifetime  = (int)($config['remember_lifetime'] ?? 2592000); // 30 days
            $expires   = date('Y-m-d H:i:s', time() + $lifetime);

            $this->db->prepare(
                'INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)'
            )->execute([$user['id'], $tokenHash, $expires]);

            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                       || ((int)($_SERVER['SERVER_PORT'] ?? 80) === 443);

            setcookie('fg_remember', $token, [
                'expires'  => time() + $lifetime,
                'path'     => '/',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        }

        // ── Role-based redirect ────────────────────────────────────────────
        $redirectMap = [
            'supervisor'       => '/views/user/supervisor/dashboard',
            'owner'            => '/views/user/owner/dashboard',
            'supplier'         => '/views/user/supplier/dashboard',
            'sales_person'     => '/views/user/sales_person/dashboard',
            'phone_technician' => '/views/user/phone_technician/dashboard',
            'customer'         => '/',
            'admin'            => '/dashboard.php',
        ];
        $redirect = $redirectMap[$user['role']] ?? '/';

        $this->json(true, 'Login successful! Welcome back, ' . $user['first_name'] . '.', [
            'redirect' => $redirect,
            'user'     => [
                'id'        => $user['id'],
                'firstName' => $user['first_name'],
                'lastName'  => $user['last_name'],
                'email'     => $user['email'],
                'role'      => $user['role'],
                'verified'  => true,
                'avatar_url'=> $user['avatar_url'] ?? null,
            ],
        ]);
    }

    // POST /api/otp/resend
    public function resend(): void
    {
        $config  = require APP_ROOT . '/app/Core/config.php';
        $email   = trim($_POST['email']   ?? $_SESSION['pending_email']   ?? '');
        $purpose = trim($_POST['purpose'] ?? $_SESSION['pending_purpose'] ?? 'verify');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(false, 'Invalid email.', [], 422);
        }

        $user = $this->users->findByEmail($email);
        if (!$user) {
            $this->json(false, 'Account not found.', [], 404);
        }

        $otp       = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpHash   = password_hash($otp, PASSWORD_BCRYPT);
        $expiresAt = date('Y-m-d H:i:s', time() + ($config['otp_expiry'] ?? 600));

        $this->otps->create($user['id'], $otpHash, $purpose, $expiresAt);

        \ = file_exists(APP_ROOT . '/backend/mailer.php')
    ? APP_ROOT . '/backend/mailer.php'
    : APP_ROOT . '/fixandgo/backend/mailer.php';
require_once \;
        $sent = sendOTPEmail($email, $user['first_name'], $otp, $purpose);

        if (!$sent) {
            $this->json(false, 'Failed to send code. Try again.', [], 500);
        }

        $_SESSION['pending_email']   = $email;
        $_SESSION['pending_purpose'] = $purpose;

        $this->json(true, 'New code sent to ' . $email);
    }
}
