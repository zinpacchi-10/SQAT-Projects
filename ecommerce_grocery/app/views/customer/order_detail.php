<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_customer.php'; ?>
  <div class="main-content">
    <div class="flex-between">
      <div class="page-title">Order #<?= $order['id'] ?></div>
      <span class="badge badge-<?= $order['status'] ?>" id="statusBadge"><?= str_replace('_', ' ', $order['status']) ?></span>
    </div>
    <p class="text-muted">Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?> &middot; Delivery Zone: <?= htmlspecialchars($order['zone_name']) ?></p>

    <div class="card">
      <h3>Items</h3>
      <div class="table-wrap"><table>
        <tr><th>Product</th><th>Seller</th><th>Qty</th><th>Unit Price</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($items as $it): ?>
          <tr>
            <td><?= htmlspecialchars($it['product_name']) ?></td>
            <td><?= htmlspecialchars($it['shop_name']) ?></td>
            <td><?= $it['quantity'] ?></td>
            <td>৳<?= number_format($it['unit_price'], 2) ?></td>
            <td><span class="badge badge-<?= $it['item_status'] ?>"><?= str_replace('_', ' ', $it['item_status']) ?></span></td>
            <td>
              <?php if ($it['item_status'] === 'delivered'): ?>
                <button class="btn btn-sm btn-outline" onclick="document.getElementById('reviewForm<?= $it['id'] ?>').style.display='block'">Review</button>
                <button class="btn btn-sm btn-secondary" onclick="document.getElementById('returnForm<?= $it['id'] ?>').style.display='block'">Return</button>
              <?php endif; ?>
            </td>
          </tr>
          <?php if ($it['item_status'] === 'delivered'): ?>
          <tr id="reviewForm<?= $it['id'] ?>" style="display:none;">
            <td colspan="6">
              <form method="post" action="<?= BASE_URL ?>/public/index.php?url=customer/addReview" class="form-row" style="align-items:flex-end;">
                <input type="hidden" name="product_id" value="<?= $it['product_id'] ?>">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <div class="form-group"><label>Rating</label>
                  <select name="rating"><option value="5">5 - Excellent</option><option value="4">4 - Good</option><option value="3">3 - Average</option><option value="2">2 - Poor</option><option value="1">1 - Bad</option></select>
                </div>
                <div class="form-group" style="flex:2;"><label>Review</label><textarea name="review_text" placeholder="Share your experience..."></textarea></div>
                <button class="btn btn-primary btn-sm">Submit Review</button>
              </form>
            </td>
          </tr>
          <tr id="returnForm<?= $it['id'] ?>" style="display:none;">
            <td colspan="6">
              <form method="post" action="<?= BASE_URL ?>/public/index.php?url=customer/requestReturn" class="form-row" style="align-items:flex-end;">
                <input type="hidden" name="order_item_id" value="<?= $it['id'] ?>">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <div class="form-group" style="flex:2;"><label>Reason for Return</label><input type="text" name="reason" placeholder="e.g. damaged item, wrong product" required></div>
                <button class="btn btn-secondary btn-sm">Submit Return Request</button>
              </form>
            </td>
          </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      </table></div>
    </div>

    <div class="card">
      <h3>Order Summary</h3>
      <table>
        <tr><td>Delivery Address</td><td><?= htmlspecialchars($order['shipping_address']) ?></td></tr>
        <tr><td>Payment Method</td><td><?= htmlspecialchars($order['payment_method']) ?></td></tr>
        <tr><td>Subtotal</td><td>৳<?= number_format($order['subtotal'], 2) ?></td></tr>
        <tr><td>Delivery Fee</td><td>৳<?= number_format($order['delivery_fee'], 2) ?></td></tr>
        <tr><td>Discount</td><td>-৳<?= number_format($order['discount_amount'], 2) ?></td></tr>
        <tr style="font-weight:800;"><td>Total</td><td>৳<?= number_format($order['total_amount'], 2) ?></td></tr>
      </table>
      <div class="mt-16 flex gap-8">
        <?php if ($canCancel): ?>
          <a href="<?= BASE_URL ?>/public/index.php?url=customer/cancelOrder/<?= $order['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this order?')">Cancel Order</a>
        <?php endif; ?>
        <form method="post" action="<?= BASE_URL ?>/public/index.php?url=customer/fileDispute" style="display:inline;">
          <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
          <input type="hidden" name="description" value="Issue with order #<?= $order['id'] ?>">
          <button class="btn btn-outline btn-sm" onclick="return confirm('Escalate an issue with this order to admin?')">Report an Issue</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// AJAX polling: keep the status badge live without a page reload
function pollStatus() {
  ajaxGet('<?= BASE_URL ?>/public/index.php?url=customer/orderStatus/<?= $order['id'] ?>', function (res) {
    if (res.success) {
      const badge = document.getElementById('statusBadge');
      badge.textContent = res.status.replace('_', ' ');
      badge.className = 'badge badge-' + res.status;
    }
  });
}
setInterval(pollStatus, 8000);
</script>
