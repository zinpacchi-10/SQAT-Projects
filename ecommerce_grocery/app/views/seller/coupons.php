<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_seller.php'; ?>
  <div class="main-content">
    <div class="page-title">Promotional Coupons</div>
    <div class="card">
      <h3>Create Coupon</h3>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=seller/addCoupon" class="form-row" style="align-items:flex-end;">
        <div class="form-group"><label>Code</label><input type="text" name="code" placeholder="SAVE20" required></div>
        <div class="form-group"><label>Discount %</label><input type="number" step="0.01" name="discount_pct" required></div>
        <div class="form-group"><label>Max Uses</label><input type="number" name="max_uses" value="100"></div>
        <div class="form-group"><label>Min Order Amount</label><input type="number" step="0.01" name="min_order_amount" value="0"></div>
        <div class="form-group"><label>Valid Until</label><input type="date" name="valid_until" required></div>
        <button class="btn btn-primary">Create</button>
      </form>
    </div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No coupons created yet.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Code</th><th>Discount</th><th>Uses</th><th>Min Order</th><th>Valid Until</th><th>Status</th><th></th></tr>
          <?php foreach ($items as $c): ?>
            <tr>
              <td><strong><?= htmlspecialchars($c['code']) ?></strong></td>
              <td><?= $c['discount_pct'] ?>%</td>
              <td><?= $c['uses_count'] ?>/<?= $c['max_uses'] ?></td>
              <td>৳<?= number_format($c['min_order_amount'], 2) ?></td>
              <td><?= date('d M Y', strtotime($c['valid_until'])) ?></td>
              <td><span class="badge <?= $c['is_active'] ? 'badge-active' : 'badge-cancelled' ?>"><?= $c['is_active'] ? 'Active' : 'Inactive' ?></span></td>
              <td><a href="<?= BASE_URL ?>/public/index.php?url=seller/toggleCoupon/<?= $c['id'] ?>" class="btn btn-sm btn-secondary"><?= $c['is_active'] ? 'Deactivate' : 'Activate' ?></a></td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
