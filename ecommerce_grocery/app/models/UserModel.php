<?php
require_once __DIR__ . '/../core/Model.php';

class UserModel extends Model
{
    public function findByEmail(string $email): ?array
    {
        return Database::fetchOne("SELECT * FROM users WHERE email = ?", 's', [$email]);
    }

    public function findById(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM users WHERE id = ?", 'i', [$id]);
    }

    public function create(string $name, string $email, string $passwordHash, string $phone, string $role): int
    {
        return Database::insert(
            "INSERT INTO users (name, email, password_hash, phone, role) VALUES (?,?,?,?,?)",
            'sssss',
            [$name, $email, $passwordHash, $phone, $role]
        );
    }

    public function updateProfile(int $id, string $name, string $phone): bool
    {
        Database::execute("UPDATE users SET name = ?, phone = ? WHERE id = ?", 'ssi', [$name, $phone, $id]);
        return true;
    }

    public function updateProfilePic(int $id, string $path): bool
    {
        Database::execute("UPDATE users SET profile_pic = ? WHERE id = ?", 'si', [$path, $id]);
        return true;
    }

    public function updatePassword(int $id, string $passwordHash): bool
    {
        Database::execute("UPDATE users SET password_hash = ? WHERE id = ?", 'si', [$passwordHash, $id]);
        return true;
    }

    public function listByRole(string $role, string $search = ''): array
    {
        if ($search !== '') {
            $like = "%{$search}%";
            return Database::fetchAll(
                "SELECT * FROM users WHERE role = ? AND (name LIKE ? OR email LIKE ?) ORDER BY created_at DESC",
                'sss',
                [$role, $like, $like]
            );
        }
        return Database::fetchAll("SELECT * FROM users WHERE role = ? ORDER BY created_at DESC", 's', [$role]);
    }

    public function setActive(int $id, int $active): bool
    {
        Database::execute("UPDATE users SET is_active = ? WHERE id = ?", 'ii', [$active, $id]);
        return true;
    }

    public function countByRole(string $role): int
    {
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM users WHERE role = ?", 's', [$role]);
        return (int)($row['c'] ?? 0);
    }
}
