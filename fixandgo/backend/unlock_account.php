<?php
/**
 * Unlock Accounts - Resets login attempts and lockout for all locked users
 */
require_once __DIR__ . '/db.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    // Show ALL users and their lock status
    $stmt = $pdo->query("SELECT id, email, role, login_attempts, locked_until, is_active, is_banned FROM users ORDER BY role, email");
    $users = $stmt->fetchAll();

    echo "=== ALL USER ACCOUNTS ===\n\n";
    foreach ($users as $user) {
        $isLocked = $user['locked_until'] && strtotime($user['locked_until']) > time();
        $status = $isLocked ? '🔒 LOCKED' : '✓ OK';
        echo "[{$status}] {$user['email']} (role: {$user['role']}, attempts: {$user['login_attempts']}, locked_until: " . ($user['locked_until'] ?: 'none') . ")\n";
    }

    // Unlock ALL locked accounts
    echo "\n=== UNLOCKING ALL LOCKED ACCOUNTS ===\n\n";
    $stmt = $pdo->prepare("UPDATE users SET login_attempts = 0, locked_until = NULL WHERE login_attempts > 0 OR locked_until IS NOT NULL");
    $stmt->execute();
    $unlocked = $stmt->rowCount();

    echo "✅ Unlocked $unlocked account(s).\n";
    echo "\nAll accounts can now log in.\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
