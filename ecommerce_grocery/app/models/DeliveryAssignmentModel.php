<?php
require_once __DIR__ . '/../core/Model.php';

class DeliveryAssignmentModel extends Model
{
    public function create(int $orderId, int $agentId, string $zone): int
    {
        return Database::insert(
            "INSERT INTO delivery_assignments (order_id, agent_id, delivery_zone) VALUES (?,?,?)",
            'iis',
            [$orderId, $agentId, $zone]
        );
    }

    public function find(int $id): ?array
    {
        return Database::fetchOne(
            "SELECT da.*, a.name AS agent_name, a.phone AS agent_phone, o.customer_id, u.name AS customer_name
             FROM delivery_assignments da JOIN delivery_agents a ON a.id = da.agent_id
             JOIN orders o ON o.id = da.order_id JOIN users u ON u.id = o.customer_id
             WHERE da.id = ?",
            'i',
            [$id]
        );
    }

    public function active(): array
    {
        return Database::fetchAll(
            "SELECT da.*, a.name AS agent_name, o.id AS order_id, z.zone_name, u.name AS customer_name
             FROM delivery_assignments da
             JOIN delivery_agents a ON a.id = da.agent_id
             JOIN orders o ON o.id = da.order_id
             JOIN delivery_zones z ON z.id = o.delivery_zone_id
             JOIN users u ON u.id = o.customer_id
             WHERE da.status IN ('assigned','picked_up','in_transit')
             ORDER BY da.assigned_at ASC"
        );
    }

    public function history(): array
    {
        return Database::fetchAll(
            "SELECT da.*, a.name AS agent_name, o.id AS order_id, z.zone_name, u.name AS customer_name
             FROM delivery_assignments da
             JOIN delivery_agents a ON a.id = da.agent_id
             JOIN orders o ON o.id = da.order_id
             JOIN delivery_zones z ON z.id = o.delivery_zone_id
             JOIN users u ON u.id = o.customer_id
             WHERE da.status IN ('delivered','failed')
             ORDER BY da.assigned_at DESC LIMIT 200"
        );
    }

    public function updateStatus(int $id, string $status, ?string $failedReason = null): bool
    {
        if ($status === 'delivered') {
            Database::execute("UPDATE delivery_assignments SET status=?, delivered_at = NOW() WHERE id=?", 'si', [$status, $id]);
        } elseif ($status === 'failed') {
            Database::execute("UPDATE delivery_assignments SET status=?, failed_reason=? WHERE id=?", 'ssi', [$status, $failedReason, $id]);
        } else {
            Database::execute("UPDATE delivery_assignments SET status=? WHERE id=?", 'si', [$status, $id]);
        }
        return true;
    }

    public function countByStatus(string $status): int
    {
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM delivery_assignments WHERE status = ?", 's', [$status]);
        return (int)($row['c'] ?? 0);
    }

    public function deliveredToday(): int
    {
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM delivery_assignments WHERE status = 'delivered' AND DATE(delivered_at) = CURDATE()");
        return (int)($row['c'] ?? 0);
    }

    public function summary(string $period = 'day'): array
    {
        $interval = $period === 'week' ? '7 DAY' : '1 DAY';
        $row = Database::fetchOne(
            "SELECT
                COUNT(CASE WHEN status='delivered' THEN 1 END) AS delivered,
                COUNT(CASE WHEN status='failed' THEN 1 END) AS failed,
                COUNT(CASE WHEN status IN ('assigned','picked_up','in_transit') THEN 1 END) AS in_transit
             FROM delivery_assignments WHERE assigned_at >= DATE_SUB(NOW(), INTERVAL {$interval})"
        );
        return $row ?: ['delivered' => 0, 'failed' => 0, 'in_transit' => 0];
    }

    public function zonePerformance(): array
    {
        return Database::fetchAll(
            "SELECT z.zone_name, COUNT(da.id) AS total_deliveries,
                    ROUND(AVG(CASE WHEN da.status='delivered' THEN TIMESTAMPDIFF(HOUR, da.assigned_at, da.delivered_at) END),1) AS avg_hours
             FROM delivery_zones z LEFT JOIN orders o ON o.delivery_zone_id = z.id
             LEFT JOIN delivery_assignments da ON da.order_id = o.id
             GROUP BY z.id ORDER BY total_deliveries DESC"
        );
    }

    public function byOrder(int $orderId): ?array
    {
        return Database::fetchOne("SELECT * FROM delivery_assignments WHERE order_id = ? ORDER BY id DESC LIMIT 1", 'i', [$orderId]);
    }
}
