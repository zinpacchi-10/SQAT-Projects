<?php
require_once __DIR__ . '/../core/Model.php';

class OrderModel extends Model
{
    /**
     * Place an order transactionally: validates stock for every item,
     * decrements stock, creates the order + order_items rows.
     * $items: array of ['product_id'=>, 'quantity'=>, 'unit_price'=>, 'seller_id'=>]
     * Returns ['success'=>bool, 'order_id'=>int|null, 'message'=>string]
     */
    public function placeOrder(int $customerId, string $address, int $zoneId, string $paymentMethod,
                                float $subtotal, float $deliveryFee, float $discount, float $total,
                                ?int $couponId, array $items): array
    {
        $this->db->begin_transaction();
        try {
            // Re-check stock for every item before committing (prevents negative stock / race conditions)
            foreach ($items as $item) {
                $product = Database::fetchOne("SELECT stock_qty, is_available FROM products WHERE id = ? FOR UPDATE", 'i', [$item['product_id']]);
                if (!$product || !$product['is_available']) {
                    throw new Exception("Product #{$item['product_id']} is no longer available.");
                }
                if ($product['stock_qty'] < $item['quantity']) {
                    throw new Exception("Insufficient stock for one of the items in your cart.");
                }
            }

            $orderId = Database::insert(
                "INSERT INTO orders (customer_id, shipping_address, delivery_zone_id, payment_method, subtotal, delivery_fee, discount_amount, total_amount, coupon_id)
                 VALUES (?,?,?,?,?,?,?,?,?)",
                'isisddddi',
                [$customerId, $address, $zoneId, $paymentMethod, $subtotal, $deliveryFee, $discount, $total, $couponId]
            );

            foreach ($items as $item) {
                Database::insert(
                    "INSERT INTO order_items (order_id, product_id, seller_id, quantity, unit_price) VALUES (?,?,?,?,?)",
                    'iiiid',
                    [$orderId, $item['product_id'], $item['seller_id'], $item['quantity'], $item['unit_price']]
                );
                Database::execute(
                    "UPDATE products SET stock_qty = stock_qty - ? WHERE id = ? AND stock_qty >= ?",
                    'iii',
                    [$item['quantity'], $item['product_id'], $item['quantity']]
                );
            }

            $this->db->commit();
            return ['success' => true, 'order_id' => $orderId, 'message' => 'Order placed successfully.'];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'order_id' => null, 'message' => $e->getMessage()];
        }
    }

    public function find(int $id): ?array
    {
        return Database::fetchOne(
            "SELECT o.*, z.zone_name, z.estimated_days, u.name AS customer_name, u.phone AS customer_phone
             FROM orders o JOIN delivery_zones z ON z.id = o.delivery_zone_id
             JOIN users u ON u.id = o.customer_id WHERE o.id = ?",
            'i',
            [$id]
        );
    }

    public function items(int $orderId): array
    {
        return Database::fetchAll(
            "SELECT oi.*, p.name AS product_name, p.primary_image_path, s.shop_name
             FROM order_items oi JOIN products p ON p.id = oi.product_id JOIN sellers s ON s.id = oi.seller_id
             WHERE oi.order_id = ?",
            'i',
            [$orderId]
        );
    }

    public function itemsBySeller(int $orderId, int $sellerId): array
    {
        return Database::fetchAll(
            "SELECT oi.*, p.name AS product_name FROM order_items oi JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ? AND oi.seller_id = ?",
            'ii',
            [$orderId, $sellerId]
        );
    }

    public function byCustomer(int $customerId): array
    {
        return Database::fetchAll("SELECT o.*, z.zone_name FROM orders o JOIN delivery_zones z ON z.id=o.delivery_zone_id WHERE customer_id = ? ORDER BY o.created_at DESC", 'i', [$customerId]);
    }

    public function updateStatus(int $orderId, string $status): bool
    {
        Database::execute("UPDATE orders SET status = ? WHERE id = ?", 'si', [$status, $orderId]);
        return true;
    }

    public function updateItemStatus(int $itemId, string $status, ?string $note = null): bool
    {
        if ($note !== null) {
            Database::execute("UPDATE order_items SET item_status = ?, tracking_note = ? WHERE id = ?", 'ssi', [$status, $note, $itemId]);
        } else {
            Database::execute("UPDATE order_items SET item_status = ? WHERE id = ?", 'si', [$status, $itemId]);
        }
        $this->syncOrderStatusFromItems($itemId);
        return true;
    }

    /** Recompute the parent order's overall status from its item statuses */
    private function syncOrderStatusFromItems(int $itemId): void
    {
        $item = Database::fetchOne("SELECT order_id FROM order_items WHERE id = ?", 'i', [$itemId]);
        if (!$item) return;
        $orderId = $item['order_id'];
        $statuses = array_column(Database::fetchAll("SELECT item_status FROM order_items WHERE order_id = ?", 'i', [$orderId]), 'item_status');
        if (empty($statuses)) return;

        if (count(array_unique($statuses)) === 1) {
            $this->updateStatus($orderId, $statuses[0]);
        } elseif (in_array('shipped', $statuses, true) || in_array('processing', $statuses, true)) {
            $this->updateStatus($orderId, 'processing');
        } elseif (in_array('confirmed', $statuses, true)) {
            $this->updateStatus($orderId, 'confirmed');
        }
    }

    public function itemById(int $itemId): ?array
    {
        return Database::fetchOne("SELECT * FROM order_items WHERE id = ?", 'i', [$itemId]);
    }

    public function sellerOrders(int $sellerId, string $statusFilter = ''): array
    {
        $sql = "SELECT oi.*, p.name AS product_name, o.created_at AS order_date, o.status AS order_status, o.shipping_address, o.payment_method, u.name AS customer_name
                FROM order_items oi
                JOIN orders o ON o.id = oi.order_id
                JOIN users u ON u.id = o.customer_id
                JOIN products p ON p.id = oi.product_id
                WHERE oi.seller_id = ?";
        $types = 'i';
        $params = [$sellerId];
        if ($statusFilter !== '') {
            $sql .= " AND oi.item_status = ?";
            $types .= 's';
            $params[] = $statusFilter;
        }
        $sql .= " ORDER BY o.created_at DESC";
        return Database::fetchAll($sql, $types, $params);
    }

    public function canCancel(array $order): bool
    {
        return in_array($order['status'], ['pending', 'confirmed'], true);
    }

    public function cancel(int $orderId): bool
    {
        $items = $this->items($orderId);
        foreach ($items as $item) {
            Database::execute("UPDATE products SET stock_qty = stock_qty + ? WHERE id = ?", 'ii', [$item['quantity'], $item['product_id']]);
        }
        $this->updateStatus($orderId, 'cancelled');
        Database::execute("UPDATE order_items SET item_status = 'cancelled' WHERE order_id = ?", 'i', [$orderId]);
        return true;
    }

    public function allWithFilters(array $f): array
    {
        $sql = "SELECT o.*, u.name AS customer_name, z.zone_name FROM orders o
                JOIN users u ON u.id = o.customer_id JOIN delivery_zones z ON z.id = o.delivery_zone_id WHERE 1=1";
        $types = '';
        $params = [];
        if (!empty($f['status'])) {
            $sql .= " AND o.status = ?"; $types .= 's'; $params[] = $f['status'];
        }
        if (!empty($f['date_from'])) {
            $sql .= " AND o.created_at >= ?"; $types .= 's'; $params[] = $f['date_from'] . ' 00:00:00';
        }
        if (!empty($f['date_to'])) {
            $sql .= " AND o.created_at <= ?"; $types .= 's'; $params[] = $f['date_to'] . ' 23:59:59';
        }
        if (!empty($f['customer'])) {
            $sql .= " AND u.name LIKE ?"; $types .= 's'; $params[] = '%' . $f['customer'] . '%';
        }
        $sql .= " ORDER BY o.created_at DESC LIMIT 300";
        return Database::fetchAll($sql, $types, $params);
    }

    public function readyForDispatch(string $zoneFilter = ''): array
    {
        $sql = "SELECT DISTINCT o.*, z.zone_name, u.name AS customer_name FROM orders o
                JOIN delivery_zones z ON z.id = o.delivery_zone_id
                JOIN users u ON u.id = o.customer_id
                WHERE o.status IN ('processing','shipped')
                AND o.id NOT IN (SELECT order_id FROM delivery_assignments WHERE status NOT IN ('failed'))";
        $types = '';
        $params = [];
        if ($zoneFilter !== '') {
            $sql .= " AND z.id = ?";
            $types .= 'i';
            $params[] = (int)$zoneFilter;
        }
        $sql .= " ORDER BY o.created_at ASC";
        return Database::fetchAll($sql, $types, $params);
    }

    public function revenueThisMonth(): float
    {
        $row = Database::fetchOne(
            "SELECT COALESCE(SUM(total_amount),0) AS total FROM orders WHERE status != 'cancelled' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())"
        );
        return (float)($row['total'] ?? 0);
    }

    public function countToday(): int
    {
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM orders WHERE DATE(created_at) = CURDATE()");
        return (int)($row['c'] ?? 0);
    }

    public function sellerRevenue(int $sellerId, string $period = 'month'): array
    {
        $having = match ($period) {
            'week' => "o.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)",
            'year' => "YEAR(o.created_at) = YEAR(CURDATE())",
            default => "MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())",
        };
        $row = Database::fetchOne(
            "SELECT COALESCE(SUM(oi.quantity * oi.unit_price),0) AS revenue, COUNT(DISTINCT oi.order_id) AS orders_count,
                    COALESCE(AVG(oi.quantity * oi.unit_price),0) AS aov
             FROM order_items oi JOIN orders o ON o.id = oi.order_id
             WHERE oi.seller_id = ? AND oi.item_status != 'cancelled' AND {$having}",
            'i',
            [$sellerId]
        );
        return $row ?: ['revenue' => 0, 'orders_count' => 0, 'aov' => 0];
    }

    public function topSellingProducts(int $sellerId, int $limit = 5): array
    {
        return Database::fetchAll(
            "SELECT p.name, SUM(oi.quantity) AS total_qty, SUM(oi.quantity*oi.unit_price) AS total_revenue
             FROM order_items oi JOIN products p ON p.id = oi.product_id
             WHERE oi.seller_id = ? AND oi.item_status != 'cancelled'
             GROUP BY oi.product_id ORDER BY total_qty DESC LIMIT ?",
            'ii',
            [$sellerId, $limit]
        );
    }

    public function orderVolumeByDay(int $sellerId, int $days = 7): array
    {
        return Database::fetchAll(
            "SELECT DATE(o.created_at) AS day, COUNT(DISTINCT o.id) AS orders_count
             FROM order_items oi JOIN orders o ON o.id = oi.order_id
             WHERE oi.seller_id = ? AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             GROUP BY DATE(o.created_at) ORDER BY day ASC",
            'ii',
            [$sellerId, $days]
        );
    }

    public function topSellersPlatform(int $limit = 5): array
    {
        return Database::fetchAll(
            "SELECT s.shop_name, SUM(oi.quantity*oi.unit_price) AS revenue
             FROM order_items oi JOIN sellers s ON s.id = oi.seller_id
             WHERE oi.item_status != 'cancelled'
             GROUP BY oi.seller_id ORDER BY revenue DESC LIMIT ?",
            'i',
            [$limit]
        );
    }

    public function topCategoriesPlatform(int $limit = 5): array
    {
        return Database::fetchAll(
            "SELECT c.name, SUM(oi.quantity*oi.unit_price) AS revenue
             FROM order_items oi JOIN products p ON p.id = oi.product_id JOIN categories c ON c.id = p.category_id
             WHERE oi.item_status != 'cancelled'
             GROUP BY p.category_id ORDER BY revenue DESC LIMIT ?",
            'i',
            [$limit]
        );
    }

    public function markAllItemsStatus(int $orderId, string $status): bool
    {
        Database::execute("UPDATE order_items SET item_status = ? WHERE order_id = ?", 'si', [$status, $orderId]);
        return true;
    }
}
