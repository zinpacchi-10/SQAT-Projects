<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_seller.php'; ?>
  <div class="main-content">
    <div class="page-title">Incoming Orders</div>
    <div class="pill-nav">
      <a href="<?= BASE_URL ?>/public/index.php?url=seller/orders" class="<?= $statusFilter === '' ? 'active' : '' ?>">All</a>
      <?php foreach (['pending', 'confirmed', 'processing', 'shipped', 'delivered'] as $s): ?>
        <a href="<?= BASE_URL ?>/public/index.php?url=seller/orders&status=<?= $s ?>" class="<?= $statusFilter === $s ? 'active' : '' ?>"><?= ucfirst($s) ?></a>
      <?php endforeach; ?>
    </div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No orders found.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Order</th><th>Customer</th><th>Product</th><th>Qty</th><th>Unit Price</th><th>Status</th><th>Actions</th></tr>
          <?php foreach ($items as $it): ?>
            <tr>
              <td>#<?= $it['order_id'] ?></td>
              <td><?= htmlspecialchars($it['customer_name']) ?></td>
              <td><?= htmlspecialchars($it['product_name']) ?></td>
              <td><?= $it['quantity'] ?></td>
              <td>৳<?= number_format($it['unit_price'], 2) ?></td>
              <td><span class="badge badge-<?= $it['item_status'] ?>"><?= str_replace('_', ' ', $it['item_status']) ?></span></td>
              <td class="flex gap-8">
                <?php if ($it['item_status'] === 'pending'): ?>
                  <a href="<?= BASE_URL ?>/public/index.php?url=seller/confirmItem/<?= $it['id'] ?>" class="btn btn-sm btn-primary">Confirm</a>
                <?php elseif (in_array($it['item_status'], ['confirmed', 'processing'], true)): ?>
                  <button class="btn btn-sm btn-secondary ship-btn" data-id="<?= $it['id'] ?>">Ship</button>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/public/index.php?url=seller/orderItemDetail/<?= $it['id'] ?>" class="btn btn-sm btn-outline">Details</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script>
document.querySelectorAll('.ship-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    const note = prompt('Add a tracking note for this shipment:', 'Dispatched from warehouse');
    if (note === null) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= BASE_URL ?>/public/index.php?url=seller/shipItem';
    form.innerHTML = '<input type="hidden" name="item_id" value="' + this.dataset.id + '"><input type="hidden" name="tracking_note" value="' + note.replace(/"/g,'&quot;') + '">';
    document.body.appendChild(form);
    form.submit();
  });
});
</script>
