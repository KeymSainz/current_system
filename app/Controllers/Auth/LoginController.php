<?php
namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Models\UserModel;

/**
 * Fix&Go — Login Controller
 * POST /api/login  → validate credentials → create session → redirect by role
 * POST /api/logout → destroy session
 * OTP removed: login is direct (password only).
 */
class LoginController extends Controller
{
    private UserModel $users;

    public function __construct()
    {
        parent::__construct();
        $this->users = new UserModel();
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(false, 'Method not allowed.', [], 405);
        }

        $config         = require APP_ROOT . '/app/Core/config.php';
        $maxAttempts    = (int)($config['login_max_attempts']    ?? 5);
        $lockoutSeconds = (int)($config['login_lockout_seconds'] ?? 900);

        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';
        $remember = !empty($_POST['rememberMe']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
            $this->json(false, 'Invalid email or password.', [], 401);
        }

        // Constant-time dummy hash to prevent timing attacks
        $dummyHash = '$2y$12$invalidhashtopreventtimingattacks000000000000000000000';
        $user      = $this->users->findByEmail($email);
        $hash      = $user ? $user['password_hash'] : $dummyHash;
        $passOk    = password_verify($password, $hash);

        // Check account lockout
        if ($user && !empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
            $mins = ceil((strtotime($user['locked_until']) - time()) / 60);
            $this->json(false, "Account locked. Try again in {$mins} minute(s).", [
                'locked' => true, 'seconds_left' => strtotime($user['locked_until']) - time(),
            ], 429);
        }

        if (!$user || !$passOk) {
            if ($user) {
                $newAttempts = (int)$user['login_attempts'] + 1;
                $remaining   = $maxAttempts - $newAttempts;
                if ($newAttempts >= $maxAttempts) {
                    $lockedUntil = date('Y-m-d H:i:s', time() + $lockoutSeconds);
                    $this->users->incrementLoginAttempts($user['id'], $newAttempts, $lockedUntil);
                    $this->users->logActivity($user['id'], 'login_failed');
                    $this->json(false, 'Too many failed attempts. Account locked for ' . ceil($lockoutSeconds / 60) . ' minutes.', [
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

        // Reset login attempts
        $this->users->resetLoginAttempts($user['id']);

        // Create session immediately — no OTP
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['first_name'];

        $this->users->logActivity($user['id'], 'login');
        $this->users->updateLastLogin($user['id']);

        // Remember me
        if ($remember) {
            $token     = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $lifetime  = (int)($config['remember_lifetime'] ?? 2592000);
            $this->db->prepare('INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)')
                ->execute([$user['id'], $tokenHash, date('Y-m-d H:i:s', time() + $lifetime)]);
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            setcookie('fg_remember', $token, ['expires' => time() + $lifetime, 'path' => '/', 'secure' => $isHttps, 'httponly' => true, 'samesite' => 'Strict']);
        }

        // Role-based redirect
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
                'id'         => $user['id'],
                'firstName'  => $user['first_name'],
                'lastName'   => $user['last_name'],
                'email'      => $user['email'],
                'role'       => $user['role'],
                'verified'   => true,
                'avatar_url' => $user['avatar_url'] ?? null,
            ],
        ]);
    }

    public function logout(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->users->logActivity((int)$_SESSION['user_id'], 'logout');
        }
        setcookie('fg_remember', '', time() - 3600, '/');
        session_unset();
        session_destroy();
        $this->json(true, 'Logged out successfully.');
    }
}
