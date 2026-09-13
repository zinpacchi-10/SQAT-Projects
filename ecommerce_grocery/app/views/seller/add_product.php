<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_seller.php'; ?>
  <div class="main-content">
    <div class="page-title">Add New Product</div>
    <div class="card">
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><ul><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul></div>
      <?php endif; ?>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=seller/addProduct" enctype="multipart/form-data">
        <div class="form-row">
          <div class="form-group"><label>Product Name</label><input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required></div>
          <div class="form-group"><label>Brand</label><input type="text" name="brand" value="<?= htmlspecialchars($old['brand'] ?? '') ?>"></div>
        </div>
        <div class="form-group"><label>Description</label><textarea name="description"><?= htmlspecialchars($old['description'] ?? '') ?></textarea></div>
        <div class="form-row">
          <div class="form-group"><label>Category</label>
            <select name="category_id" required>
              <option value="">Select category</option>
              <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"><?= $c['parent_id'] ? '— ' : '' ?><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="form-group"><label>Unit (e.g. 1 kg, 500 ml)</label><input type="text" name="unit" value="<?= htmlspecialchars($old['unit'] ?? '') ?>" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Price (৳)</label><input type="number" step="0.01" name="price" value="<?= htmlspecialchars($old['price'] ?? '') ?>" required></div>
          <div class="form-group"><label>Stock Quantity</label><input type="number" name="stock_qty" value="<?= htmlspecialchars($old['stock_qty'] ?? '') ?>" required></div>
          <div class="form-group"><label>Reorder Level</label><input type="number" name="reorder_level" value="<?= htmlspecialchars($old['reorder_level'] ?? '5') ?>"></div>
        </div>
        <div class="form-group"><label>Expiry Date (if applicable)</label><input type="date" name="expiry_date"></div>
        <div class="form-group"><label>Primary Image</label><input type="file" name="primary_image" accept="image/*"></div>
        <div class="form-group"><label>Additional Images (up to 4)</label><input type="file" name="extra_images[]" accept="image/*" multiple></div>
        <button type="submit" class="btn btn-primary">Add Product</button>
      </form>
    </div>
  </div>
</div>
