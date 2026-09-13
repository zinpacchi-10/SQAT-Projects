<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="page-title">Featured Products (Homepage)</div>
    <div class="card">
      <h3>Add to Featured</h3>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=admin/addFeatured" class="flex gap-8">
        <select name="product_id" style="flex:1;">
          <?php foreach ($allProducts as $p): ?><option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['shop_name']) ?>)</option><?php endforeach; ?>
        </select>
        <button class="btn btn-primary">Add</button>
      </form>
    </div>
    <div class="card">
      <?php if (empty($featured)): ?>
        <div class="empty-state">No featured products set. Newest products will show on the homepage by default.</div>
      <?php else: ?>
        <div class="product-grid">
          <?php foreach ($featured as $p): ?>
            <div class="product-card">
              <div class="thumb"><?php if ($p['primary_image_path']): ?><img src="<?= UPLOAD_URL . '/' . htmlspecialchars($p['primary_image_path']) ?>"><?php else: ?>🛒<?php endif; ?></div>
              <div class="body">
                <div class="name"><?= htmlspecialchars($p['name']) ?></div>
                <div class="price">৳<?= number_format($p['price'], 2) ?></div>
              </div>
              <div style="padding:0 14px 14px;"><a href="<?= BASE_URL ?>/public/index.php?url=admin/removeFeatured/<?= $p['id'] ?>" class="btn btn-danger btn-sm btn-block">Remove</a></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
