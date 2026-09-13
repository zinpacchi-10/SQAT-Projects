<?php
require_once __DIR__ . '/../core/Model.php';

class ReturnRequestModel extends Model
{
    public function create(int $orderId, int $orderItemId, int $customerId, string $reason): int
    {
        return Database::insert(
            "INSERT INTO return_requests (order_id, order_item_id, customer_id, reason) VALUES (?,?,?,?)",
            'iiis',
            [$orderId, $orderItemId, $customerId, $reason]
        );
    }

    public function byCustomer(int $customerId): array
    {
        return Database::fetchAll(
            "SELECT rr.*, p.name AS product_name FROM return_requests rr
             JOIN order_items oi ON oi.id = rr.order_item_id JOIN products p ON p.id = oi.product_id
             WHERE rr.customer_id = ? ORDER BY rr.created_at DESC",
            'i',
            [$customerId]
        );
    }

    public function bySeller(int $sellerId): array
    {
        return Database::fetchAll(
            "SELECT rr.*, p.name AS product_name, u.name AS customer_name, oi.quantity, oi.unit_price
             FROM return_requests rr
             JOIN order_items oi ON oi.id = rr.order_item_id
             JOIN products p ON p.id = oi.product_id
             JOIN users u ON u.id = rr.customer_id
             WHERE oi.seller_id = ? ORDER BY rr.created_at DESC",
            'i',
            [$sellerId]
        );
    }

    public function find(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM return_requests WHERE id = ?", 'i', [$id]);
    }

    public function updateStatus(int $id, string $status, ?string $note = null): bool
    {
        Database::execute("UPDATE return_requests SET status=?, seller_note=? WHERE id=?", 'ssi', [$status, $note, $id]);
        return true;
    }

    public function alreadyRequested(int $orderItemId): bool
    {
        $row = Database::fetchOne("SELECT id FROM return_requests WHERE order_item_id = ?", 'i', [$orderItemId]);
        return $row !== null;
    }
}
