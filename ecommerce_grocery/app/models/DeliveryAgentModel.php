<?php
require_once __DIR__ . '/../core/Model.php';

class DeliveryAgentModel extends Model
{
    public function all(): array
    {
        return Database::fetchAll(
            "SELECT a.*, (SELECT COUNT(*) FROM delivery_assignments da WHERE da.agent_id = a.id AND da.status IN ('assigned','picked_up','in_transit')) AS active_count
             FROM delivery_agents a ORDER BY a.created_at DESC"
        );
    }

    public function find(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM delivery_agents WHERE id = ?", 'i', [$id]);
    }

    public function activeAgents(): array
    {
        return Database::fetchAll("SELECT * FROM delivery_agents WHERE is_active = 1 ORDER BY name ASC");
    }

    public function create(string $name, string $vehicle, string $phone): int
    {
        return Database::insert(
            "INSERT INTO delivery_agents (name, vehicle_type, phone) VALUES (?,?,?)",
            'sss',
            [$name, $vehicle, $phone]
        );
    }

    public function update(int $id, string $name, string $vehicle, string $phone): bool
    {
        Database::execute("UPDATE delivery_agents SET name=?, vehicle_type=?, phone=? WHERE id=?", 'sssi', [$name, $vehicle, $phone, $id]);
        return true;
    }

    public function toggleActive(int $id): bool
    {
        Database::execute("UPDATE delivery_agents SET is_active = NOT is_active WHERE id = ?", 'i', [$id]);
        return true;
    }

    public function performanceReport(): array
    {
        return Database::fetchAll(
            "SELECT a.id, a.name,
                    COUNT(CASE WHEN da.status = 'delivered' THEN 1 END) AS delivered_count,
                    COUNT(CASE WHEN da.status = 'failed' THEN 1 END) AS failed_count,
                    COUNT(da.id) AS total_assignments,
                    ROUND(AVG(CASE WHEN da.status = 'delivered' THEN TIMESTAMPDIFF(HOUR, da.assigned_at, da.delivered_at) END),1) AS avg_hours
             FROM delivery_agents a LEFT JOIN delivery_assignments da ON da.agent_id = a.id
             GROUP BY a.id ORDER BY delivered_count DESC"
        );
    }
}
