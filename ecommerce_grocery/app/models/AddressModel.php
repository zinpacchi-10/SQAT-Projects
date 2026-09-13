<?php
require_once __DIR__ . '/../core/Model.php';

class AddressModel extends Model
{
    public function byCustomer(int $customerId): array
    {
        return Database::fetchAll("SELECT * FROM addresses WHERE customer_id = ? ORDER BY is_default DESC, id DESC", 'i', [$customerId]);
    }

    public function find(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM addresses WHERE id = ?", 'i', [$id]);
    }

    public function create(int $customerId, string $label, string $fullAddress, string $city, bool $default): int
    {
        if ($default) {
            Database::execute("UPDATE addresses SET is_default = 0 WHERE customer_id = ?", 'i', [$customerId]);
        }
        return Database::insert(
            "INSERT INTO addresses (customer_id, label, full_address, city, is_default) VALUES (?,?,?,?,?)",
            'isssi',
            [$customerId, $label, $fullAddress, $city, $default ? 1 : 0]
        );
    }

    public function update(int $id, string $label, string $fullAddress, string $city): bool
    {
        Database::execute("UPDATE addresses SET label=?, full_address=?, city=? WHERE id=?", 'sssi', [$label, $fullAddress, $city, $id]);
        return true;
    }

    public function setDefault(int $id, int $customerId): bool
    {
        Database::execute("UPDATE addresses SET is_default = 0 WHERE customer_id = ?", 'i', [$customerId]);
        Database::execute("UPDATE addresses SET is_default = 1 WHERE id = ?", 'i', [$id]);
        return true;
    }

    public function delete(int $id): bool
    {
        Database::execute("DELETE FROM addresses WHERE id = ?", 'i', [$id]);
        return true;
    }
}
