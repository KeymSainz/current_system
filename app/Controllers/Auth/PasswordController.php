<?php
namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\OtpModel;

class PasswordController extends Controller
{
    private UserModel $users;
    private OtpModel  $otps;

    public function __construct()
    {
        parent::__construct();
        $this->users = new UserModel();
        $this->otps  = new OtpModel();
    }

    // POST /api/password/forgot  → send reset OTP
    public function forgot(): void
    {
        $config = require APP_ROOT . '/app/Core/config.php';
        $email  = trim($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(false, 'Invalid email address.', [], 422);
        }

        $user = $this->users->findByEmail($email);
        // Always respond success to prevent email enumeration
        if (!$user) {
            $this->json(true, 'If that email exists, a reset code has been sent.');
        }

        $otp       = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpHash   = password_hash($otp, PASSWORD_BCRYPT);
        $expiresAt = date('Y-m-d H:i:s', time() + ($config['otp_expiry'] ?? 600));

        $this->otps->create($user['id'], $otpHash, 'reset', $expiresAt);

        \ = file_exists(APP_ROOT . '/backend/mailer.php')
    ? APP_ROOT . '/backend/mailer.php'
    : APP_ROOT . '/fixandgo/backend/mailer.php';
require_once \;
        sendOTPEmail($email, $user['first_name'], $otp, 'reset');

        $_SESSION['pending_email']   = $email;
        $_SESSION['pending_purpose'] = 'reset';

        $this->json(true, 'Reset code sent to ' . $email);
    }

    // POST /api/password/reset  → set new password
    public function reset(): void
    {
        $authorizedEmail = $_SESSION['reset_authorized_email'] ?? '';
        if (!$authorizedEmail) {
            $this->json(false, 'Session expired. Start the reset process again.', [], 401);
        }

        $newPassword = $_POST['newPassword'] ?? '';
        if (strlen($newPassword) < 8 || !preg_match('/[A-Z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
            $this->json(false, 'Password must be 8+ chars with one uppercase and one number.', [], 422);
        }

        $user = $this->users->findByEmail($authorizedEmail);
        if (!$user) {
            $this->json(false, 'Account not found.', [], 404);
        }

        $this->db->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($newPassword, PASSWORD_BCRYPT), $user['id']]);

        unset($_SESSION['reset_authorized_email']);

        $this->json(true, 'Password reset successfully!', ['redirect' => 'login.html']);
    }
}
