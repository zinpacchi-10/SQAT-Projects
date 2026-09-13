<?php
require_once __DIR__ . '/../core/Model.php';

class ReviewModel extends Model
{
    public function byProduct(int $productId): array
    {
        return Database::fetchAll(
            "SELECT r.*, u.name AS customer_name FROM reviews r JOIN users u ON u.id = r.customer_id
             WHERE r.product_id = ? ORDER BY r.created_at DESC",
            'i',
            [$productId]
        );
    }

    public function bySeller(int $sellerId): array
    {
        return Database::fetchAll(
            "SELECT r.*, u.name AS customer_name, p.name AS product_name
             FROM reviews r JOIN users u ON u.id = r.customer_id JOIN products p ON p.id = r.product_id
             WHERE p.seller_id = ? ORDER BY r.created_at DESC",
            'i',
            [$sellerId]
        );
    }

    public function byCustomer(int $customerId): array
    {
        return Database::fetchAll(
            "SELECT r.*, p.name AS product_name FROM reviews r JOIN products p ON p.id = r.product_id
             WHERE r.customer_id = ? ORDER BY r.created_at DESC",
            'i',
            [$customerId]
        );
    }

    public function find(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM reviews WHERE id = ?", 'i', [$id]);
    }

    public function alreadyReviewed(int $customerId, int $orderId, int $productId): bool
    {
        $row = Database::fetchOne(
            "SELECT id FROM reviews WHERE customer_id=? AND order_id=? AND product_id=?",
            'iii',
            [$customerId, $orderId, $productId]
        );
        return $row !== null;
    }

    public function create(int $productId, int $orderId, int $customerId, int $rating, string $text): int
    {
        return Database::insert(
            "INSERT INTO reviews (product_id, order_id, customer_id, rating, review_text) VALUES (?,?,?,?,?)",
            'iiiis',
            [$productId, $orderId, $customerId, $rating, $text]
        );
    }

    public function update(int $id, int $rating, string $text): bool
    {
        Database::execute("UPDATE reviews SET rating=?, review_text=? WHERE id=?", 'isi', [$rating, $text, $id]);
        return true;
    }

    public function delete(int $id): bool
    {
        Database::execute("DELETE FROM reviews WHERE id = ?", 'i', [$id]);
        return true;
    }

    public function reply(int $id, string $reply): bool
    {
        Database::execute("UPDATE reviews SET seller_reply = ? WHERE id = ?", 'si', [$reply, $id]);
        return true;
    }
}
