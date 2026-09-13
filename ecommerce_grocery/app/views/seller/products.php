<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_seller.php'; ?>
  <div class="main-content">
    <div class="flex-between">
      <div class="page-title">My Products</div>
      <a href="<?= BASE_URL ?>/public/index.php?url=seller/addProduct" class="btn btn-primary">+ Add Product</a>
    </div>
    <div class="card">
      <?php if (empty($products)): ?>
        <div class="empty-state">You haven't added any products yet.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Available</th><th></th></tr>
          <?php foreach ($products as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['category_name']) ?></td>
              <td>৳<?= number_format($p['price'], 2) ?></td>
              <td>
                <form class="stock-form flex gap-8" data-id="<?= $p['id'] ?>">
                  <input type="number" name="stock_qty" value="<?= $p['stock_qty'] ?>" min="0" class="qty-input">
                  <button type="submit" class="btn btn-sm btn-secondary">Save</button>
                </form>
                <?php if ($p['stock_qty'] <= $p['reorder_level']): ?><span class="text-muted" style="color:var(--red);font-size:11px;">Low stock!</span><?php endif; ?>
              </td>
              <td><a href="<?= BASE_URL ?>/public/index.php?url=seller/toggleAvailability/<?= $p['id'] ?>" class="badge <?= $p['is_available'] ? 'badge-active' : 'badge-cancelled' ?>"><?= $p['is_available'] ? 'Available' : 'Hidden' ?></a></td>
              <td class="flex gap-8">
                <a href="<?= BASE_URL ?>/public/index.php?url=seller/editProduct/<?= $p['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                <a href="<?= BASE_URL ?>/public/index.php?url=seller/deleteProduct/<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script>
document.querySelectorAll('.stock-form').forEach(form => {
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const qty = this.querySelector('input[name=stock_qty]').value;
    ajaxPost('<?= BASE_URL ?>/public/index.php?url=seller/updateStock', { product_id: this.dataset.id, stock_qty: qty }, function (res) {
      alert(res.message);
    });
  });
});
</script>
