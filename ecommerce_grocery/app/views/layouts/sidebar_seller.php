<?php $cur = $_GET['url'] ?? ''; function navcls2($path, $cur) { return strpos($cur, $path) === 0 ? 'active' : ''; } ?>
<aside class="sidebar">
  <h4>Seller Panel</h4>
  <a class="<?= navcls2('seller/dashboard', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=seller/dashboard">Dashboard</a>
  <a class="<?= navcls2('seller/products', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=seller/products">Products</a>
  <a class="<?= navcls2('seller/orders', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=seller/orders">Orders</a>
  <a class="<?= navcls2('seller/coupons', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=seller/coupons">Coupons</a>
  <a class="<?= navcls2('seller/returns', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=seller/returns">Returns</a>
  <a class="<?= navcls2('seller/reviews', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=seller/reviews">Reviews</a>
  <a class="<?= navcls2('seller/analytics', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=seller/analytics">Analytics</a>
  <a class="<?= navcls2('seller/profile', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=seller/profile">Shop Profile</a>
</aside>
