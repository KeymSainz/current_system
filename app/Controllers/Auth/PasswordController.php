<?php
namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Models\UserModel;

/**
 * Fix&Go — Password Controller
 * POST /api/password/reset → change password for logged-in user
 * OTP/email-based forgot-password removed.
 * Reset now requires the user to be logged in and provide current password.
 */
class PasswordController extends Controller
{
    private UserModel $users;

    public function __construct()
    {
        parent::__construct();
        $this->users = new UserModel();
    }

    // POST /api/password/reset — authenticated password change
    public function reset(): void
    {
        $this->requireAuth();
        $userId          = (int) $_SESSION['user_id'];
        $currentPassword = $_POST['currentPassword'] ?? $_POST['current_password'] ?? '';
        $newPassword     = $_POST['newPassword']     ?? $_POST['new_password']      ?? '';

        if (empty($currentPassword) || empty($newPassword)) {
            $this->json(false, 'Both current and new passwords are required.', [], 422);
        }
        if (strlen($newPassword) < 8 || !preg_match('/[A-Z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
            $this->json(false, 'New password must be 8+ chars with one uppercase letter and one number.', [], 422);
        }

        $stmt = $this->db->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($currentPassword, $user['password_hash'] ?? '')) {
            $this->json(false, 'Current password is incorrect.', [], 401);
        }

        $this->db->prepare('UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?')
            ->execute([password_hash($newPassword, PASSWORD_BCRYPT), $userId]);

        $this->json(true, 'Password changed successfully.');
    }
}
