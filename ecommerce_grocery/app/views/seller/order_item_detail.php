<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_seller.php'; ?>
  <div class="main-content">
    <div class="page-title">Order #<?= $order['id'] ?> — Item Detail</div>
    <div class="card">
      <table>
        <tr><td class="text-muted">Customer</td><td><?= htmlspecialchars($order['customer_name']) ?></td></tr>
        <tr><td class="text-muted">Phone</td><td><?= htmlspecialchars($order['customer_phone']) ?></td></tr>
        <tr><td class="text-muted">Shipping Address</td><td><?= htmlspecialchars($order['shipping_address']) ?></td></tr>
        <tr><td class="text-muted">Payment Method</td><td><?= htmlspecialchars($order['payment_method']) ?></td></tr>
        <tr><td class="text-muted">Delivery Zone</td><td><?= htmlspecialchars($order['zone_name']) ?></td></tr>
        <tr><td class="text-muted">Quantity</td><td><?= $item['quantity'] ?></td></tr>
        <tr><td class="text-muted">Unit Price</td><td>৳<?= number_format($item['unit_price'], 2) ?></td></tr>
        <tr><td class="text-muted">Item Status</td><td><span class="badge badge-<?= $item['item_status'] ?>"><?= str_replace('_',' ',$item['item_status']) ?></span></td></tr>
        <?php if ($item['tracking_note']): ?><tr><td class="text-muted">Tracking Note</td><td><?= htmlspecialchars($item['tracking_note']) ?></td></tr><?php endif; ?>
      </table>
      <a href="<?= BASE_URL ?>/public/index.php?url=seller/orders" class="btn btn-secondary mt-16">Back to Orders</a>
    </div>
  </div>
</div>
