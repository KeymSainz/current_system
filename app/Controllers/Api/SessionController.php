<?php
namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\UserModel;

class SessionController extends Controller
{
    // GET /api/session/user
    public function user(): void
    {
        header('Content-Type: application/json');
        header('Cache-Control: no-store');

        if (empty($_SESSION['user_id'])) {
            echo json_encode(['loggedIn' => false, 'success' => false]);
            exit;
        }

        // Flush one-time OAuth payload
        if (!empty($_SESSION['oauth_user_payload'])) {
            $payload = json_decode($_SESSION['oauth_user_payload'], true);
            unset($_SESSION['oauth_user_payload']);
            echo json_encode(['loggedIn' => true, 'user' => $payload]);
            exit;
        }

        $users = new UserModel();
        $u     = $users->findById((int) $_SESSION['user_id']);

        if (!$u) {
            session_destroy();
            echo json_encode(['loggedIn' => false]);
            exit;
        }

        echo json_encode([
            'loggedIn' => true,
            'user'     => [
                'id'         => $u['id'],
                'firstName'  => $u['first_name'],
                'lastName'   => $u['last_name'],
                'email'      => $u['email'],
                'phone'      => $u['phone'] ?? '',
                'role'       => $u['role'],
                'verified'   => (bool) $u['is_verified'],
                'createdAt'  => $u['created_at'],
                'avatar_url' => $u['avatar_url'] ?? null,
            ],
        ]);
        exit;
    }

    // POST /api/session/ping
    public function ping(): void
    {
        header('Content-Type: application/json');
        if (empty($_SESSION['user_id'])) {
            echo json_encode(['loggedIn' => false, 'expired' => true]);
            exit;
        }
        echo json_encode(['loggedIn' => true, 'success' => true]);
        exit;
    }

    // GET /api/session/csrf
    public function csrf(): void
    {
        header('Content-Type: application/json');
        header('Cache-Control: no-store');
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        echo json_encode(['token' => $_SESSION['csrf_token']]);
        exit;
    }
}
