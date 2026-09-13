<?php require APP_ROOT . '/app/views/layouts/header.php'; ?>
<div class="page container" style="max-width:460px;">
  <div class="card">
    <h2 class="page-title">Create Your Account</h2>
    <p class="page-subtitle">Shop groceries from multiple local sellers.</p>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-error"><ul><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul></div>
    <?php endif; ?>
    <form method="post" action="<?= BASE_URL ?>/public/index.php?url=auth/registerCustomer">
      <div class="form-group"><label>Full Name</label><input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required></div>
      <div class="form-group"><label>Phone</label><input type="tel" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" required></div>
      <div class="form-group"><label>Password</label><input type="password" name="password" required><div class="form-hint">Minimum 6 characters.</div></div>
      <button type="submit" class="btn btn-primary btn-block">Create Account</button>
    </form>
    <p class="text-muted mt-16">Already have an account? <a href="<?= BASE_URL ?>/public/index.php?url=auth/login" style="color:var(--green);font-weight:600;">Log in</a></p>
  </div>
</div>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
