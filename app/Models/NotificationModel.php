<?php
namespace App\Models;

use App\Core\Model;

class NotificationModel extends Model
{
    public function list(int $userId, int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, type, title, body, is_read, created_at
             FROM notifications
             WHERE user_id = ?
             ORDER BY created_at DESC
             LIMIT ? OFFSET ?'
        );
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function countUnread(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0'
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function countTotal(int $userId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ?');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function markRead(int $userId, array $ids): int
    {
        $ids  = array_map('intval', $ids);
        $ph   = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare(
            "UPDATE notifications SET is_read = 1 WHERE id IN ($ph) AND user_id = ?"
        );
        $stmt->execute([...$ids, $userId]);
        return $stmt->rowCount();
    }

    public function markAllRead(int $userId): int
    {
        $stmt = $this->db->prepare(
            'UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0'
        );
        $stmt->execute([$userId]);
        return $stmt->rowCount();
    }

    public function delete(int $userId, array $ids): int
    {
        $ids  = array_map('intval', $ids);
        $ph   = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare(
            "DELETE FROM notifications WHERE id IN ($ph) AND user_id = ?"
        );
        $stmt->execute([...$ids, $userId]);
        return $stmt->rowCount();
    }
}
