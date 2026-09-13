<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_seller.php'; ?>
  <div class="main-content">
    <div class="page-title"><?= htmlspecialchars($seller['shop_name']) ?></div>
    <div class="page-subtitle">Seller Dashboard &middot; Commission rate: <?= $seller['commission_rate'] ?>%</div>

    <div class="stat-grid">
      <div class="stat-card"><div class="num"><?= $productCount ?></div><div class="label">Products</div></div>
      <div class="stat-card"><div class="num">৳<?= number_format($revenue['revenue'], 0) ?></div><div class="label">Revenue (This Month)</div></div>
      <div class="stat-card"><div class="num"><?= $revenue['orders_count'] ?></div><div class="label">Orders (This Month)</div></div>
      <div class="stat-card"><div class="num"><?= count($lowStock) ?></div><div class="label">Low Stock Alerts</div></div>
    </div>

    <div class="card">
      <div class="flex-between mb-16"><h3 style="margin:0;">Pending Orders</h3><a href="<?= BASE_URL ?>/public/index.php?url=seller/orders" class="btn btn-outline btn-sm">View All Orders</a></div>
      <?php if (empty($pendingOrders)): ?>
        <div class="empty-state">No pending orders.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Order</th><th>Customer</th><th>Product</th><th>Qty</th><th>Date</th></tr>
          <?php foreach (array_slice($pendingOrders, 0, 6) as $o): ?>
            <tr><td>#<?= $o['order_id'] ?></td><td><?= htmlspecialchars($o['customer_name']) ?></td><td><?= htmlspecialchars($o['product_name']) ?></td><td><?= $o['quantity'] ?></td><td><?= date('d M', strtotime($o['order_date'])) ?></td></tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>

    <?php if (!empty($lowStock)): ?>
    <div class="card">
      <h3>Low Stock Alerts</h3>
      <div class="table-wrap"><table>
        <tr><th>Product</th><th>Stock</th><th>Reorder Level</th></tr>
        <?php foreach ($lowStock as $p): ?>
          <tr><td><?= htmlspecialchars($p['name']) ?></td><td style="color:var(--red); font-weight:700;"><?= $p['stock_qty'] ?></td><td><?= $p['reorder_level'] ?></td></tr>
        <?php endforeach; ?>
      </table></div>
    </div>
    <?php endif; ?>
  </div>
</div>
