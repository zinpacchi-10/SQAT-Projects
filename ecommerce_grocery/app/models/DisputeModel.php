<?php
require_once __DIR__ . '/../core/Model.php';

class DisputeModel extends Model
{
    public function create(int $customerId, ?int $sellerId, ?int $orderId, string $description): int
    {
        return Database::insert(
            "INSERT INTO disputes (customer_id, seller_id, order_id, description) VALUES (?,?,?,?)",
            'iiis',
            [$customerId, $sellerId, $orderId, $description]
        );
    }

    public function byCustomer(int $customerId): array
    {
        return Database::fetchAll("SELECT * FROM disputes WHERE customer_id = ? ORDER BY created_at DESC", 'i', [$customerId]);
    }

    public function all(string $status = ''): array
    {
        if ($status !== '') {
            return Database::fetchAll(
                "SELECT d.*, u.name AS customer_name, s.shop_name FROM disputes d
                 JOIN users u ON u.id = d.customer_id LEFT JOIN sellers s ON s.id = d.seller_id
                 WHERE d.status = ? ORDER BY d.created_at DESC",
                's',
                [$status]
            );
        }
        return Database::fetchAll(
            "SELECT d.*, u.name AS customer_name, s.shop_name FROM disputes d
             JOIN users u ON u.id = d.customer_id LEFT JOIN sellers s ON s.id = d.seller_id
             ORDER BY d.created_at DESC"
        );
    }

    public function find(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM disputes WHERE id = ?", 'i', [$id]);
    }

    public function resolve(int $id, string $note): bool
    {
        Database::execute("UPDATE disputes SET status='resolved', admin_note=? WHERE id=?", 'si', [$note, $id]);
        return true;
    }
}
