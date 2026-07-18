<?php
namespace App\Models;

use App\Core\Model;

class OtpModel extends Model
{
    public function create(int $userId, string $otpHash, string $purpose, string $expiresAt): void
    {
        // Delete any existing OTP for this user+purpose
        $this->db->prepare('DELETE FROM otp_tokens WHERE user_id = ? AND purpose = ?')
            ->execute([$userId, $purpose]);

        $this->db->prepare(
            'INSERT INTO otp_tokens (user_id, otp_hash, purpose, expires_at, attempts)
             VALUES (?, ?, ?, ?, 0)'
        )->execute([$userId, $otpHash, $purpose, $expiresAt]);
    }

    public function findLatest(int $userId, string $purpose): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, otp_hash, expires_at, attempts
             FROM otp_tokens
             WHERE user_id = ? AND purpose = ?
             ORDER BY created_at DESC LIMIT 1'
        );
        $stmt->execute([$userId, $purpose]);
        return $stmt->fetch() ?: null;
    }

    public function incrementAttempts(int $id): void
    {
        $this->db->prepare('UPDATE otp_tokens SET attempts = attempts + 1 WHERE id = ?')
            ->execute([$id]);
    }

    public function delete(int $id): void
    {
        $this->db->prepare('DELETE FROM otp_tokens WHERE id = ?')->execute([$id]);
    }
}
