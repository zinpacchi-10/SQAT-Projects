<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_customer.php'; ?>
  <div class="main-content">
    <div class="page-title">My Orders</div>
    <div class="card">
      <?php if (empty($orders)): ?>
        <div class="empty-state">No orders yet.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Order #</th><th>Date</th><th>Zone</th><th>Total</th><th>Status</th><th></th></tr>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td>#<?= $o['id'] ?></td>
              <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
              <td><?= htmlspecialchars($o['zone_name']) ?></td>
              <td>৳<?= number_format($o['total_amount'], 2) ?></td>
              <td><span class="badge badge-<?= $o['status'] ?>"><?= str_replace('_', ' ', $o['status']) ?></span></td>
              <td><a href="<?= BASE_URL ?>/public/index.php?url=customer/orderDetail/<?= $o['id'] ?>" class="btn btn-sm btn-secondary">Details</a></td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
