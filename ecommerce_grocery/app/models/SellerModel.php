<?php
require_once __DIR__ . '/../core/Model.php';

class SellerModel extends Model
{
    public function create(int $userId, string $shopName, string $desc, string $address, ?string $logoPath): int
    {
        return Database::insert(
            "INSERT INTO sellers (user_id, shop_name, shop_description, address, shop_logo_path) VALUES (?,?,?,?,?)",
            'issss',
            [$userId, $shopName, $desc, $address, $logoPath]
        );
    }

    public function findByUserId(int $userId): ?array
    {
        return Database::fetchOne("SELECT * FROM sellers WHERE user_id = ?", 'i', [$userId]);
    }

    public function findById(int $id): ?array
    {
        return Database::fetchOne(
            "SELECT s.*, u.name AS owner_name, u.email, u.phone, u.is_active FROM sellers s
             JOIN users u ON u.id = s.user_id WHERE s.id = ?",
            'i',
            [$id]
        );
    }

    public function all(string $statusFilter = ''): array
    {
        if ($statusFilter !== '') {
            return Database::fetchAll(
                "SELECT s.*, u.name AS owner_name, u.email, u.is_active FROM sellers s
                 JOIN users u ON u.id = s.user_id WHERE s.is_approved = ? ORDER BY s.created_at DESC",
                's',
                [$statusFilter]
            );
        }
        return Database::fetchAll(
            "SELECT s.*, u.name AS owner_name, u.email, u.is_active FROM sellers s
             JOIN users u ON u.id = s.user_id ORDER BY s.created_at DESC"
        );
    }

    public function updateApproval(int $id, string $status, ?string $reason = null): bool
    {
        Database::execute(
            "UPDATE sellers SET is_approved = ?, rejection_reason = ? WHERE id = ?",
            'ssi',
            [$status, $reason, $id]
        );
        return true;
    }

    public function updateProfile(int $id, string $shopName, string $desc, string $address): bool
    {
        Database::execute(
            "UPDATE sellers SET shop_name = ?, shop_description = ?, address = ? WHERE id = ?",
            'sssi',
            [$shopName, $desc, $address, $id]
        );
        return true;
    }

    public function updateLogo(int $id, string $path): bool
    {
        Database::execute("UPDATE sellers SET shop_logo_path = ? WHERE id = ?", 'si', [$path, $id]);
        return true;
    }

    public function updateCommission(int $id, float $rate): bool
    {
        Database::execute("UPDATE sellers SET commission_rate = ? WHERE id = ?", 'di', [$rate, $id]);
        return true;
    }

    public function countApproved(): int
    {
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM sellers WHERE is_approved = 'approved'");
        return (int)($row['c'] ?? 0);
    }
}
