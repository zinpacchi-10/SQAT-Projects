<div class="page container">
  <div class="page-title">Your Cart</div>
  <?php if (empty($cartItems)): ?>
    <div class="empty-state">Your cart is empty. <a href="<?= BASE_URL ?>/public/index.php?url=customer/products">Browse products</a>.</div>
  <?php else: ?>
    <div class="card">
      <div class="table-wrap"><table>
        <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr>
        <?php foreach ($cartItems as $ci): $p = $ci['product']; ?>
          <tr>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td>৳<?= number_format($p['price'], 2) ?></td>
            <td>
              <form method="post" action="<?= BASE_URL ?>/public/index.php?url=customer/updateCartQty" class="flex gap-8">
                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                <input type="number" name="qty" value="<?= $ci['qty'] ?>" min="0" max="<?= $p['stock_qty'] ?>" class="qty-input">
                <button class="btn btn-sm btn-secondary" type="submit">Update</button>
              </form>
            </td>
            <td>৳<?= number_format($ci['line_total'], 2) ?></td>
            <td><a class="btn btn-sm btn-danger" href="<?= BASE_URL ?>/public/index.php?url=customer/removeFromCart/<?= $p['id'] ?>">Remove</a></td>
          </tr>
        <?php endforeach; ?>
      </table></div>
      <div class="flex-between mt-16">
        <div style="font-size:18px;font-weight:800;">Subtotal: ৳<?= number_format($subtotal, 2) ?></div>
        <a class="btn btn-primary" href="<?= BASE_URL ?>/public/index.php?url=customer/checkout">Proceed to Checkout</a>
      </div>
    </div>
  <?php endif; ?>
</div>
