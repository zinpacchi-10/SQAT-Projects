<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_customer.php'; ?>
  <div class="main-content">
    <div class="page-title">My Dashboard</div>
    <div class="page-subtitle">Welcome back, <?= htmlspecialchars($_SESSION['user']['name']) ?>.</div>

    <div class="stat-grid">
      <div class="stat-card"><div class="num"><?= $orderCount ?></div><div class="label">Total Orders</div></div>
      <div class="stat-card"><div class="num"><?= $wishlistCount ?></div><div class="label">Wishlist Items</div></div>
      <div class="stat-card"><div class="num"><?= $notifCount ?></div><div class="label">Unread Notifications</div></div>
    </div>

    <div class="card">
      <div class="flex-between mb-16"><h3 style="margin:0;">Recent Orders</h3><a href="<?= BASE_URL ?>/public/index.php?url=customer/orders" class="btn btn-outline btn-sm">View All</a></div>
      <?php if (empty($orders)): ?>
        <div class="empty-state">You haven't placed any orders yet. <a href="<?= BASE_URL ?>/public/index.php?url=customer/products">Start shopping</a>.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td>#<?= $o['id'] ?></td>
              <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
              <td>৳<?= number_format($o['total_amount'], 2) ?></td>
              <td><span class="badge badge-<?= $o['status'] ?>"><?= str_replace('_', ' ', $o['status']) ?></span></td>
              <td><a href="<?= BASE_URL ?>/public/index.php?url=customer/orderDetail/<?= $o['id'] ?>" class="btn btn-sm btn-secondary">View</a></td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
