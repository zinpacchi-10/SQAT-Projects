<div class="page container" style="max-width:700px;">
  <div class="card text-center">
    <div style="font-size:50px;">✅</div>
    <h2>Order Placed Successfully!</h2>
    <p class="text-muted">Order ID: <strong>#<?= $order['id'] ?></strong></p>
    <p>Estimated delivery window: <strong><?= $order['estimated_days'] ?> day(s)</strong> to <?= htmlspecialchars($order['zone_name']) ?></p>
    <table class="mt-16" style="text-align:left;">
      <?php foreach ($items as $it): ?>
        <tr><td><?= htmlspecialchars($it['product_name']) ?> × <?= $it['quantity'] ?></td><td style="text-align:right;">৳<?= number_format($it['quantity'] * $it['unit_price'], 2) ?></td></tr>
      <?php endforeach; ?>
      <tr><td>Delivery Fee</td><td style="text-align:right;">৳<?= number_format($order['delivery_fee'], 2) ?></td></tr>
      <tr><td>Discount</td><td style="text-align:right;">-৳<?= number_format($order['discount_amount'], 2) ?></td></tr>
      <tr style="font-weight:800;"><td>Total</td><td style="text-align:right;">৳<?= number_format($order['total_amount'], 2) ?></td></tr>
    </table>
    <div class="mt-24">
      <a href="<?= BASE_URL ?>/public/index.php?url=customer/orderDetail/<?= $order['id'] ?>" class="btn btn-primary">Track Order</a>
      <a href="<?= BASE_URL ?>/public/index.php?url=customer/products" class="btn btn-outline">Continue Shopping</a>
    </div>
  </div>
</div>
