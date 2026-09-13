<?php
require_once __DIR__ . '/../core/Model.php';

class NotificationModel extends Model
{
    public function create(int $userId, string $message): int
    {
        return Database::insert("INSERT INTO notifications (user_id, message) VALUES (?,?)", 'is', [$userId, $message]);
    }

    public function byUser(int $userId, int $limit = 30): array
    {
        return Database::fetchAll("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?", 'ii', [$userId, $limit]);
    }

    public function unreadCount(int $userId): int
    {
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM notifications WHERE user_id = ? AND is_read = 0", 'i', [$userId]);
        return (int)($row['c'] ?? 0);
    }

    public function markAllRead(int $userId): bool
    {
        Database::execute("UPDATE notifications SET is_read = 1 WHERE user_id = ?", 'i', [$userId]);
        return true;
    }
}
