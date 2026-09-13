<?php $navUser = $_SESSION['user'] ?? null; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FreshCart Marketplace</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css">
</head>
<body>
<nav class="navbar">
  <div class="navbar-inner">
    <a href="<?= BASE_URL ?>/public/index.php?url=home/index" class="brand">🛒 FreshCart <span class="tag">Marketplace</span></a>
    <div class="nav-links">
      <?php if (!$navUser): ?>
        <a href="<?= BASE_URL ?>/public/index.php?url=home/index">Shop</a>
        <a href="<?= BASE_URL ?>/public/index.php?url=auth/login">Log In</a>
        <a href="<?= BASE_URL ?>/public/index.php?url=auth/registerCustomer" class="btn btn-primary btn-sm">Sign Up</a>
        <a href="<?= BASE_URL ?>/public/index.php?url=auth/registerSeller" class="btn btn-outline btn-sm">Become a Seller</a>
      <?php elseif ($navUser['role'] === 'customer'): ?>
        <a href="<?= BASE_URL ?>/public/index.php?url=home/index">Shop</a>
        <a href="<?= BASE_URL ?>/public/index.php?url=customer/cart">Cart<?php if (!empty($_SESSION['cart'])): ?><span class="badge-count"><?= count($_SESSION['cart']) ?></span><?php endif; ?></a>
        <a href="<?= BASE_URL ?>/public/index.php?url=customer/dashboard">Dashboard</a>
        <span class="user-chip">👤 <?= htmlspecialchars($navUser['name']) ?></span>
        <a href="<?= BASE_URL ?>/public/index.php?url=auth/logout" class="btn btn-secondary btn-sm">Logout</a>
      <?php elseif ($navUser['role'] === 'seller'): ?>
        <a href="<?= BASE_URL ?>/public/index.php?url=seller/dashboard">Seller Dashboard</a>
        <span class="user-chip">🏪 <?= htmlspecialchars($navUser['name']) ?></span>
        <a href="<?= BASE_URL ?>/public/index.php?url=auth/logout" class="btn btn-secondary btn-sm">Logout</a>
      <?php elseif ($navUser['role'] === 'delivery_manager'): ?>
        <a href="<?= BASE_URL ?>/public/index.php?url=delivery/dashboard">Delivery Dashboard</a>
        <span class="user-chip">🚚 <?= htmlspecialchars($navUser['name']) ?></span>
        <a href="<?= BASE_URL ?>/public/index.php?url=auth/logout" class="btn btn-secondary btn-sm">Logout</a>
      <?php elseif ($navUser['role'] === 'admin'): ?>
        <a href="<?= BASE_URL ?>/public/index.php?url=admin/dashboard">Admin Dashboard</a>
        <span class="user-chip">🛡️ <?= htmlspecialchars($navUser['name']) ?></span>
        <a href="<?= BASE_URL ?>/public/index.php?url=auth/logout" class="btn btn-secondary btn-sm">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<?php if (!empty($_SESSION['flash'])): $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="container" style="padding-top:16px;">
    <div class="alert alert-<?= $f['type'] === 'success' ? 'success' : 'error' ?>"><?= htmlspecialchars($f['message']) ?></div>
  </div>
<?php endif; ?>
