<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_customer.php'; ?>
  <div class="main-content">
    <div class="page-title">My Wishlist</div>
    <?php if (empty($items)): ?>
      <div class="empty-state">Your wishlist is empty. <a href="<?= BASE_URL ?>/public/index.php?url=customer/products">Browse products</a>.</div>
    <?php else: ?>
      <div class="product-grid">
        <?php foreach ($items as $w): ?>
          <div class="product-card">
            <a href="<?= BASE_URL ?>/public/index.php?url=customer/productDetail/<?= $w['product_id'] ?>">
              <div class="thumb"><?php if ($w['primary_image_path']): ?><img src="<?= UPLOAD_URL . '/' . htmlspecialchars($w['primary_image_path']) ?>"><?php else: ?>🛒<?php endif; ?></div>
              <div class="body">
                <div class="name"><?= htmlspecialchars($w['name']) ?></div>
                <div class="shop"><?= htmlspecialchars($w['shop_name']) ?></div>
                <div class="price">৳<?= number_format($w['price'], 2) ?></div>
              </div>
            </a>
            <div style="padding:0 14px 14px;">
              <button class="btn btn-danger btn-sm btn-block remove-wish" data-id="<?= $w['product_id'] ?>">Remove</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
<script>
document.querySelectorAll('.remove-wish').forEach(btn => {
  btn.addEventListener('click', function () {
    ajaxPost('<?= BASE_URL ?>/public/index.php?url=customer/toggleWishlist', { product_id: this.dataset.id }, function () {
      btn.closest('.product-card').remove();
    });
  });
});
</script>
