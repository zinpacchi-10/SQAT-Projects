<?php
require_once __DIR__ . '/../core/Model.php';

class CategoryModel extends Model
{
    public function all(): array
    {
        return Database::fetchAll("SELECT * FROM categories ORDER BY parent_id IS NULL DESC, name ASC");
    }

    public function topLevel(): array
    {
        return Database::fetchAll("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
    }

    public function children(int $parentId): array
    {
        return Database::fetchAll("SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC", 'i', [$parentId]);
    }

    public function find(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM categories WHERE id = ?", 'i', [$id]);
    }

    public function create(?int $parentId, string $name, string $description): int
    {
        return Database::insert(
            "INSERT INTO categories (parent_id, name, description) VALUES (?,?,?)",
            'iss',
            [$parentId, $name, $description]
        );
    }

    public function update(int $id, ?int $parentId, string $name, string $description): bool
    {
        Database::execute(
            "UPDATE categories SET parent_id = ?, name = ?, description = ? WHERE id = ?",
            'issi',
            [$parentId, $name, $description, $id]
        );
        return true;
    }

    public function delete(int $id): bool
    {
        $count = Database::fetchOne("SELECT COUNT(*) AS c FROM products WHERE category_id = ?", 'i', [$id]);
        if ((int)$count['c'] > 0) {
            return false; // blocked: products exist in this category
        }
        Database::execute("DELETE FROM categories WHERE id = ?", 'i', [$id]);
        return true;
    }
}
