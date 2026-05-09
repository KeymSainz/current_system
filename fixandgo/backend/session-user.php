<?php
/**
 * Fix&Go — Session User Endpoint
 * GET /backend/session-user.php
 *
 * Returns the currently logged-in user from the PHP session.
 * Used by dashboard.js to populate the JS UserStore after
 * a server-side login (Google OAuth, PHP login endpoint).
 *
 * Also flushes the one-time oauth_user_payload if present.
 *
 * POST action=update_profile  → update name/email/phone
 * POST action=change_password → change password
 */

require_once __DIR__ . '/helpers.php';

startSecureSession();
setCORSHeaders();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['loggedIn' => false, 'success' => false, 'message' => 'Not logged in.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

// ── POST: profile update / password change ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';
    $pdo    = require __DIR__ . '/db.php';

    if ($action === 'update_profile') {
        $firstName = trim($body['first_name'] ?? '');
        $lastName  = trim($body['last_name']  ?? '');
        $email     = trim($body['email']      ?? '');
        $phone     = trim($body['phone']      ?? '');

        if (empty($firstName) || empty($lastName) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'First name, last name, and email are required.']);
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
            exit;
        }

        // Check email uniqueness (excluding current user)
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $stmt->execute([$email, $userId]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email is already in use by another account.']);
            exit;
        }

        $stmt = $pdo->prepare(
            'UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, updated_at = NOW() WHERE id = ?'
        );
        $stmt->execute([$firstName, $lastName, $email, $phone, $userId]);

        echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
        exit;
    }

    if ($action === 'change_password') {
        $currentPassword = $body['current_password'] ?? '';
        $newPassword     = $body['new_password']     ?? '';

        if (empty($currentPassword) || empty($newPassword)) {
            echo json_encode(['success' => false, 'message' => 'Both current and new passwords are required.']);
            exit;
        }
        if (strlen($newPassword) < 8) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters.']);
            exit;
        }

        // Verify current password
        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
            exit;
        }

        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$newHash, $userId]);

        echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// ── GET: return session user ──────────────────────────────────────────────

// If there's a one-time OAuth payload, return it and clear it
if (!empty($_SESSION['oauth_user_payload'])) {
    $payload = $_SESSION['oauth_user_payload'];
    unset($_SESSION['oauth_user_payload']);
    $user = json_decode($payload, true);
    echo json_encode(['loggedIn' => true, 'user' => $user]);
    exit;
}

// Otherwise fetch from DB
$pdo  = require __DIR__ . '/db.php';
$stmt = $pdo->prepare('SELECT id, first_name, last_name, email, phone, role, is_verified, created_at FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$userId]);
$u = $stmt->fetch();

if (!$u) {
    // Session references a deleted user — clear it
    session_destroy();
    echo json_encode(['loggedIn' => false]);
    exit;
}

echo json_encode([
    'loggedIn' => true,
    'user' => [
        'id'        => $u['id'],
        'firstName' => $u['first_name'],
        'lastName'  => $u['last_name'],
        'email'     => $u['email'],
        'phone'     => $u['phone'] ?? '',
        'role'      => $u['role'],
        'verified'  => (bool) $u['is_verified'],
        'createdAt' => $u['created_at'],
    ],
]);
