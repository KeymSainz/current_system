<?php
namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Models\UserModel;

/**
 * Fix&Go — Register Controller
 * POST /api/register → create user → auto-verify → create session → redirect
 * OTP removed: users are auto-verified on registration.
 */
class RegisterController extends Controller
{
    private UserModel $users;

    public function __construct()
    {
        parent::__construct();
        $this->users = new UserModel();
    }

    public function register(): void
    {
        $firstName = trim($_POST['firstName'] ?? '');
        $lastName  = trim($_POST['lastName']  ?? '');
        $email     = trim($_POST['email']     ?? '');
        $password  = $_POST['password']       ?? '';

        if (strlen($firstName) < 2 || strlen($lastName) < 2) {
            $this->json(false, 'First and last name must be at least 2 characters.', [], 422);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(false, 'Invalid email address.', [], 422);
        }
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $this->json(false, 'Password must be 8+ chars with one uppercase letter and one number.', [], 422);
        }
        if ($this->users->findByEmail($email)) {
            $this->json(false, 'An account with this email already exists.', [], 409);
        }

        $userId = $this->users->create([
            'first_name'    => $firstName,
            'last_name'     => $lastName,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role'          => 'customer',
            'is_verified'   => 1, // auto-verified, no OTP required
        ]);

        // Create session immediately
        session_regenerate_id(true);
        $_SESSION['user_id']   = $userId;
        $_SESSION['user_role'] = 'customer';
        $_SESSION['user_name'] = $firstName;

        $this->users->logActivity($userId, 'login');

        $this->json(true, 'Account created! Welcome to Fix&Go.', [
            'redirect' => '/',
            'user'     => [
                'id'        => $userId,
                'firstName' => $firstName,
                'lastName'  => $lastName,
                'email'     => $email,
                'role'      => 'customer',
                'verified'  => true,
            ],
        ]);
    }
}
