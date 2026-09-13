<?php require APP_ROOT . '/app/views/layouts/header.php'; ?>
<div class="page container" style="max-width:440px;">
  <div class="card">
    <h2 class="page-title">Log In</h2>
    <p class="page-subtitle">Welcome back to FreshCart Marketplace.</p>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error"><ul><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul></div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_URL ?>/public/index.php?url=auth/login">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Log In</button>
    </form>
    <p class="text-muted mt-16">No account? <a href="<?= BASE_URL ?>/public/index.php?url=auth/registerCustomer" style="color:var(--green);font-weight:600;">Sign up as customer</a> or <a href="<?= BASE_URL ?>/public/index.php?url=auth/registerSeller" style="color:var(--green);font-weight:600;">register your shop</a>.</p>
    <div class="card mt-16" style="background:var(--gray-100);border:none;">
      <strong style="font-size:13px;">Demo accounts</strong> (password: <code>Password123</code>)
      <div class="text-muted" style="margin-top:6px; line-height:1.8;">
        Admin: admin@grocery.test<br>
        Delivery Manager: delivery@grocery.test<br>
        Seller: seller@grocery.test<br>
        Customer: customer@grocery.test
      </div>
    </div>
  </div>
</div>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
