<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_seller.php'; ?>
  <div class="main-content">
    <div class="page-title">Shop Profile</div>
    <div class="card">
      <?php if ($seller['shop_logo_path']): ?>
        <img src="<?= UPLOAD_URL . '/' . htmlspecialchars($seller['shop_logo_path']) ?>" style="width:90px;height:90px;object-fit:cover;border-radius:10px;margin-bottom:12px;">
      <?php endif; ?>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=seller/updateProfile" enctype="multipart/form-data">
        <div class="form-group"><label>Shop Name</label><input type="text" name="shop_name" value="<?= htmlspecialchars($seller['shop_name']) ?>" required></div>
        <div class="form-group"><label>Shop Description</label><textarea name="shop_description"><?= htmlspecialchars($seller['shop_description']) ?></textarea></div>
        <div class="form-group"><label>Address</label><input type="text" name="address" value="<?= htmlspecialchars($seller['address']) ?>" required></div>
        <div class="form-group"><label>Shop Logo</label><input type="file" name="shop_logo" accept="image/*"></div>
        <button class="btn btn-primary">Save Changes</button>
      </form>
      <div class="mt-16 text-muted">Approval status: <span class="badge badge-<?= $seller['is_approved'] ?>"><?= $seller['is_approved'] ?></span> &middot; Commission rate: <?= $seller['commission_rate'] ?>%</div>
    </div>
  </div>
</div>
