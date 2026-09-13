<?php
require_once __DIR__ . '/../core/Model.php';

class ProductModel extends Model
{
    /** Public storefront browse with search / filter / sort */
    public function browse(array $f): array
    {
        $sql = "SELECT p.*, s.shop_name, c.name AS category_name,
                       (SELECT ROUND(AVG(rating),1) FROM reviews r WHERE r.product_id = p.id) AS avg_rating,
                       (SELECT COUNT(*) FROM reviews r WHERE r.product_id = p.id) AS review_count,
                       (SELECT COUNT(*) FROM order_items oi WHERE oi.product_id = p.id) AS popularity
                FROM products p
                JOIN sellers s ON s.id = p.seller_id
                JOIN categories c ON c.id = p.category_id
                WHERE p.is_available = 1 AND p.is_removed = 0 AND s.is_approved = 'approved'";
        $types = '';
        $params = [];

        if (!empty($f['keyword'])) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)";
            $like = '%' . $f['keyword'] . '%';
            $types .= 'sss';
            $params = array_merge($params, [$like, $like, $like]);
        }
        if (!empty($f['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $types .= 'i';
            $params[] = (int)$f['category_id'];
        }
        if (!empty($f['brand'])) {
            $sql .= " AND p.brand = ?";
            $types .= 's';
            $params[] = $f['brand'];
        }
        if (!empty($f['min_price'])) {
            $sql .= " AND p.price >= ?";
            $types .= 'd';
            $params[] = (float)$f['min_price'];
        }
        if (!empty($f['max_price'])) {
            $sql .= " AND p.price <= ?";
            $types .= 'd';
            $params[] = (float)$f['max_price'];
        }
        if (!empty($f['min_rating'])) {
            $sql .= " HAVING avg_rating >= ?";
            $types .= 'd';
            $params[] = (float)$f['min_rating'];
        }

        $sort = $f['sort'] ?? 'newest';
        $sql .= match ($sort) {
            'price_asc'  => " ORDER BY p.price ASC",
            'price_desc' => " ORDER BY p.price DESC",
            'rating'     => " ORDER BY avg_rating DESC",
            'popularity' => " ORDER BY popularity DESC",
            default      => " ORDER BY p.created_at DESC",
        };

        return Database::fetchAll($sql, $types, $params);
    }

    public function find(int $id): ?array
    {
        return Database::fetchOne(
            "SELECT p.*, s.shop_name, s.id AS seller_id, c.name AS category_name
             FROM products p JOIN sellers s ON s.id = p.seller_id JOIN categories c ON c.id = p.category_id
             WHERE p.id = ?",
            'i',
            [$id]
        );
    }

    public function images(int $productId): array
    {
        return Database::fetchAll("SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order ASC", 'i', [$productId]);
    }

    public function bySeller(int $sellerId): array
    {
        return Database::fetchAll(
            "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id
             WHERE p.seller_id = ? ORDER BY p.created_at DESC",
            'i',
            [$sellerId]
        );
    }

    public function insertProduct(int $sellerId, int $categoryId, string $name, string $desc, string $brand, string $unit,
                                   float $price, int $stock, int $reorder, ?string $expiry, ?string $image): int
    {
        return Database::insert(
            "INSERT INTO products (seller_id, category_id, name, description, brand, unit, price, stock_qty, reorder_level, expiry_date, primary_image_path)
             VALUES (?,?,?,?,?,?,?,?,?,?,?)",
            'iissssdiiss',
            [$sellerId, $categoryId, $name, $desc, $brand, $unit, $price, $stock, $reorder, $expiry, $image]
        );
    }

    public function updateProduct(int $id, int $categoryId, string $name, string $desc, string $brand, string $unit,
                                   float $price, int $stock, int $reorder, ?string $expiry): bool
    {
        Database::execute(
            "UPDATE products SET category_id=?, name=?, description=?, brand=?, unit=?, price=?, stock_qty=?, reorder_level=?, expiry_date=? WHERE id=?",
            'issssdiisi',
            [$categoryId, $name, $desc, $brand, $unit, $price, $stock, $reorder, $expiry, $id]
        );
        return true;
    }

    public function updateImage(int $id, string $path): bool
    {
        Database::execute("UPDATE products SET primary_image_path = ? WHERE id = ?", 'si', [$path, $id]);
        return true;
    }

    public function addExtraImage(int $productId, string $path, int $order): int
    {
        return Database::insert(
            "INSERT INTO product_images (product_id, image_path, display_order) VALUES (?,?,?)",
            'isi',
            [$productId, $path, $order]
        );
    }

    public function toggleAvailability(int $id): bool
    {
        Database::execute("UPDATE products SET is_available = NOT is_available WHERE id = ?", 'i', [$id]);
        return true;
    }

    public function hasPendingOrders(int $productId): bool
    {
        $row = Database::fetchOne(
            "SELECT COUNT(*) AS c FROM order_items WHERE product_id = ? AND item_status IN ('pending','confirmed','processing','shipped')",
            'i',
            [$productId]
        );
        return (int)$row['c'] > 0;
    }

    public function delete(int $id): bool
    {
        if ($this->hasPendingOrders($id)) {
            return false;
        }
        Database::execute("DELETE FROM products WHERE id = ?", 'i', [$id]);
        return true;
    }

    public function adminRemove(int $id): bool
    {
        Database::execute("UPDATE products SET is_removed = 1, is_available = 0 WHERE id = ?", 'i', [$id]);
        return true;
    }

    public function decrementStock(int $productId, int $qty): bool
    {
        Database::execute(
            "UPDATE products SET stock_qty = stock_qty - ? WHERE id = ? AND stock_qty >= ?",
            'iii',
            [$qty, $productId, $qty]
        );
        $p = $this->find($productId);
        return $p !== null;
    }

    public function incrementStock(int $productId, int $qty): bool
    {
        Database::execute("UPDATE products SET stock_qty = stock_qty + ? WHERE id = ?", 'ii', [$qty, $productId]);
        return true;
    }

    public function updateStock(int $productId, int $newQty): bool
    {
        if ($newQty < 0) return false;
        Database::execute("UPDATE products SET stock_qty = ? WHERE id = ?", 'ii', [$newQty, $productId]);
        return true;
    }

    public function lowStock(int $sellerId): array
    {
        return Database::fetchAll(
            "SELECT * FROM products WHERE seller_id = ? AND stock_qty <= reorder_level ORDER BY stock_qty ASC",
            'i',
            [$sellerId]
        );
    }

    public function distinctBrands(): array
    {
        $rows = Database::fetchAll("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand <> '' ORDER BY brand ASC");
        return array_column($rows, 'brand');
    }

    public function adminSearch(string $keyword = '', string $categoryId = '', string $sellerId = ''): array
    {
        $sql = "SELECT p.*, s.shop_name, c.name AS category_name FROM products p
                JOIN sellers s ON s.id = p.seller_id JOIN categories c ON c.id = p.category_id WHERE 1=1";
        $types = '';
        $params = [];
        if ($keyword !== '') {
            $sql .= " AND p.name LIKE ?";
            $types .= 's';
            $params[] = "%{$keyword}%";
        }
        if ($categoryId !== '') {
            $sql .= " AND p.category_id = ?";
            $types .= 'i';
            $params[] = (int)$categoryId;
        }
        if ($sellerId !== '') {
            $sql .= " AND p.seller_id = ?";
            $types .= 'i';
            $params[] = (int)$sellerId;
        }
        $sql .= " ORDER BY p.created_at DESC";
        return Database::fetchAll($sql, $types, $params);
    }

    public function featured(): array
    {
        return Database::fetchAll(
            "SELECT p.* FROM featured_products f JOIN products p ON p.id = f.product_id
             WHERE p.is_available = 1 AND p.is_removed = 0 ORDER BY f.added_at DESC LIMIT 8"
        );
    }

    public function addFeatured(int $productId): int
    {
        return Database::insert("INSERT INTO featured_products (product_id) VALUES (?)", 'i', [$productId]);
    }

    public function removeFeatured(int $productId): bool
    {
        Database::execute("DELETE FROM featured_products WHERE product_id = ?", 'i', [$productId]);
        return true;
    }
}
