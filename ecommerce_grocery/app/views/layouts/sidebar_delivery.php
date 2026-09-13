<?php $cur = $_GET['url'] ?? ''; function navcls3($path, $cur) { return strpos($cur, $path) === 0 ? 'active' : ''; } ?>
<aside class="sidebar">
  <h4>Logistics Panel</h4>
  <a class="<?= navcls3('delivery/dashboard', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=delivery/dashboard">Dashboard</a>
  <a class="<?= navcls3('delivery/agents', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=delivery/agents">Delivery Agents</a>
  <a class="<?= navcls3('delivery/zones', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=delivery/zones">Delivery Zones</a>
  <a class="<?= navcls3('delivery/dispatch', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=delivery/dispatch">Dispatch Queue</a>
  <a class="<?= navcls3('delivery/active', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=delivery/active">Active Deliveries</a>
  <a class="<?= navcls3('delivery/history', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=delivery/history">Delivery History</a>
  <a class="<?= navcls3('delivery/reports', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=delivery/reports">Reports</a>
  <a class="<?= navcls3('delivery/profile', $cur) ?>" href="<?= BASE_URL ?>/public/index.php?url=delivery/profile">Profile</a>
</aside>
