<?php
require_once __DIR__ . '/../core/Model.php';

class DeliveryZoneModel extends Model
{
    public function all(): array
    {
        return Database::fetchAll("SELECT * FROM delivery_zones ORDER BY zone_name ASC");
    }

    public function find(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM delivery_zones WHERE id = ?", 'i', [$id]);
    }

    public function create(string $name, float $fee, int $days): int
    {
        return Database::insert(
            "INSERT INTO delivery_zones (zone_name, delivery_fee, estimated_days) VALUES (?,?,?)",
            'sdi',
            [$name, $fee, $days]
        );
    }

    public function update(int $id, string $name, float $fee, int $days): bool
    {
        Database::execute(
            "UPDATE delivery_zones SET zone_name=?, delivery_fee=?, estimated_days=? WHERE id=?",
            'sdii',
            [$name, $fee, $days, $id]
        );
        return true;
    }

    public function delete(int $id): bool
    {
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM orders WHERE delivery_zone_id = ?", 'i', [$id]);
        if ((int)$row['c'] > 0) return false;
        Database::execute("DELETE FROM delivery_zones WHERE id = ?", 'i', [$id]);
        return true;
    }
}
