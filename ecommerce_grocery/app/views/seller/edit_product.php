<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_seller.php'; ?>
  <div class="main-content">
    <div class="page-title">Edit Product</div>
    <div class="card">
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=seller/editProduct/<?= $product['id'] ?>" enctype="multipart/form-data">
        <div class="form-row">
          <div class="form-group"><label>Product Name</label><input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required></div>
          <div class="form-group"><label>Brand</label><input type="text" name="brand" value="<?= htmlspecialchars($product['brand']) ?>"></div>
        </div>
        <div class="form-group"><label>Description</label><textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea></div>
        <div class="form-row">
          <div class="form-group"><label>Category</label>
            <select name="category_id" required>
              <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= $c['id'] == $product['category_id'] ? 'selected' : '' ?>><?= $c['parent_id'] ? '— ' : '' ?><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="form-group"><label>Unit</label><input type="text" name="unit" value="<?= htmlspecialchars($product['unit']) ?>" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Price (৳)</label><input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required></div>
          <div class="form-group"><label>Stock Quantity</label><input type="number" name="stock_qty" value="<?= $product['stock_qty'] ?>" required></div>
          <div class="form-group"><label>Reorder Level</label><input type="number" name="reorder_level" value="<?= $product['reorder_level'] ?>"></div>
        </div>
        <div class="form-group"><label>Expiry Date</label><input type="date" name="expiry_date" value="<?= $product['expiry_date'] ?>"></div>

        <?php if ($product['primary_image_path']): ?>
          <div class="form-group"><label>Current Primary Image</label><img src="<?= UPLOAD_URL . '/' . htmlspecialchars($product['primary_image_path']) ?>" style="width:100px;border-radius:8px;"></div>
        <?php endif; ?>
        <div class="form-group"><label>Replace Primary Image</label><input type="file" name="primary_image" accept="image/*"></div>

        <?php if (!empty($images)): ?>
          <div class="form-group"><label>Additional Images</label>
            <div class="img-thumb-row"><?php foreach ($images as $img): ?><img src="<?= UPLOAD_URL . '/' . htmlspecialchars($img['image_path']) ?>"><?php endforeach; ?></div>
          </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Update Product</button>
      </form>
    </div>
  </div>
</div>
