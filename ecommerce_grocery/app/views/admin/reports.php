<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="page-title">Platform Reports &amp; Settings</div>

    <div class="card">
      <h3>Default Commission Rate</h3>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=admin/updateDefaultCommission" class="flex gap-8" style="align-items:flex-end;">
        <div class="form-group"><label>Default Rate (%) applied to new sellers</label><input type="number" step="0.01" name="default_commission_rate" value="<?= htmlspecialchars($defaultCommission) ?>"></div>
        <button class="btn btn-primary">Save</button>
      </form>
    </div>

    <div class="stat-grid">
      <div class="stat-card"><div class="num">৳<?= number_format($revenueMonth, 2) ?></div><div class="label">Platform Revenue This Month</div></div>
    </div>

    <div class="card">
      <h3>Top Sellers</h3>
      <div class="table-wrap"><table><tr><th>Shop</th><th>Revenue</th></tr>
        <?php foreach ($topSellers as $s): ?><tr><td><?= htmlspecialchars($s['shop_name']) ?></td><td>৳<?= number_format($s['revenue'], 2) ?></td></tr><?php endforeach; ?>
      </table></div>
    </div>

    <div class="card">
      <h3>Top Categories</h3>
      <div class="table-wrap"><table><tr><th>Category</th><th>Revenue</th></tr>
        <?php foreach ($topCategories as $c): ?><tr><td><?= htmlspecialchars($c['name']) ?></td><td>৳<?= number_format($c['revenue'], 2) ?></td></tr><?php endforeach; ?>
      </table></div>
    </div>

    <div class="card">
      <h3>Delivery Agent Performance</h3>
      <div class="table-wrap"><table>
        <tr><th>Agent</th><th>Delivered</th><th>Failed</th><th>Total</th></tr>
        <?php foreach ($agentPerformance as $ap): ?>
          <tr><td><?= htmlspecialchars($ap['name']) ?></td><td><?= $ap['delivered_count'] ?></td><td><?= $ap['failed_count'] ?></td><td><?= $ap['total_assignments'] ?></td></tr>
        <?php endforeach; ?>
      </table></div>
    </div>

    <div class="text-muted mt-16">Tip: use your browser's Print (Ctrl+P) on this page to export a PDF report.</div>
  </div>
</div>
