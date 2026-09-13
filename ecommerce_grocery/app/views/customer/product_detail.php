<div class="page container">
  <div class="card" style="display:flex; gap:28px; flex-wrap:wrap;">
    <div style="flex:1; min-width:280px;">
      <div class="thumb" style="height:320px; border-radius:10px;">
        <?php if ($product['primary_image_path']): ?>
          <img src="<?= UPLOAD_URL . '/' . htmlspecialchars($product['primary_image_path']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
        <?php else: ?><div style="font-size:60px;">🛒</div><?php endif; ?>
      </div>
      <?php if (!empty($images)): ?>
        <div class="img-thumb-row">
          <?php foreach ($images as $img): ?><img src="<?= UPLOAD_URL . '/' . htmlspecialchars($img['image_path']) ?>"><?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <div style="flex:1.3; min-width:300px;">
      <h2 style="margin-bottom:4px;"><?= htmlspecialchars($product['name']) ?></h2>
      <div class="text-muted mb-8">by <?= htmlspecialchars($product['shop_name']) ?> &middot; <?= htmlspecialchars($product['category_name']) ?></div>
      <?php if ($avg > 0): ?><div class="rating mb-8">★ <?= $avg ?> (<?= count($reviews) ?> reviews)</div><?php endif; ?>
      <div style="font-size:28px; font-weight:800; color:var(--green-dark); margin-bottom:10px;">৳<?= number_format($product['price'], 2) ?> <span class="text-muted" style="font-size:14px; font-weight:500;">/ <?= htmlspecialchars($product['unit']) ?></span></div>

      <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
      <table style="margin:14px 0;">
        <?php if ($product['brand']): ?><tr><td class="text-muted">Brand</td><td><?= htmlspecialchars($product['brand']) ?></td></tr><?php endif; ?>
        <tr><td class="text-muted">Stock</td><td><?= $product['stock_qty'] > 0 ? $product['stock_qty'] . ' units available' : 'Out of stock' ?></td></tr>
        <?php if ($product['expiry_date']): ?><tr><td class="text-muted">Best before</td><td><?= date('d M Y', strtotime($product['expiry_date'])) ?></td></tr><?php endif; ?>
      </table>

      <?php if ($product['stock_qty'] > 0 && $product['is_available']): ?>
        <div class="flex gap-8" style="align-items:center;">
          <input type="number" id="qtyInput" class="qty-input" value="1" min="1" max="<?= $product['stock_qty'] ?>">
          <button class="btn btn-primary" id="addToCartBtn" data-id="<?= $product['id'] ?>">Add to Cart</button>
          <button class="btn btn-outline" id="wishlistBtn" data-id="<?= $product['id'] ?>"><?= $inWishlist ? '♥ Saved' : '♡ Save' ?></button>
        </div>
        <div id="cartMsg" class="text-muted mt-8"></div>
      <?php else: ?>
        <div class="alert alert-error">Currently unavailable.</div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card mt-16">
    <h3>Customer Reviews (<?= count($reviews) ?>)</h3>
    <?php if (empty($reviews)): ?>
      <p class="text-muted">No reviews yet.</p>
    <?php else: ?>
      <?php foreach ($reviews as $r): ?>
        <div class="review-item">
          <div class="flex-between">
            <strong><?= htmlspecialchars($r['customer_name']) ?></strong>
            <span class="rating">★ <?= $r['rating'] ?></span>
          </div>
          <div class="text-muted" style="font-size:12px;"><?= date('d M Y', strtotime($r['created_at'])) ?></div>
          <p><?= nl2br(htmlspecialchars($r['review_text'])) ?></p>
          <?php if ($r['seller_reply']): ?>
            <div class="seller-reply"><strong>Seller reply:</strong> <?= nl2br(htmlspecialchars($r['seller_reply'])) ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script>
document.getElementById('addToCartBtn')?.addEventListener('click', function () {
  const qty = document.getElementById('qtyInput').value;
  ajaxPost('<?= BASE_URL ?>/public/index.php?url=customer/addToCart', { product_id: <?= $product['id'] ?>, qty: qty }, function (res) {
    document.getElementById('cartMsg').textContent = res.message;
    document.getElementById('cartMsg').style.color = 'var(--green)';
  }, function (res) {
    document.getElementById('cartMsg').textContent = res.message;
    document.getElementById('cartMsg').style.color = 'var(--red)';
  });
});
document.getElementById('wishlistBtn')?.addEventListener('click', function () {
  const btn = this;
  ajaxPost('<?= BASE_URL ?>/public/index.php?url=customer/toggleWishlist', { product_id: <?= $product['id'] ?> }, function (res) {
    btn.textContent = res.action === 'added' ? '♥ Saved' : '♡ Save';
  });
});
</script>
