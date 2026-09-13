<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="page-title">All Orders</div>
    <form method="get" action="<?= BASE_URL ?>/public/index.php" class="filters-bar">
      <input type="hidden" name="url" value="admin/orders">
      <div class="form-group"><label>Status</label>
        <select name="status">
          <option value="">All</option>
          <?php foreach (['pending','confirmed','processing','shipped','delivered','cancelled','return_requested','returned'] as $s): ?>
            <option value="<?= $s ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Customer</label><input type="text" name="customer" value="<?= htmlspecialchars($filters['customer']) ?>"></div>
      <div class="form-group"><label>From</label><input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from']) ?>"></div>
      <div class="form-group"><label>To</label><input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to']) ?>"></div>
      <button class="btn btn-primary">Filter</button>
    </form>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No orders found.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Order</th><th>Customer</th><th>Zone</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr>
          <?php foreach ($items as $o): ?>
            <tr>
              <td>#<?= $o['id'] ?></td>
              <td><?= htmlspecialchars($o['customer_name']) ?></td>
              <td><?= htmlspecialchars($o['zone_name']) ?></td>
              <td>৳<?= number_format($o['total_amount'], 2) ?></td>
              <td><span class="badge badge-<?= $o['status'] ?>"><?= str_replace('_',' ',$o['status']) ?></span></td>
              <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
              <td><a href="<?= BASE_URL ?>/public/index.php?url=admin/orderDetail/<?= $o['id'] ?>" class="btn btn-sm btn-secondary">View</a></td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
