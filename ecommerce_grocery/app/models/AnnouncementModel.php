<?php
require_once __DIR__ . '/../core/Model.php';

class AnnouncementModel extends Model
{
    public function all(int $limit = 20): array
    {
        return Database::fetchAll("SELECT * FROM announcements ORDER BY created_at DESC LIMIT ?", 'i', [$limit]);
    }

    public function create(int $adminId, string $title, string $message): int
    {
        return Database::insert("INSERT INTO announcements (admin_id, title, message) VALUES (?,?,?)", 'iss', [$adminId, $title, $message]);
    }
}
