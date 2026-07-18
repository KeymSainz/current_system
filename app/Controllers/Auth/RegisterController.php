<?php
namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\OtpModel;

class RegisterController extends Controller
{
    private UserModel $users;
    private OtpModel  $otps;

    public function __construct()
    {
        parent::__construct();
        $this->users = new UserModel();
        $this->otps  = new OtpModel();
    }

    // POST /api/register
    public function register(): void
    {
        $config = require APP_ROOT . '/app/Core/config.php';

        $firstName = trim($_POST['firstName'] ?? '');
        $lastName  = trim($_POST['lastName']  ?? '');
        $email     = trim($_POST['email']     ?? '');
        $password  = $_POST['password']       ?? '';

        // Validate
        if (strlen($firstName) < 2 || strlen($lastName) < 2) {
            $this->json(false, 'First and last name must be at least 2 characters.', [], 422);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(false, 'Invalid email address.', [], 422);
        }
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $this->json(false, 'Password must be 8+ chars with one uppercase and one number.', [], 422);
        }

        // Check duplicate
        if ($this->users->findByEmail($email)) {
            $this->json(false, 'An account with this email already exists.', [], 409);
        }

        $userId = $this->users->create([
            'first_name'    => $firstName,
            'last_name'     => $lastName,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role'          => 'customer',
            'is_verified'   => 0,
        ]);

        // Send verification OTP
        $otp       = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpHash   = password_hash($otp, PASSWORD_BCRYPT);
        $expiresAt = date('Y-m-d H:i:s', time() + ($config['otp_expiry'] ?? 600));

        $this->otps->create($userId, $otpHash, 'verify', $expiresAt);

        \ = file_exists(APP_ROOT . '/backend/mailer.php')
    ? APP_ROOT . '/backend/mailer.php'
    : APP_ROOT . '/fixandgo/backend/mailer.php';
require_once \;
        sendOTPEmail($email, $firstName, $otp, 'verify');

        $_SESSION['pending_email']   = $email;
        $_SESSION['pending_purpose'] = 'verify';

        $this->json(true, 'Account created! Check your email for a verification code.', [
            'redirect' => 'otp.html',
        ]);
    }
}
