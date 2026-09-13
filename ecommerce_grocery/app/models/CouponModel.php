<?php
require_once __DIR__ . '/../core/Model.php';

class CouponModel extends Model
{
    public function bySeller(int $sellerId): array
    {
        return Database::fetchAll("SELECT * FROM coupons WHERE seller_id = ? ORDER BY id DESC", 'i', [$sellerId]);
    }

    public function platformCoupons(): array
    {
        return Database::fetchAll("SELECT * FROM coupons WHERE created_by_admin = 1 ORDER BY id DESC");
    }

    public function findByCode(string $code): ?array
    {
        return Database::fetchOne("SELECT * FROM coupons WHERE code = ?", 's', [$code]);
    }

    public function findById(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM coupons WHERE id = ?", 'i', [$id]);
    }

    public function create(?int $sellerId, bool $byAdmin, string $code, float $pct, int $maxUses, float $minOrder, string $validUntil): int
    {
        return Database::insert(
            "INSERT INTO coupons (seller_id, created_by_admin, code, discount_pct, max_uses, min_order_amount, valid_until) VALUES (?,?,?,?,?,?,?)",
            'iisdids',
            [$sellerId, $byAdmin ? 1 : 0, $code, $pct, $maxUses, $minOrder, $validUntil]
        );
    }

    public function toggleActive(int $id): bool
    {
        Database::execute("UPDATE coupons SET is_active = NOT is_active WHERE id = ?", 'i', [$id]);
        return true;
    }

    public function incrementUse(int $id): bool
    {
        Database::execute("UPDATE coupons SET uses_count = uses_count + 1 WHERE id = ?", 'i', [$id]);
        return true;
    }

    /**
     * Validate a coupon for a given order subtotal.
     * Returns ['valid'=>bool, 'message'=>string, 'discount'=>float, 'coupon'=>array|null]
     */
    public function validate(string $code, float $subtotal): array
    {
        $c = $this->findByCode($code);
        if (!$c) {
            return ['valid' => false, 'message' => 'Invalid coupon code.', 'discount' => 0, 'coupon' => null];
        }
        if (!$c['is_active']) {
            return ['valid' => false, 'message' => 'This coupon is no longer active.', 'discount' => 0, 'coupon' => null];
        }
        if (strtotime($c['valid_until']) < strtotime(date('Y-m-d'))) {
            return ['valid' => false, 'message' => 'This coupon has expired.', 'discount' => 0, 'coupon' => null];
        }
        if ($c['uses_count'] >= $c['max_uses']) {
            return ['valid' => false, 'message' => 'This coupon has reached its usage limit.', 'discount' => 0, 'coupon' => null];
        }
        if ($subtotal < $c['min_order_amount']) {
            return ['valid' => false, 'message' => 'Minimum order amount of ৳' . number_format($c['min_order_amount'], 2) . ' required.', 'discount' => 0, 'coupon' => null];
        }
        $discount = round($subtotal * ((float)$c['discount_pct'] / 100), 2);
        return ['valid' => true, 'message' => 'Coupon applied successfully!', 'discount' => $discount, 'coupon' => $c];
    }
}
