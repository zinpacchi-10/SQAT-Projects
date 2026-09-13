<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="page-title">Order #<?= $order['id'] ?></div>
    <div class="card">
      <table>
        <tr><td class="text-muted">Customer</td><td><?= htmlspecialchars($order['customer_name']) ?> (<?= htmlspecialchars($order['customer_phone']) ?>)</td></tr>
        <tr><td class="text-muted">Address</td><td><?= htmlspecialchars($order['shipping_address']) ?></td></tr>
        <tr><td class="text-muted">Zone</td><td><?= htmlspecialchars($order['zone_name']) ?></td></tr>
        <tr><td class="text-muted">Payment</td><td><?= htmlspecialchars($order['payment_method']) ?></td></tr>
        <tr><td class="text-muted">Status</td><td><span class="badge badge-<?= $order['status'] ?>"><?= str_replace('_',' ',$order['status']) ?></span></td></tr>
        <tr><td class="text-muted">Placed</td><td><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></td></tr>
      </table>
    </div>
    <div class="card">
      <h3>Items</h3>
      <div class="table-wrap"><table>
        <tr><th>Product</th><th>Seller</th><th>Qty</th><th>Unit Price</th><th>Status</th></tr>
        <?php foreach ($items as $it): ?>
          <tr>
            <td><?= htmlspecialchars($it['product_name']) ?></td>
            <td><?= htmlspecialchars($it['shop_name']) ?></td>
            <td><?= $it['quantity'] ?></td>
            <td>৳<?= number_format($it['unit_price'], 2) ?></td>
            <td><span class="badge badge-<?= $it['item_status'] ?>"><?= str_replace('_',' ',$it['item_status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </table></div>
      <table class="mt-16">
        <tr><td>Subtotal</td><td>৳<?= number_format($order['subtotal'], 2) ?></td></tr>
        <tr><td>Delivery Fee</td><td>৳<?= number_format($order['delivery_fee'], 2) ?></td></tr>
        <tr><td>Discount</td><td>-৳<?= number_format($order['discount_amount'], 2) ?></td></tr>
        <tr style="font-weight:800;"><td>Total</td><td>৳<?= number_format($order['total_amount'], 2) ?></td></tr>
      </table>
    </div>
  </div>
</div>
