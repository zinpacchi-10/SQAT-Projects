<?php /** Expects $p (product row, possibly with avg_rating) */ ?>
<a href="<?= BASE_URL ?>/public/index.php?url=customer/productDetail/<?= $p['id'] ?>" class="product-card">
  <div class="thumb">
    <?php if (!empty($p['primary_image_path'])): ?>
      <img src="<?= UPLOAD_URL . '/' . htmlspecialchars($p['primary_image_path']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
    <?php else: ?>🛒<?php endif; ?>
  </div>
  <div class="body">
    <div class="name"><?= htmlspecialchars($p['name']) ?></div>
    <div class="shop"><?= htmlspecialchars($p['shop_name'] ?? '') ?> &middot; <?= htmlspecialchars($p['unit']) ?></div>
    <?php if (!empty($p['avg_rating'])): ?>
      <div class="rating">★ <?= $p['avg_rating'] ?> <span class="text-muted">(<?= $p['review_count'] ?? 0 ?>)</span></div>
    <?php endif; ?>
    <div class="price">৳<?= number_format($p['price'], 2) ?></div>
  </div>
</a>
