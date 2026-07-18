<?php
namespace App\Models;

use App\Core\Model;

class UserModel extends Model
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, first_name, last_name, email, password_hash, role,
                    is_verified,
                    COALESCE(is_banned, 0)       AS is_banned,
                    COALESCE(banned_reason, NULL) AS banned_reason,
                    COALESCE(is_active, 1)        AS is_active,
                    COALESCE(login_attempts, 0)   AS login_attempts,
                    locked_until, avatar_url
             FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, first_name, last_name, email, phone, role,
                    is_verified, is_active, avatar_url, created_at
             FROM users WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (first_name, last_name, email, password_hash, role, is_verified)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password_hash'],
            $data['role'] ?? 'customer',
            $data['is_verified'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function upsertOAuth(array $profile): int
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$profile['email']]);
        $existing = $stmt->fetch();

        if ($existing) {
            $this->db->prepare(
                'UPDATE users SET provider = "google", provider_id = ?, is_verified = 1 WHERE id = ?'
            )->execute([$profile['id'], $existing['id']]);
            return (int) $existing['id'];
        }

        $this->db->prepare(
            'INSERT INTO users (first_name, last_name, email, provider, provider_id, role, is_verified)
             VALUES (?, ?, ?, "google", ?, "customer", 1)'
        )->execute([
            $profile['given_name']  ?? 'Google',
            $profile['family_name'] ?? 'User',
            $profile['email'],
            $profile['id'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function incrementLoginAttempts(int $id, int $attempts, ?string $lockedUntil = null): void
    {
        if ($lockedUntil) {
            $this->db->prepare('UPDATE users SET login_attempts = ?, locked_until = ? WHERE id = ?')
                ->execute([$attempts, $lockedUntil, $id]);
        } else {
            $this->db->prepare('UPDATE users SET login_attempts = ? WHERE id = ?')
                ->execute([$attempts, $id]);
        }
    }

    public function resetLoginAttempts(int $id): void
    {
        $this->db->prepare('UPDATE users SET login_attempts = 0, locked_until = NULL WHERE id = ?')
            ->execute([$id]);
    }

    public function setVerified(int $id): void
    {
        $this->db->prepare('UPDATE users SET is_verified = 1 WHERE id = ?')->execute([$id]);
    }

    public function updateLastLogin(int $id): void
    {
        $this->db->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')->execute([$id]);
    }

    public function logActivity(int $userId, string $action): void
    {
        $ip        = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512);
        try {
            $this->db->prepare(
                'INSERT INTO user_activity_logs (user_id, action, ip_address, user_agent, created_at)
                 VALUES (?, ?, ?, ?, NOW())'
            )->execute([$userId, $action, $ip, $userAgent]);
        } catch (\Throwable $e) {
            error_log('logActivity error: ' . $e->getMessage());
        }
    }
}
