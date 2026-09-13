<?php
require_once __DIR__ . '/../core/Model.php';

class WishlistModel extends Model
{
    public function byCustomer(int $customerId): array
    {
        return Database::fetchAll(
            "SELECT w.*, p.name, p.price, p.primary_image_path, p.is_available, s.shop_name
             FROM wishlists w JOIN products p ON p.id = w.product_id JOIN sellers s ON s.id = p.seller_id
             WHERE w.customer_id = ? ORDER BY w.added_at DESC",
            'i',
            [$customerId]
        );
    }

    public function exists(int $customerId, int $productId): bool
    {
        $row = Database::fetchOne("SELECT id FROM wishlists WHERE customer_id=? AND product_id=?", 'ii', [$customerId, $productId]);
        return $row !== null;
    }

    public function add(int $customerId, int $productId): bool
    {
        if ($this->exists($customerId, $productId)) return true;
        Database::insert("INSERT INTO wishlists (customer_id, product_id) VALUES (?,?)", 'ii', [$customerId, $productId]);
        return true;
    }

    public function remove(int $customerId, int $productId): bool
    {
        Database::execute("DELETE FROM wishlists WHERE customer_id=? AND product_id=?", 'ii', [$customerId, $productId]);
        return true;
    }
}
