<div class="page container" style="max-width:900px;">
  <div class="page-title">Checkout</div>
  <form method="post" action="<?= BASE_URL ?>/public/index.php?url=customer/placeOrder">
    <div class="card">
      <h3>Delivery Address</h3>
      <?php if (!empty($addresses)): ?>
        <div class="form-group">
          <label>Choose a saved address</label>
          <select id="savedAddress">
            <option value="">-- Enter manually below --</option>
            <?php foreach ($addresses as $a): ?>
              <option value="<?= htmlspecialchars($a['full_address']) ?>"><?= htmlspecialchars($a['label']) ?>: <?= htmlspecialchars($a['full_address']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endif; ?>
      <div class="form-group">
        <label>Shipping Address</label>
        <textarea name="shipping_address" id="shipAddress" required placeholder="Full delivery address"></textarea>
      </div>
      <div class="form-group">
        <label>Delivery Zone</label>
        <select name="delivery_zone_id" id="zoneSelect" required>
          <option value="">Select zone</option>
          <?php foreach ($zones as $z): ?>
            <option value="<?= $z['id'] ?>" data-fee="<?= $z['delivery_fee'] ?>" data-days="<?= $z['estimated_days'] ?>">
              <?= htmlspecialchars($z['zone_name']) ?> — ৳<?= number_format($z['delivery_fee'], 2) ?> (<?= $z['estimated_days'] ?> day<?= $z['estimated_days'] > 1 ? 's' : '' ?>)
            </option>
          <?php endforeach; ?>
        </select>
        <div class="form-hint" id="zoneInfo"></div>
      </div>
      <div class="form-group">
        <label>Payment Method</label>
        <select name="payment_method" required>
          <option value="COD">Cash on Delivery</option>
          <option value="Card">Card</option>
        </select>
      </div>
    </div>

    <div class="card">
      <h3>Order Summary</h3>
      <div class="table-wrap"><table>
        <?php foreach ($cartItems as $ci): $p = $ci['product']; ?>
          <tr><td><?= htmlspecialchars($p['name']) ?> × <?= $ci['qty'] ?></td><td>৳<?= number_format($ci['line_total'], 2) ?></td></tr>
        <?php endforeach; ?>
      </table></div>

      <div class="form-group mt-16">
        <label>Coupon Code</label>
        <div class="flex gap-8">
          <input type="text" id="couponCode" placeholder="Enter coupon code">
          <button type="button" class="btn btn-secondary" id="applyCouponBtn">Apply</button>
        </div>
        <div id="couponMsg" class="form-hint"></div>
      </div>

      <table class="mt-16">
        <tr><td>Subtotal</td><td style="text-align:right;">৳<span id="subtotalVal"><?= number_format($subtotal, 2) ?></span></td></tr>
        <tr><td>Delivery Fee</td><td style="text-align:right;">৳<span id="feeVal">0.00</span></td></tr>
        <tr><td>Discount</td><td style="text-align:right;">-৳<span id="discountVal">0.00</span></td></tr>
        <tr style="font-weight:800; font-size:16px;"><td>Total</td><td style="text-align:right;">৳<span id="totalVal"><?= number_format($subtotal, 2) ?></span></td></tr>
      </table>

      <button type="submit" class="btn btn-primary btn-block mt-16">Place Order</button>
    </div>
  </form>
</div>

<script>
const subtotal = <?= $subtotal ?>;
let deliveryFee = 0, discount = 0;

document.getElementById('savedAddress')?.addEventListener('change', function () {
  if (this.value) document.getElementById('shipAddress').value = this.value;
});

document.getElementById('zoneSelect').addEventListener('change', function () {
  const opt = this.options[this.selectedIndex];
  deliveryFee = parseFloat(opt.dataset.fee || 0);
  document.getElementById('zoneInfo').textContent = opt.dataset.days ? 'Estimated delivery: ' + opt.dataset.days + ' day(s)' : '';
  recalc();
});

document.getElementById('applyCouponBtn').addEventListener('click', function () {
  const code = document.getElementById('couponCode').value.trim();
  const msg = document.getElementById('couponMsg');
  if (!code) { msg.textContent = 'Enter a code first.'; msg.style.color = 'var(--red)'; return; }
  ajaxPost('<?= BASE_URL ?>/public/index.php?url=customer/applyCoupon', { code: code, subtotal: subtotal }, function (res) {
    msg.textContent = res.message;
    msg.style.color = res.valid ? 'var(--green)' : 'var(--red)';
    discount = res.valid ? res.discount : 0;
    recalc();
  }, function (res) {
    msg.textContent = res.message || 'Could not validate coupon.';
    msg.style.color = 'var(--red)';
    discount = 0;
    recalc();
  });
});

function recalc() {
  document.getElementById('feeVal').textContent = deliveryFee.toFixed(2);
  document.getElementById('discountVal').textContent = discount.toFixed(2);
  document.getElementById('totalVal').textContent = Math.max(0, subtotal + deliveryFee - discount).toFixed(2);
}
</script>
