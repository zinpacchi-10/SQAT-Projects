<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_delivery.php'; ?>
  <div class="main-content">
    <div class="page-title">Logistics Dashboard</div>
    <div class="stat-grid">
      <div class="stat-card"><div class="num"><?= $pendingDispatch ?></div><div class="label">Pending Dispatch</div></div>
      <div class="stat-card"><div class="num"><?= $activeDeliveries ?></div><div class="label">Active Deliveries</div></div>
      <div class="stat-card"><div class="num"><?= $deliveredToday ?></div><div class="label">Delivered Today</div></div>
    </div>

    <div class="card">
      <div class="flex-between mb-16"><h3 style="margin:0;">Active Deliveries</h3><a href="<?= BASE_URL ?>/public/index.php?url=delivery/active" class="btn btn-outline btn-sm">View All</a></div>
      <?php if (empty($recentActive)): ?>
        <div class="empty-state">No active deliveries right now.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Order</th><th>Customer</th><th>Zone</th><th>Agent</th><th>Status</th></tr>
          <?php foreach ($recentActive as $a): ?>
            <tr>
              <td>#<?= $a['order_id'] ?></td>
              <td><?= htmlspecialchars($a['customer_name']) ?></td>
              <td><?= htmlspecialchars($a['zone_name']) ?></td>
              <td><?= htmlspecialchars($a['agent_name']) ?></td>
              <td><span class="badge badge-<?= $a['status'] ?>"><?= str_replace('_', ' ', $a['status']) ?></span></td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
