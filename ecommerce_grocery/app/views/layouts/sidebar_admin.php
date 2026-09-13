<?php $cur = $_GET['url'] ?? ''; function navcls4($path, $cur) { return strpos($cur, $path) === 0 ? 'active' : ''; } ?>
<aside class="sidebar">
  <h4>Admin Panel</h4>
  <a class="<?= navcls4('admin/dashboard', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=admin/dashboard">Dashboard</a>
  <a class="<?= navcls4('admin/sellers', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=admin/sellers">Sellers</a>
  <a class="<?= navcls4('admin/categories', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=admin/categories">Categories</a>
  <a class="<?= navcls4('admin/customers', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=admin/customers">Customers</a>
  <a class="<?= navcls4('admin/deliveryManagers', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=admin/deliveryManagers">Delivery Managers</a>
  <a class="<?= navcls4('admin/products', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=admin/products">Products</a>
  <a class="<?= navcls4('admin/orders', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=admin/orders">Orders</a>
  <a class="<?= navcls4('admin/disputes', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=admin/disputes">Disputes</a>
  <a class="<?= navcls4('admin/coupons', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=admin/coupons">Platform Coupons</a>
  <a class="<?= navcls4('admin/featured', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=admin/featured">Featured Products</a>
  <a class="<?= navcls4('admin/announcements', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=admin/announcements">Announcements</a>
  <a class="<?= navcls4('admin/reports', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=admin/reports">Reports</a>
</aside>
