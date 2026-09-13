<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="page-title">All Products</div>
    <form method="get" action="<?= BASE_URL ?>/public/index.php" class="filters-bar">
      <input type="hidden" name="url" value="admin/products">
      <div class="form-group"><label>Search</label><input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>"></div>
      <div class="form-group"><label>Category</label>
        <select name="category_id">
          <option value="">All</option>
          <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= $categoryId == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Seller</label>
        <select name="seller_id">
          <option value="">All</option>
          <?php foreach ($sellers as $s): ?><option value="<?= $s['id'] ?>" <?= $sellerId == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['shop_name']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <button class="btn btn-primary">Filter</button>
    </form>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No products found.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Name</th><th>Seller</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr>
          <?php foreach ($items as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['shop_name']) ?></td>
              <td><?= htmlspecialchars($p['category_name']) ?></td>
              <td>৳<?= number_format($p['price'], 2) ?></td>
              <td><?= $p['stock_qty'] ?></td>
              <td><?= $p['is_removed'] ? '<span class="badge badge-cancelled">Removed</span>' : ($p['is_available'] ? '<span class="badge badge-active">Live</span>' : '<span class="badge badge-pending">Hidden</span>') ?></td>
              <td><?php if (!$p['is_removed']): ?><a href="<?= BASE_URL ?>/public/index.php?url=admin/removeProduct/<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove this product from the marketplace?')">Remove</a><?php endif; ?></td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
