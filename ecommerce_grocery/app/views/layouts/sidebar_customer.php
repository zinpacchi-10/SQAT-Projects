<?php $cur = $_GET['url'] ?? ''; function navcls($path, $cur) { return strpos($cur, $path) === 0 ? 'active' : ''; } ?>
<aside class="sidebar">
  <h4>My Account</h4>
  <a class="<?= navcls('customer/dashboard', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=customer/dashboard">Dashboard</a>
  <a class="<?= navcls('customer/orders', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=customer/orders">My Orders</a>
  <a class="<?= navcls('customer/wishlist', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=customer/wishlist">Wishlist</a>
  <a class="<?= navcls('customer/addresses', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=customer/addresses">Addresses</a>
  <a class="<?= navcls('customer/returns', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=customer/returns">Returns</a>
  <a class="<?= navcls('customer/disputes', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=customer/disputes">Disputes</a>
  <a class="<?= navcls('customer/notifications', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=customer/notifications">Notifications</a>
  <a class="<?= navcls('customer/profile', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=customer/profile">Profile Settings</a>
</aside>
