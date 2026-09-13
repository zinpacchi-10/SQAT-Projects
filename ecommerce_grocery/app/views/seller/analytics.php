<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_seller.php'; ?>
  <div class="main-content">
    <div class="flex-between">
      <div class="page-title">Sales Analytics</div>
      <form method="get" action="<?= BASE_URL ?>/public/index.php">
        <input type="hidden" name="url" value="seller/analytics">
        <select name="period" onchange="this.form.submit()">
          <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>Last 7 Days</option>
          <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>This Month</option>
          <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>This Year</option>
        </select>
      </form>
    </div>

    <div class="stat-grid">
      <div class="stat-card"><div class="num">৳<?= number_format($revenue['revenue'], 2) ?></div><div class="label">Total Revenue</div></div>
      <div class="stat-card"><div class="num"><?= $revenue['orders_count'] ?></div><div class="label">Orders</div></div>
      <div class="stat-card"><div class="num">৳<?= number_format($revenue['aov'], 2) ?></div><div class="label">Average Order Value</div></div>
      <div class="stat-card"><div class="num">৳<?= number_format($netPayout, 2) ?></div><div class="label">Net Payout (after <?= $commission ?>% commission)</div></div>
    </div>

    <div class="card">
      <h3>Top-Selling Products</h3>
      <?php if (empty($topProducts)): ?>
        <div class="empty-state">No sales yet.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Product</th><th>Units Sold</th><th>Revenue</th></tr>
          <?php foreach ($topProducts as $tp): ?>
            <tr><td><?= htmlspecialchars($tp['name']) ?></td><td><?= $tp['total_qty'] ?></td><td>৳<?= number_format($tp['total_revenue'], 2) ?></td></tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>

    <div class="card">
      <h3>Order Volume (Last 14 Days)</h3>
      <?php if (empty($volume)): ?>
        <div class="empty-state">No order activity in this period.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Date</th><th>Orders</th></tr>
          <?php foreach ($volume as $v): ?><tr><td><?= date('d M', strtotime($v['day'])) ?></td><td><?= $v['orders_count'] ?></td></tr><?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>

    <div class="card">
      <h3>Earnings Summary</h3>
      <table>
        <tr><td>Total Earned</td><td>৳<?= number_format($revenue['revenue'], 2) ?></td></tr>
        <tr><td>Platform Commission (<?= $commission ?>%)</td><td>-৳<?= number_format($revenue['revenue'] * $commission / 100, 2) ?></td></tr>
        <tr style="font-weight:800;"><td>Net Payout</td><td>৳<?= number_format($netPayout, 2) ?></td></tr>
      </table>
    </div>
  </div>
</div>
