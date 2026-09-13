<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="page-title"><?= htmlspecialchars($seller['shop_name']) ?></div>
    <div class="card">
      <table>
        <tr><td class="text-muted">Owner</td><td><?= htmlspecialchars($seller['owner_name']) ?></td></tr>
        <tr><td class="text-muted">Email</td><td><?= htmlspecialchars($seller['email']) ?></td></tr>
        <tr><td class="text-muted">Phone</td><td><?= htmlspecialchars($seller['phone']) ?></td></tr>
        <tr><td class="text-muted">Address</td><td><?= htmlspecialchars($seller['address']) ?></td></tr>
        <tr><td class="text-muted">Status</td><td><span class="badge badge-<?= $seller['is_approved'] ?>"><?= $seller['is_approved'] ?></span></td></tr>
        <tr><td class="text-muted">Description</td><td><?= htmlspecialchars($seller['shop_description']) ?></td></tr>
      </table>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=admin/updateCommission" class="flex gap-8 mt-16" style="align-items:flex-end;">
        <input type="hidden" name="seller_id" value="<?= $seller['id'] ?>">
        <div class="form-group"><label>Commission Rate (%)</label><input type="number" step="0.01" name="commission_rate" value="<?= $seller['commission_rate'] ?>"></div>
        <button class="btn btn-primary">Update</button>
      </form>
    </div>
    <div class="card">
      <h3>Products (<?= count($products) ?>)</h3>
      <?php if (empty($products)): ?><div class="empty-state">No products listed.</div><?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Name</th><th>Price</th><th>Stock</th><th>Status</th></tr>
          <?php foreach ($products as $p): ?>
            <tr><td><?= htmlspecialchars($p['name']) ?></td><td>৳<?= number_format($p['price'], 2) ?></td><td><?= $p['stock_qty'] ?></td><td><?= $p['is_available'] ? 'Available' : 'Hidden' ?></td></tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
