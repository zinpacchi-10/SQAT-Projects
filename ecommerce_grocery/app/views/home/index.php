<?php require APP_ROOT . '/app/views/layouts/header.php'; ?>
<div class="page container">
  <div class="hero">
    <h1>Fresh groceries, delivered to your door.</h1>
    <p>Shop from multiple trusted local sellers in one place.</p>
    <a href="<?= BASE_URL ?>/public/index.php?url=customer/products" class="btn" style="background:#fff;color:var(--green-dark);">Browse All Products</a>
  </div>

  <h3 class="section-title" style="margin-top:0;">Shop by Category</h3>
  <div class="cat-grid">
    <?php foreach ($categories as $c): ?>
      <a class="cat-card" href="<?= BASE_URL ?>/public/index.php?url=customer/products&category_id=<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></a>
    <?php endforeach; ?>
  </div>

  <h3 class="section-title">Featured Products</h3>
  <?php if (empty($featured)): ?>
    <div class="empty-state">No products available yet.</div>
  <?php else: ?>
    <div class="product-grid">
      <?php foreach ($featured as $p): require APP_ROOT . '/app/views/partials/product_card.php'; endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
