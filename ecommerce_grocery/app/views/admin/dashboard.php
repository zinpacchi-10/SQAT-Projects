<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="page-title">Platform Overview</div>
    <div class="stat-grid">
      <div class="stat-card"><div class="num"><?= $stats['customers'] ?></div><div class="label">Customers</div></div>
      <div class="stat-card"><div class="num"><?= $stats['sellers'] ?></div><div class="label">Approved Sellers</div></div>
      <div class="stat-card"><div class="num"><?= $stats['pendingSellers'] ?></div><div class="label">Pending Approvals</div></div>
      <div class="stat-card"><div class="num"><?= $stats['deliveryManagers'] ?></div><div class="label">Delivery Managers</div></div>
      <div class="stat-card"><div class="num"><?= $stats['ordersToday'] ?></div><div class="label">Orders Today</div></div>
      <div class="stat-card"><div class="num">৳<?= number_format($stats['revenueMonth'], 0) ?></div><div class="label">Revenue This Month</div></div>
      <div class="stat-card"><div class="num"><?= $stats['openDisputes'] ?></div><div class="label">Open Disputes</div></div>
    </div>

    <?php if ($stats['pendingSellers'] > 0): ?>
      <div class="alert alert-error"><?= $stats['pendingSellers'] ?> seller application(s) awaiting your approval. <a href="<?= BASE_URL ?>/public/index.php?url=admin/sellers&status=pending" style="font-weight:700;">Review now</a></div>
    <?php endif; ?>

    <div class="card">
      <h3>Top Sellers by Revenue</h3>
      <?php if (empty($topSellers)): ?><div class="empty-state">No sales data yet.</div><?php else: ?>
        <div class="table-wrap"><table><tr><th>Shop</th><th>Revenue</th></tr>
          <?php foreach ($topSellers as $s): ?><tr><td><?= htmlspecialchars($s['shop_name']) ?></td><td>৳<?= number_format($s['revenue'], 2) ?></td></tr><?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>

    <div class="card">
      <h3>Top Categories by Revenue</h3>
      <?php if (empty($topCategories)): ?><div class="empty-state">No sales data yet.</div><?php else: ?>
        <div class="table-wrap"><table><tr><th>Category</th><th>Revenue</th></tr>
          <?php foreach ($topCategories as $c): ?><tr><td><?= htmlspecialchars($c['name']) ?></td><td>৳<?= number_format($c['revenue'], 2) ?></td></tr><?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
